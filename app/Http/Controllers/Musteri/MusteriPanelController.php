<?php

namespace App\Http\Controllers\Musteri;

use App\Enums\RandevuDurumu;
use App\Enums\RandevuKatilimDurumu;
use App\Enums\RandevuSlotDurumu;
use App\Enums\RandevuSlotTipi;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Randevu;
use App\Models\RandevuSlot;
use App\Models\User;
use App\Services\AileHekimiOneriService;
use App\Services\DoctorRandevuSlotGenerator;
use App\Support\GeoDistance;
use App\Support\MusteriAccess;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MusteriPanelController extends Controller
{
    private const BOOKING_DAYS_AHEAD = 30;

    public function index(): View
    {
        $baseQuery = Randevu::query()
            ->where('user_id', MusteriAccess::user()->getAuthIdentifier())
            ->with(['doctor.user', 'doctor.department', 'doctor.hospital', 'slot']);

        $yaklasanSirali = (clone $baseQuery)
            ->whereIn('durum', [RandevuDurumu::Bekliyor, RandevuDurumu::Onaylandi])
            ->whereHas('slot', function ($q) {
                $q->where('baslangic', '>=', now());
            })
            ->get()
            ->sortBy(function ($r) {
                return $r->slot?->baslangic?->getTimestamp() ?? PHP_INT_MAX;
            })
            ->values();

        $yaklasanRandevularAcik = $yaklasanSirali->filter(fn (Randevu $r) => ! $r->gizli)->take(5)->values();
        $yaklasanRandevularGizli = $yaklasanSirali->filter(fn (Randevu $r) => $r->gizli)->take(5)->values();

        $sonRandevular = (clone $baseQuery)
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        $patientUser = MusteriAccess::user()->fresh();
        $favNorm = $patientUser->patientFavoritesNormalized();
        $favoriteHospitalsPreview = collect();
        if ($favNorm['hospitals'] !== []) {
            $favoriteHospitalsPreview = Hospital::query()
                ->whereIn('id', $favNorm['hospitals'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'city']);
        }
        $favoriteClinicsPreview = collect();
        if ($favNorm['clinics'] !== []) {
            $deptIds = collect($favNorm['clinics'])->pluck('department_id')->unique()->all();
            $departmentsById = Department::query()->whereIn('id', $deptIds)->get()->keyBy('id');
            $hospitalIds = collect($favNorm['clinics'])->pluck('hospital_id')->unique()->all();
            $hospitalsById = Hospital::query()->whereIn('id', $hospitalIds)->get()->keyBy('id');
            foreach ($favNorm['clinics'] as $c) {
                $h = $hospitalsById->get($c['hospital_id']);
                $d = $departmentsById->get($c['department_id']);
                if ($h && $h->is_active && $d && $d->is_active) {
                    $favoriteClinicsPreview->push([
                        'hospital' => $h,
                        'department' => $d,
                    ]);
                }
            }
        }

        return view('musteri.panel', compact(
            'yaklasanRandevularAcik',
            'yaklasanRandevularGizli',
            'sonRandevular',
            'favoriteHospitalsPreview',
            'favoriteClinicsPreview'
        ));
    }

    public function notificationsJson(Request $request): JsonResponse
    {
        $user = MusteriAccess::user($request);
        if (! $user) {
            return response()->json([
                'unreadCount' => 0,
                'notifications' => [],
            ]);
        }

        $items = $user->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? 'Bildirim',
                    'message' => $n->data['message'] ?? $n->type,
                    'kind' => $n->data['kind'] ?? null,
                    'randevu_id' => isset($n->data['randevu_id']) ? (int) $n->data['randevu_id'] : null,
                    'read_at' => $n->read_at?->toIso8601String(),
                    'created_human' => $n->created_at?->diffForHumans(),
                ];
            })
            ->values();

        return response()->json([
            'unreadCount' => $user->unreadNotifications()->count(),
            'notifications' => $items,
        ]);
    }

    public function bildirimler(): View
    {
        $user = MusteriAccess::user();

        $notifications = $user?->notifications()->latest()->paginate(20);

        return view('musteri.bildirimler', [
            'notifications' => $notifications,
        ]);
    }

    public function randevuAlForm(Request $request): View|RedirectResponse
    {
        $patient = MusteriAccess::user()->fresh();
        $proxyHastaId = null;
        $randevuHedefHasta = $patient;

        if ($request->filled('hasta_id')) {
            $hid = $request->integer('hasta_id');
            if ($hid !== (int) $patient->id) {
                $other = User::query()->whereKey($hid)->first();
                if (! $other || ! $other->isPatient()
                    || ! $this->vekilRandevuBaskasiAdinaYetkisiVar($patient, $hid)) {
                    return redirect()
                        ->route('musteri.randevu.al')
                        ->with('error', 'Geçersiz veya yetkisiz hasta seçimi.');
                }
                $randevuHedefHasta = $other;
                $proxyHastaId = $hid;
            }
        }

        $oncelikliHasta = $randevuHedefHasta->isOncelikliHasta();
        $gizliRandevuModu = $request->boolean('gizli_randevu');

        $aileHekimiOdak = $request->boolean('aile_hekimi');
        $aileHekimiDoctor = null;

        if ($aileHekimiOdak) {
            $randevuHedefHasta->loadMissing(['aileHekimi.hospital', 'aileHekimi.user', 'aileHekimi.department']);
            $fam = $randevuHedefHasta->aileHekimi;
            if (! $fam || ! $fam->is_active || ! $fam->is_aile_hekimi
                || ! $fam->hospital_id || ! $fam->department_id) {
                $ahRoute = $proxyHastaId
                    ? route('musteri.yetkili-olduklarim')
                    : route('musteri.aile-hekimi');

                return redirect()->to($ahRoute)
                    ->with('error', 'Seçilen hasta için kayıtlı aile hekimi bulunmuyor veya randevu alınamaz durumda.');
            }
            if (! $fam->hospital || ! $fam->hospital->is_active) {
                $ahRoute = $proxyHastaId
                    ? route('musteri.yetkili-olduklarim')
                    : route('musteri.aile-hekimi');

                return redirect()->to($ahRoute)
                    ->with('error', 'Aile hekiminin bağlı olduğu kurum aktif değil.');
            }
            $aileHekimiDoctor = $fam;
            $request->merge([
                'hospital_id' => (string) $fam->hospital_id,
                'department_id' => (string) $fam->department_id,
                'doctor_id' => (string) $fam->id,
                'city' => (string) ($fam->hospital->city ?? ''),
                'district' => '',
                'hospital_q' => '',
                'department_q' => '',
            ]);
        }

        $vekaletRandevuHastalar = $patient->proxyPatients()->orderByPivot('created_at', 'desc')->get();
        $kendimRandevuUrl = route('musteri.randevu.al', array_filter([
            'aile_hekimi' => $aileHekimiOdak ? 1 : null,
            'gizli_randevu' => $gizliRandevuModu ? 1 : null,
        ]));

        $hospitalId = $request->integer('hospital_id') ?: null;
        $kurumTipi = $request->string('kurum_tipi')->trim()->value() ?: null;
        if (! in_array($kurumTipi, ['hastane', 'saglik_merkezi'], true)) {
            $kurumTipi = null;
        }
        $city = $request->string('city')->trim()->value() ?: null;
        $district = $request->string('district')->trim()->value() ?: null;
        $hospitalQ = $request->string('hospital_q')->trim()->value() ?: null;
        $departmentQ = $request->string('department_q')->trim()->value() ?: null;

        $nearLat = null;
        $nearLng = null;
        if (! $aileHekimiOdak && $request->filled('near_lat') && $request->filled('near_lng')) {
            $lat = filter_var($request->input('near_lat'), FILTER_VALIDATE_FLOAT);
            $lng = filter_var($request->input('near_lng'), FILTER_VALIDATE_FLOAT);
            if ($lat !== false && $lng !== false
                && $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                $nearLat = (float) $lat;
                $nearLng = (float) $lng;
            }
        }
        $nearMeActive = ! $aileHekimiOdak && $nearLat !== null && $nearLng !== null;

        if ($hospitalId) {
            $resolvedHospitalQuery = Hospital::query()
                ->whereKey($hospitalId)
                ->where('is_active', true);
            if ($kurumTipi === 'hastane') {
                $resolvedHospitalQuery->where('is_saglik_merkezi', false);
            } elseif ($kurumTipi === 'saglik_merkezi') {
                $resolvedHospitalQuery->where('is_saglik_merkezi', true);
            }
            $resolvedHospital = $resolvedHospitalQuery->first();
            if ($resolvedHospital) {
                $city = $resolvedHospital->city;
                $hDistricts = $resolvedHospital->districts ?? [];
                if ($district === null || $district === '' || ! in_array($district, $hDistricts, true)) {
                    $district = $hDistricts[0] ?? null;
                }
            } else {
                $hospitalId = null;
            }
        }

        $citiesQuery = Hospital::query()
            ->where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '');
        if ($kurumTipi === 'hastane') {
            $citiesQuery->where('is_saglik_merkezi', false);
        } elseif ($kurumTipi === 'saglik_merkezi') {
            $citiesQuery->where('is_saglik_merkezi', true);
        }
        $cities = $citiesQuery->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->values();

        $districts = collect();
        if ($city) {
            $districtsQuery = Hospital::query()
                ->where('is_active', true)
                ->where('city', $city);
            if ($kurumTipi === 'hastane') {
                $districtsQuery->where('is_saglik_merkezi', false);
            } elseif ($kurumTipi === 'saglik_merkezi') {
                $districtsQuery->where('is_saglik_merkezi', true);
            }
            $districts = $districtsQuery->get()
                ->flatMap(fn (Hospital $h) => collect($h->districts ?? []))
                ->map(fn ($d) => trim((string) $d))
                ->filter()
                ->unique()
                ->sort()
                ->values();
        }

        $hospitals = collect();
        /** @var array<int, float> */
        $hospitalDistanceKm = [];

        $listHospitals = ! $aileHekimiOdak && ($city !== null || $nearMeActive);
        if ($listHospitals) {
            $hospitalsQuery = Hospital::query()->where('is_active', true);
            if ($kurumTipi === 'hastane') {
                $hospitalsQuery->where('is_saglik_merkezi', false);
            } elseif ($kurumTipi === 'saglik_merkezi') {
                $hospitalsQuery->where('is_saglik_merkezi', true);
            }

            if ($city) {
                $hospitalsQuery->where('city', $city);
                if ($district) {
                    $hospitalsQuery->where(function ($w) use ($district) {
                        $w->whereJsonContains('districts', $district)
                            ->orWhereNull('districts')
                            ->orWhereJsonLength('districts', 0);
                    });
                }
            }

            if ($hospitalQ !== null && $hospitalQ !== '') {
                $term = '%'.addcslashes($hospitalQ, '%_\\').'%';
                $hospitalsQuery->where(function ($w) use ($term) {
                    $w->where('name', 'like', $term)
                        ->orWhere('address', 'like', $term);
                });
            }

            if ($nearMeActive) {
                $all = $hospitalsQuery->get();
                $withGeo = [];
                foreach ($all as $h) {
                    if ($h->latitude !== null && $h->longitude !== null
                        && $h->latitude !== '' && $h->longitude !== '') {
                        $km = GeoDistance::haversineKm(
                            $nearLat,
                            $nearLng,
                            (float) $h->latitude,
                            (float) $h->longitude
                        );
                        $withGeo[] = ['h' => $h, 'km' => $km];
                    }
                }
                usort($withGeo, fn (array $a, array $b): int => $a['km'] <=> $b['km']);
                $noGeo = $all
                    ->filter(fn (Hospital $h) => $h->latitude === null || $h->longitude === null
                        || $h->latitude === '' || $h->longitude === '')
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values();
                $hospitals = collect(array_column($withGeo, 'h'))->merge($noGeo);
                foreach ($withGeo as $row) {
                    $hospitalDistanceKm[(int) $row['h']->id] = round($row['km'], 1);
                }
            } else {
                $hospitals = $hospitalsQuery->orderBy('name')->get();
            }
        }

        $departments = collect();
        if ($hospitalId) {
            $departmentsQuery = Department::query()
                ->where('is_active', true)
                ->whereHas('doctors', function ($q) use ($hospitalId) {
                    $q->where('hospital_id', $hospitalId)->where('is_active', true);
                });

            if ($departmentQ !== null && $departmentQ !== '') {
                $dterm = '%'.addcslashes($departmentQ, '%_\\').'%';
                $departmentsQuery->where('name', 'like', $dterm);
            }

            $departments = $departmentsQuery
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        $departmentId = $request->integer('department_id') ?: null;
        $doctorId = $request->integer('doctor_id') ?: null;

        if (! $hospitalId) {
            $departmentId = null;
            $doctorId = null;
        } else {
            $allowedDepartmentIds = $departments->pluck('id');
            if ($departmentId && ! $allowedDepartmentIds->contains($departmentId)) {
                $departmentId = null;
                $doctorId = null;
            }
        }

        if ($doctorId && $hospitalId && $departmentId) {
            $doctorOk = Doctor::query()
                ->whereKey($doctorId)
                ->where('hospital_id', $hospitalId)
                ->where('department_id', $departmentId)
                ->where('is_active', true)
                ->exists();
            if (! $doctorOk) {
                $doctorId = null;
            }
        }

        $selectedDateStr = $request->string('randevu_date') ?: null;

        $selectedDate = null;
        if ($selectedDateStr) {
            try {
                $selectedDate = Carbon::parse($selectedDateStr)->startOfDay();
            } catch (\Throwable $e) {
                $selectedDate = null;
            }
        }

        $doctors = collect();
        if ($departmentId && $hospitalId) {
            $doctors = Doctor::query()
                ->where('department_id', $departmentId)
                ->where('hospital_id', $hospitalId)
                ->where('is_active', true)
                ->with(['user', 'department'])
                ->orderBy('title')
                ->get();

            if ($doctors->isNotEmpty()) {
                $generator = app(DoctorRandevuSlotGenerator::class);
                foreach ($doctors as $doc) {
                    $generator->ensureSlotsForDoctor((int) $doc->id, self::BOOKING_DAYS_AHEAD);
                }

                $slotWindowEnd = now()->addDays(self::BOOKING_DAYS_AHEAD)->endOfDay();
                $nextSlotByDoctor = RandevuSlot::query()
                    ->selectRaw('doctor_id, MIN(baslangic) as next_baslangic')
                    ->whereIn('doctor_id', $doctors->pluck('id'))
                    ->where('durum', RandevuSlotDurumu::Musait)
                    ->where('baslangic', '>', now())
                    ->whereBetween('baslangic', [now(), $slotWindowEnd])
                    ->visibleForPatient($oncelikliHasta)
                    ->whereDoesntHave('randevu', function ($q) {
                        $q->where('durum', '!=', RandevuDurumu::Iptal);
                    })
                    ->groupBy('doctor_id')
                    ->pluck('next_baslangic', 'doctor_id');

                $doctors = $doctors
                    ->sort(function (Doctor $a, Doctor $b) use ($nextSlotByDoctor) {
                        $ta = $nextSlotByDoctor[$a->id] ?? null;
                        $tb = $nextSlotByDoctor[$b->id] ?? null;
                        if ($ta === null && $tb === null) {
                            return strcmp((string) $a->title, (string) $b->title);
                        }
                        if ($ta === null) {
                            return 1;
                        }
                        if ($tb === null) {
                            return -1;
                        }

                        return Carbon::parse($ta)->getTimestamp() <=> Carbon::parse($tb)->getTimestamp();
                    })
                    ->values();
            }
        }

        $slots = collect();
        $availableDates = collect();
        $doctorHasWorkingHours = false;
        if ($doctorId) {
            app(DoctorRandevuSlotGenerator::class)->ensureSlotsForDoctor((int) $doctorId, self::BOOKING_DAYS_AHEAD);

            $doctorHasWorkingHours = Doctor::query()
                ->whereKey($doctorId)
                ->whereHas('hospital.departmentWorkingHours', function ($dq) {
                    $dq->whereColumn(
                        'hospital_department_working_hours.department_id',
                        'doctors.department_id'
                    );
                })
                ->exists();

            $slotWindowEnd = now()->addDays(self::BOOKING_DAYS_AHEAD)->endOfDay();
            $baseSlots = RandevuSlot::query()
                ->where('doctor_id', $doctorId)
                ->where('durum', RandevuSlotDurumu::Musait)
                ->where('baslangic', '>', now())
                ->whereBetween('baslangic', [now(), $slotWindowEnd])
                ->visibleForPatient($oncelikliHasta)
                ->whereDoesntHave('randevu', function ($q) {
                    $q->where('durum', '!=', RandevuDurumu::Iptal);
                })
                ->orderBy('baslangic')
                ->orderBy('id')
                ->get();

            $availableDates = $baseSlots
                ->groupBy(fn ($s) => $s->baslangic->toDateString())
                ->keys()
                ->sort()
                ->values();

            if ($selectedDate) {
                $slots = $baseSlots
                    ->filter(fn ($s) => $s->baslangic->isSameDay($selectedDate))
                    ->sort(function (RandevuSlot $a, RandevuSlot $b) {
                        $ta = $a->baslangic->getTimestamp();
                        $tb = $b->baslangic->getTimestamp();
                        if ($ta !== $tb) {
                            return $ta <=> $tb;
                        }

                        return $a->id <=> $b->id;
                    })
                    ->values();
            }
        }

        $patientForFav = $patient;
        $favoriteHospitalIds = $patientForFav->patientFavoritesNormalized()['hospitals'];
        $favoriteClinicPairs = $patientForFav->patientFavoritesNormalized()['clinics'];

        return view('musteri.randevu-al', compact(
            'hospitalId',
            'kurumTipi',
            'city',
            'district',
            'hospitalQ',
            'departmentQ',
            'cities',
            'districts',
            'hospitals',
            'departments',
            'departmentId',
            'doctorId',
            'doctors',
            'slots',
            'selectedDate',
            'availableDates',
            'doctorHasWorkingHours',
            'oncelikliHasta',
            'gizliRandevuModu',
            'favoriteHospitalIds',
            'favoriteClinicPairs',
            'nearLat',
            'nearLng',
            'nearMeActive',
            'hospitalDistanceKm',
            'aileHekimiOdak',
            'aileHekimiDoctor',
            'proxyHastaId',
            'randevuHedefHasta',
            'vekaletRandevuHastalar',
            'kendimRandevuUrl',
        ) + [
            'bookingDaysAhead' => self::BOOKING_DAYS_AHEAD,
            'oncelikliYasEsigi' => User::ONCELIKLI_YAS_ESIGI,
            'randevuAlStickyParams' => array_filter([
                'aile_hekimi' => $aileHekimiOdak ? 1 : null,
                'hasta_id' => $proxyHastaId,
                'kurum_tipi' => $kurumTipi,
            ], fn ($v) => $v !== null && $v !== ''),
        ]);
    }

    public function favoriler(): View
    {
        $user = MusteriAccess::user()->fresh();
        $norm = $user->patientFavoritesNormalized();

        $favoriteHospitals = collect();
        if ($norm['hospitals'] !== []) {
            $favoriteHospitals = Hospital::query()
                ->whereIn('id', $norm['hospitals'])
                ->orderBy('name')
                ->get();
        }

        $favoriteClinics = collect();
        if ($norm['clinics'] !== []) {
            $deptIds = collect($norm['clinics'])->pluck('department_id')->unique()->all();
            $departmentsById = Department::query()->whereIn('id', $deptIds)->get()->keyBy('id');
            $hospitalIds = collect($norm['clinics'])->pluck('hospital_id')->unique()->all();
            $hospitalsById = Hospital::query()->whereIn('id', $hospitalIds)->get()->keyBy('id');
            foreach ($norm['clinics'] as $c) {
                $h = $hospitalsById->get($c['hospital_id']);
                $d = $departmentsById->get($c['department_id']);
                if ($h && $d) {
                    $favoriteClinics->push([
                        'hospital' => $h,
                        'department' => $d,
                    ]);
                }
            }
        }

        return view('musteri.favoriler', compact('favoriteHospitals', 'favoriteClinics'));
    }

    public function favoriHastaneToggle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hospital_id' => ['required', 'integer', 'exists:hospitals,id'],
        ]);

        $hospital = Hospital::query()->whereKey($validated['hospital_id'])->firstOrFail();
        $user = MusteriAccess::user()->fresh();
        $hid = (int) $hospital->id;

        if ($user->isFavoriteHospital($hid)) {
            $user->toggleFavoriteHospital($hid);

            return redirect()
                ->back(fallback: route('musteri.favoriler'))
                ->with('success', 'Hastane favorilerden çıkarıldı.');
        }

        if (! $hospital->is_active) {
            return redirect()
                ->back(fallback: route('musteri.favoriler'))
                ->with('error', 'Pasif hastaneler favorilere eklenemez.');
        }

        $user->toggleFavoriteHospital($hid);

        return redirect()
            ->back(fallback: route('musteri.favoriler'))
            ->with('success', 'Hastane favorilere eklendi.');
    }

    public function favoriPoliklinikToggle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hospital_id' => ['required', 'integer', 'exists:hospitals,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ]);

        $hospital = Hospital::query()->whereKey($validated['hospital_id'])->firstOrFail();
        $department = Department::query()->whereKey($validated['department_id'])->firstOrFail();
        $user = MusteriAccess::user()->fresh();
        $hid = (int) $hospital->id;
        $did = (int) $department->id;

        if ($user->isFavoriteClinic($hid, $did)) {
            $user->toggleFavoriteClinic($hid, $did);

            return redirect()
                ->back(fallback: route('musteri.favoriler'))
                ->with('success', 'Poliklinik favorilerden çıkarıldı.');
        }

        if (! $hospital->is_active || ! $department->is_active) {
            return redirect()
                ->back(fallback: route('musteri.randevu.al'))
                ->with('error', 'Pasif kayıtlar favorilere eklenemez.');
        }

        $validCombo = Doctor::query()
            ->where('hospital_id', $hid)
            ->where('department_id', $did)
            ->where('is_active', true)
            ->exists();

        if (! $validCombo) {
            return redirect()
                ->back(fallback: route('musteri.randevu.al'))
                ->with('error', 'Bu hastane ve birim için aktif doktor yok; favori eklenemedi.');
        }

        $user->toggleFavoriteClinic($hid, $did);

        return redirect()
            ->back(fallback: route('musteri.favoriler'))
            ->with('success', 'Poliklinik favorilere eklendi.');
    }

    public function aileHekimi(): View
    {
        $user = MusteriAccess::user()->fresh();
        $user->load(['aileHekimi.user', 'aileHekimi.hospital', 'aileHekimi.department']);

        $cities = Hospital::query()
            ->where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->values();

        return view('musteri.aile-hekimi', [
            'user' => $user,
            'cities' => $cities,
        ]);
    }

    public function aileHekimiIlceler(Request $request): JsonResponse
    {
        $city = $request->string('city')->trim()->value();
        if ($city === '') {
            return response()->json(['districts' => []]);
        }

        $exists = Hospital::query()->where('is_active', true)->where('city', $city)->exists();
        if (! $exists) {
            return response()->json(['message' => 'Geçersiz il seçimi.'], 422);
        }

        $districts = Hospital::query()
            ->where('is_active', true)
            ->where('city', $city)
            ->get()
            ->flatMap(fn (Hospital $h) => collect($h->districts ?? []))
            ->map(fn ($d) => trim((string) $d))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return response()->json(['districts' => $districts->all()]);
    }

    public function aileHekimiOneriJson(Request $request, AileHekimiOneriService $aileHekimiOneriService): JsonResponse
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
        ]);

        $city = trim($validated['city']);
        $district = isset($validated['district']) ? trim((string) $validated['district']) : '';

        $exists = Hospital::query()->where('is_active', true)->where('city', $city)->exists();
        if (! $exists) {
            return response()->json(['message' => 'Geçersiz il.'], 422);
        }

        $doctors = $aileHekimiOneriService->listNearest($city, $district !== '' ? $district : null);

        return response()->json(['doctors' => $doctors]);
    }

    public function aileHekimiKaydet(Request $request, AileHekimiOneriService $aileHekimiOneriService): RedirectResponse
    {
        $data = $this->validatedPatientAileHekimi($request, $aileHekimiOneriService);

        $request->user()->update([
            'patient_city' => $data['patient_city'],
            'patient_district' => $data['patient_district'],
            'aile_hekimi_doctor_id' => $data['aile_hekimi_doctor_id'],
        ]);

        return redirect()
            ->route('musteri.aile-hekimi')
            ->with('success', 'Aile hekimi seçiminiz kaydedildi.');
    }

    public function aileHekimiKaldir(Request $request): RedirectResponse
    {
        $request->user()->update([
            'patient_city' => null,
            'patient_district' => null,
            'aile_hekimi_doctor_id' => null,
        ]);

        return redirect()
            ->route('musteri.aile-hekimi')
            ->with('success', 'Aile hekimi seçiminiz kaldırıldı.');
    }

    public function profil(): View
    {
        return view('musteri.profil', [
            'user' => MusteriAccess::user()->fresh(),
        ]);
    }

    public function profilGuncelle(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:E,K,D'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->gender = $validated['gender'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('musteri.profil')
            ->with('success', 'Profiliniz güncellendi.');
    }

    public function gecmisRandevular(Request $request): View|RedirectResponse
    {
        $self = MusteriAccess::user()->fresh();
        $gecmisGorunenHasta = $self;
        if ($request->filled('hasta_id')) {
            $hid = $request->integer('hasta_id');
            if ($hid !== (int) $self->id) {
                if (! $this->vekilRandevuBaskasiAdinaYetkisiVar($self, $hid)) {
                    return redirect()
                        ->route('musteri.randevu.gecmis')
                        ->with('error', 'Bu hastanın geçmiş randevularını görüntüleme yetkiniz yok.');
                }
                $gecmisGorunenHasta = User::query()->whereKey($hid)->firstOrFail();
            }
        }

        $gecmisVekilModu = (int) $gecmisGorunenHasta->id !== (int) $self->id;

        $gecmisRandevular = Randevu::query()
            ->where('user_id', $gecmisGorunenHasta->id)
            ->with(['doctor.user', 'doctor.department', 'doctor.hospital', 'slot'])
            ->where(function ($q) {
                $q->whereIn('durum', [
                    RandevuDurumu::Tamamlandi,
                    RandevuDurumu::Iptal,
                    RandevuDurumu::Gelmedi,
                ])->orWhereHas('slot', function ($sq) {
                    $sq->where('baslangic', '<', now());
                });
            })
            ->get()
            ->sortByDesc(function ($r) {
                return $r->slot?->baslangic?->getTimestamp() ?? $r->created_at?->getTimestamp() ?? 0;
            })
            ->values();

        $gecmisRandevularGizli = $gecmisRandevular->filter(fn (Randevu $r) => $r->gizli)->values();
        $gecmisRandevularAcik = $gecmisRandevular->filter(fn (Randevu $r) => ! $r->gizli)->values();

        return view('musteri.gecmis-randevular', compact(
            'gecmisRandevularGizli',
            'gecmisRandevularAcik',
            'gecmisGorunenHasta',
            'gecmisVekilModu'
        ));
    }

    public function randevuKaydet(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'randevu_slot_id' => ['required', 'integer', 'exists:randevu_slotlari,id'],
            'sikayet' => ['nullable', 'string', 'max:2000'],
            'gizli' => ['sometimes', 'boolean'],
            'hasta_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $patient = $request->user()->fresh();
        $target = $this->randevuHedefPatientFromRequest($patient, $validated['hasta_id'] ?? null);
        $gizliRandevu = $request->boolean('gizli');

        DB::transaction(function () use ($validated, $patient, $gizliRandevu, $target) {
            $slot = RandevuSlot::query()
                ->whereKey($validated['randevu_slot_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($slot->durum !== RandevuSlotDurumu::Musait) {
                throw ValidationException::withMessages([
                    'randevu_slot_id' => 'Bu saat artık müsait değil.',
                ]);
            }

            if ($slot->baslangic->isPast()) {
                throw ValidationException::withMessages([
                    'randevu_slot_id' => 'Geçmiş bir saat seçilemez.',
                ]);
            }

            if (! $slot->doctor || ! $slot->doctor->is_active) {
                throw ValidationException::withMessages([
                    'randevu_slot_id' => 'Doktor şu an randevu almıyor.',
                ]);
            }

            if ($slot->randevu()->where('durum', '!=', RandevuDurumu::Iptal)->exists()) {
                throw ValidationException::withMessages([
                    'randevu_slot_id' => 'Bu saat dolu.',
                ]);
            }

            $slotTipi = $slot->slot_tipi ?? RandevuSlotTipi::Normal;
            if ($slotTipi === RandevuSlotTipi::Oncelikli && ! $target->isOncelikliHasta()) {
                throw ValidationException::withMessages([
                    'randevu_slot_id' => 'Bu saat yalnızca öncelikli hastalar (65 yaş üstü veya engelli) içindir.',
                ]);
            }

            $cakis = Randevu::query()
                ->where('user_id', $target->id)
                ->whereIn('durum', [RandevuDurumu::Bekliyor, RandevuDurumu::Onaylandi])
                ->whereHas('slot', function ($q) use ($slot) {
                    $q->where('baslangic', '<', $slot->bitis)
                        ->where('bitis', '>', $slot->baslangic);
                })
                ->exists();

            if ($cakis) {
                throw ValidationException::withMessages([
                    'randevu_slot_id' => 'Bu hasta için aynı zaman dilimine denk gelen başka bir randevunuz var. Önce onu iptal edin veya farklı bir saat seçin.',
                ]);
            }

            $slot->update(['durum' => RandevuSlotDurumu::Rezerve]);

            Randevu::query()->create([
                'user_id' => $target->getAuthIdentifier(),
                'booked_by_user_id' => (int) $target->id !== (int) $patient->id ? $patient->id : null,
                'doctor_id' => $slot->doctor_id,
                'randevu_slot_id' => $slot->id,
                'sikayet' => $validated['sikayet'] ?? null,
                'gizli' => $gizliRandevu,
                'durum' => RandevuDurumu::Onaylandi,
            ]);
        });

        $success = (int) $target->id === (int) $patient->id
            ? 'Randevunuz oluşturuldu ve anında onaylandı.'
            : ($target->name.' adına randevu oluşturuldu ve anında onaylandı.');

        return redirect()
            ->route('musteri.panel')
            ->with('success', $success);
    }

    public function randevuIptal(Request $request, Randevu $randevu): RedirectResponse
    {
        $self = MusteriAccess::user();
        if (! $this->hastaRandevusunuYonetebilir($randevu, $self)) {
            abort(403);
        }

        if (! in_array($randevu->durum, [RandevuDurumu::Bekliyor, RandevuDurumu::Onaylandi], true)) {
            return redirect()
                ->back(fallback: route('musteri.panel'))
                ->with('error', 'Yalnızca onaylı veya bekleyen randevular iptal edilebilir.');
        }

        DB::transaction(function () use ($randevu, $self) {
            $slot = RandevuSlot::query()
                ->whereKey($randevu->randevu_slot_id)
                ->lockForUpdate()
                ->first();

            $randevu->update([
                'durum' => RandevuDurumu::Iptal,
                'iptal_nedeni' => (int) $randevu->user_id === (int) $self->getAuthIdentifier()
                    ? 'Hasta tarafından iptal'
                    : 'Vekil / yetkili kullanıcı tarafından iptal',
            ]);

            if ($slot) {
                $slot->update(['durum' => RandevuSlotDurumu::Musait]);
            }
        });

        return redirect()
            ->back(fallback: route('musteri.panel'))
            ->with('success', 'Randevu iptal edildi.');
    }

    public function randevuKatilimBildirimiKaydet(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'randevu_id' => ['required', 'integer', 'exists:randevular,id'],
            'yanit' => ['required', Rule::in([
                RandevuKatilimDurumu::Gelecek->value,
                RandevuKatilimDurumu::Gelemeyecek->value,
            ])],
            'notification_id' => ['nullable', 'string'],
        ]);

        $self = MusteriAccess::user();
        $randevu = Randevu::query()
            ->whereKey($validated['randevu_id'])
            ->where('user_id', $self->getAuthIdentifier())
            ->whereIn('durum', [RandevuDurumu::Bekliyor, RandevuDurumu::Onaylandi])
            ->firstOrFail();

        $yanit = RandevuKatilimDurumu::from($validated['yanit']);
        $randevu->update([
            'katilim_durumu' => $yanit,
            'katilim_bildirimi_at' => now(),
        ]);

        $notificationId = $validated['notification_id'] ?? null;
        if (is_string($notificationId) && $notificationId !== '') {
            $notif = $self->unreadNotifications()
                ->where('id', $notificationId)
                ->first();
            if ($notif) {
                $notifRandevuId = (int) ($notif->data['randevu_id'] ?? 0);
                if ($notifRandevuId === (int) $randevu->id) {
                    $notif->markAsRead();
                }
            }
        }

        $self->unreadNotifications()
            ->when(
                DB::connection()->getDriverName() === 'pgsql',
                fn ($q) => $q
                    ->whereRaw("(data::jsonb ->> 'kind') = 'appointment_attendance_check'")
                    ->whereRaw("(data::jsonb ->> 'randevu_id')::int = ?", [(int) $randevu->id]),
                fn ($q) => $q
                    ->where('data->kind', 'appointment_attendance_check')
                    ->where('data->randevu_id', (int) $randevu->id)
            )
            ->update(['read_at' => now()]);

        return redirect()
            ->back(fallback: route('musteri.panel'))
            ->with('success', $yanit === RandevuKatilimDurumu::Gelecek
                ? 'Katılım bildiriminiz alındı: randevuya geleceğinizi belirttiniz.'
                : 'Katılım bildiriminiz alındı: randevuya gelemeyeceğinizi belirttiniz.');
    }

    public function yetkiliOlduklarim(): View
    {
        $user = MusteriAccess::user()->fresh();
        $manuelProxyHastalar = $user->proxyPatients()->orderByPivot('created_at', 'desc')->get();

        return view('musteri.yetkili-olduklarim', compact(
            'manuelProxyHastalar'
        ));
    }

    public function yetkiliHastaEkle(Request $request): RedirectResponse
    {
        $tcDigits = preg_replace('/\D/', '', (string) $request->input('tc_kimlik_no', ''));
        $request->merge(['tc_kimlik_no' => $tcDigits]);
        $seriNo = strtoupper(trim((string) $request->input('seri_no', '')));
        $request->merge(['seri_no' => $seriNo]);

        $validated = $request->validate([
            'tc_kimlik_no' => ['required', 'digits:11'],
            'kimlik_ad' => ['required', 'string', 'max:100'],
            'kimlik_soyad' => ['required', 'string', 'max:100'],
            'seri_no' => ['required', 'string', 'max:32', 'regex:/^[A-Z0-9-]+$/'],
            'cinsiyet' => ['required', 'in:E,K,D'],
        ], [
            'tc_kimlik_no.digits' => 'T.C. kimlik numarası 11 haneli olmalıdır.',
            'seri_no.regex' => 'Seri no yalnızca harf, rakam ve tire içerebilir.',
            'cinsiyet.in' => 'Cinsiyet alanında geçerli bir seçenek seçin.',
        ], [
            'tc_kimlik_no' => 'T.C. kimlik numarası',
            'kimlik_ad' => 'ad',
            'kimlik_soyad' => 'soyad',
            'seri_no' => 'seri no',
            'cinsiyet' => 'cinsiyet',
        ]);

        $self = $request->user();
        $other = User::query()->where('tc_kimlik_no', $validated['tc_kimlik_no'])->first();
        if (! $other) {
            $newName = trim($validated['kimlik_ad'].' '.$validated['kimlik_soyad']);
            $syntheticEmail = 'proxy-'.$validated['tc_kimlik_no'].'@mhrs.local';

            $other = User::query()->create([
                'name' => $newName,
                'email' => $syntheticEmail,
                'tc_kimlik_no' => $validated['tc_kimlik_no'],
                'phone' => null,
                'birth_date' => null,
                'gender' => strtoupper((string) $validated['cinsiyet']),
                'role' => 'patient',
                'password' => Hash::make(Str::random(32)),
            ]);
        }

        if ((int) $other->id === (int) $self->id) {
            return redirect()
                ->route('musteri.yetkili-olduklarim')
                ->withInput($request->except('tc_kimlik_no'))
                ->with('error', 'Kendi T.C. kimlik numaranızı ekleyemezsiniz.');
        }
        if (! $other->isPatient()) {
            return redirect()
                ->route('musteri.yetkili-olduklarim')
                ->withInput($request->except('tc_kimlik_no'))
                ->with('error', 'Yalnızca hasta hesapları eklenebilir.');
        }
        if (! $this->kimlikAdSoyadEslesti($other, $validated['kimlik_ad'], $validated['kimlik_soyad'])) {
            return redirect()
                ->route('musteri.yetkili-olduklarim')
                ->withInput($request->except('tc_kimlik_no'))
                ->with('error', 'Ad ve soyad bilgileri, hastanın kayıtlı bilgileriyle eşleşmiyor.');
        }

        if ($other->gender === null || strtoupper((string) $other->gender) !== strtoupper((string) $validated['cinsiyet'])) {
            return redirect()
                ->route('musteri.yetkili-olduklarim')
                ->withInput($request->except('tc_kimlik_no'))
                ->with('error', 'Cinsiyet bilgisi, sistemdeki hasta kaydı ile uyuşmuyor veya kayıtta eksik.');
        }

        if ($self->proxyPatients()->where('users.id', $other->id)->exists()) {
            return redirect()
                ->route('musteri.yetkili-olduklarim')
                ->with('error', 'Bu kişi zaten listenizde.');
        }

        $self->proxyPatients()->attach($other->id, [
            'kimlik_tc_kimlik_no' => $validated['tc_kimlik_no'],
            'kimlik_seri_no' => $validated['seri_no'],
            'kimlik_dogum_tarihi' => $other->birth_date?->toDateString(),
            'kimlik_cinsiyet' => strtoupper((string) $validated['cinsiyet']),
        ]);

        return redirect()
            ->route('musteri.yetkili-olduklarim')
            ->with('success', $other->name.' yetkili olduklarınıza eklendi.');
    }

    public function yetkiliHastaKaldir(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $self = $request->user();
        $detached = $self->proxyPatients()->detach($validated['patient_user_id']);

        if ($detached === 0) {
            return redirect()
                ->route('musteri.yetkili-olduklarim')
                ->with('error', 'Bu kayıt manuel listenizde bulunamadı.');
        }

        return redirect()
            ->route('musteri.yetkili-olduklarim')
            ->with('success', 'Manuel kayıt kaldırıldı.');
    }

    private function normalizeKimlikAdSoyad(string $ad, string $soyad): string
    {
        $birlesik = trim($ad).' '.trim($soyad);

        return $this->normalizeKimlikTamAd($birlesik);
    }

    private function normalizeKimlikTamAd(string $name): string
    {
        $s = trim((string) preg_replace('/\s+/u', ' ', $name));

        return mb_strtolower($s, 'UTF-8');
    }

    private function kimlikAdSoyadEslesti(User $other, string $ad, string $soyad): bool
    {
        $form = $this->normalizeKimlikAdSoyad($ad, $soyad);
        $full = $this->normalizeKimlikTamAd((string) $other->name);
        if ($form === $full) {
            return true;
        }

        $parts = preg_split('/\s+/u', trim((string) $other->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($parts) < 2) {
            return false;
        }

        $ilk = $this->normalizeKimlikTamAd($parts[0]);
        $son = $this->normalizeKimlikTamAd($parts[count($parts) - 1]);

        return $ilk === $this->normalizeKimlikTamAd($ad)
            && $son === $this->normalizeKimlikTamAd($soyad);
    }

    private function randevuHedefPatientFromRequest(User $self, ?int $hastaId): User
    {
        if ($hastaId === null || $hastaId === (int) $self->id) {
            return $self;
        }

        $target = User::query()->whereKey($hastaId)->firstOrFail();
        if (! $target->isPatient()) {
            throw ValidationException::withMessages(['hasta_id' => 'Geçersiz hasta seçimi.']);
        }
        if (! $this->vekilRandevuBaskasiAdinaYetkisiVar($self, $hastaId)) {
            throw ValidationException::withMessages(['hasta_id' => 'Bu kişi adına randevu alma yetkiniz yok.']);
        }

        return $target;
    }

    private function vekilRandevuBaskasiAdinaYetkisiVar(User $guardian, int $patientUserId): bool
    {
        return $guardian->proxyPatients()->where('users.id', $patientUserId)->exists();
    }

    private function hastaRandevusunuYonetebilir(Randevu $randevu, User $self): bool
    {
        if ((int) $randevu->user_id === (int) $self->getAuthIdentifier()) {
            return true;
        }
        if ($randevu->booked_by_user_id
            && (int) $randevu->booked_by_user_id === (int) $self->getAuthIdentifier()) {
            return true;
        }

        return $self->proxyPatients()->where('users.id', $randevu->user_id)->exists();
    }

    /**
     * @return array{patient_city: string, patient_district: ?string, aile_hekimi_doctor_id: int}
     */
    private function validatedPatientAileHekimi(Request $request, AileHekimiOneriService $aileHekimiOneriService): array
    {
        $validated = $request->validate([
            'patient_city' => [
                'required',
                'string',
                'max:100',
                Rule::exists('hospitals', 'city')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'patient_district' => ['nullable', 'string', 'max:100'],
            'aile_hekimi_doctor_id' => [
                'required',
                'integer',
                Rule::exists('doctors', 'id')->where(fn ($q) => $q->where('is_aile_hekimi', true)->where('is_active', true)),
            ],
        ], [], [
            'patient_city' => 'il',
            'patient_district' => 'ilçe',
            'aile_hekimi_doctor_id' => 'aile hekimi',
        ]);

        $city = trim((string) $validated['patient_city']);
        $district = trim((string) ($validated['patient_district'] ?? ''));

        $allowedDistricts = Hospital::query()
            ->where('is_active', true)
            ->where('city', $city)
            ->get()
            ->flatMap(fn (Hospital $h) => collect($h->districts ?? []))
            ->map(fn ($d) => trim((string) $d))
            ->filter()
            ->unique();

        $resolvedDistrict = null;

        if ($allowedDistricts->isNotEmpty()) {
            if ($district === '' || ! $allowedDistricts->contains($district)) {
                throw ValidationException::withMessages([
                    'patient_district' => 'Seçilen il için geçerli bir ilçe seçin.',
                ]);
            }
            $resolvedDistrict = $district;
        }

        $doctor = Doctor::query()->find($validated['aile_hekimi_doctor_id']);
        if (! $doctor || ! $aileHekimiOneriService->doctorMatchesPatientArea($doctor, $city, $resolvedDistrict)) {
            throw ValidationException::withMessages([
                'aile_hekimi_doctor_id' => 'Seçilen aile hekimi, girdiğiniz il ve ilçe ile uyumlu olmalıdır.',
            ]);
        }

        return [
            'patient_city' => $city,
            'patient_district' => $resolvedDistrict,
            'aile_hekimi_doctor_id' => (int) $validated['aile_hekimi_doctor_id'],
        ];
    }
}
