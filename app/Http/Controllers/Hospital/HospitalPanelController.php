<?php

namespace App\Http\Controllers\Hospital;

use App\Enums\RandevuDurumu;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\HospitalWorkingHour;
use App\Models\Randevu;
use App\Models\User;
use App\Services\DoctorRandevuSlotGenerator;
use App\Services\HospitalWorkingHoursService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class HospitalPanelController extends Controller
{
    protected function institutionRouteGroup(): string
    {
        return 'hastane';
    }

    protected function institutionRouteName(string $suffix): string
    {
        return $this->institutionRouteGroup().'.'.$suffix;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function institutionView(string $name, array $data = []): View
    {
        return view($name, array_merge($data, [
            'kr' => $this->institutionRouteGroup(),
        ]));
    }

    /** ISO hafta günü 1 = Pazartesi … 7 = Pazar */
    private const GUNLER = [
        1 => 'Pazartesi',
        2 => 'Salı',
        3 => 'Çarşamba',
        4 => 'Perşembe',
        5 => 'Cuma',
        6 => 'Cumartesi',
        7 => 'Pazar',
    ];

    public function __construct(
        private readonly HospitalWorkingHoursService $workingHoursService
    ) {}

    public function index(Request $request): View
    {
        $hastane = $this->authorizedHospital();

        $doktorSayisi = Doctor::query()->where('hospital_id', $hastane->id)->count();
        $aktifRandevu = Randevu::query()
            ->whereHas('doctor', fn ($q) => $q->where('hospital_id', $hastane->id))
            ->where('durum', '!=', RandevuDurumu::Iptal)
            ->count();

        $seciliPoliklinikId = $this->seciliPoliklinikId($request, $hastane);
        $poliklinikler = $this->polikliniklerForHospital($hastane);
        $doktorlar = $this->doktorlarOrdered($hastane, $seciliPoliklinikId);

        return $this->institutionView('hospital.panel', [
            'hastane' => $hastane,
            'doktorSayisi' => $doktorSayisi,
            'aktifRandevu' => $aktifRandevu,
            'doktorlar' => $doktorlar,
            'poliklinikler' => $poliklinikler,
            'seciliPoliklinikId' => $seciliPoliklinikId,
            'routeName' => $this->institutionRouteName('panel'),
            'poliklinikFiltreTemizRoute' => $this->institutionRouteName('panel'),
        ]);
    }

    public function doktorlar(Request $request): View
    {
        $hastane = $this->authorizedHospital();

        $seciliPoliklinikId = $this->seciliPoliklinikId($request, $hastane);
        $poliklinikler = $this->polikliniklerForHospital($hastane);
        $doktorToplam = Doctor::query()->where('hospital_id', $hastane->id)->count();

        return $this->institutionView('hospital.doktorlar', [
            'hastane' => $hastane,
            'doktorlar' => $this->doktorlarOrdered($hastane, $seciliPoliklinikId),
            'poliklinikler' => $poliklinikler,
            'seciliPoliklinikId' => $seciliPoliklinikId,
            'doktorToplam' => $doktorToplam,
            'routeName' => $this->institutionRouteName('doktorlar'),
            'poliklinikFiltreTemizRoute' => $this->institutionRouteName('doktorlar'),
        ]);
    }

    public function ayarlar(Request $request): View
    {
        $hastane = $this->authorizedHospital();
        $hastane->load(['workingHours', 'doctors.user', 'doctors.department']);

        $intervals = old('intervals', null);
        if ($intervals === null) {
            $intervals = $hastane->workingHours->map(function (HospitalWorkingHour $wh) {
                return [
                    'weekday' => (int) $wh->weekday,
                    'start_time' => $this->formatTimeInput($wh->start_time),
                    'end_time' => $this->formatTimeInput($wh->end_time),
                ];
            })->values()->all();
        } else {
            $intervals = array_values(is_array($intervals) ? $intervals : []);
        }

        $seciliPoliklinikId = $this->seciliPoliklinikId($request, $hastane);
        $poliklinikler = $this->polikliniklerForHospital($hastane);
        $doktorlarFiltreli = $this->doktorlarOrdered($hastane, $seciliPoliklinikId);
        $doktorToplam = Doctor::query()->where('hospital_id', $hastane->id)->count();

        return $this->institutionView('hospital.ayarlar', [
            'hastane' => $hastane,
            'intervals' => $intervals,
            'gunler' => self::GUNLER,
            'doktorlarFiltreli' => $doktorlarFiltreli,
            'poliklinikler' => $poliklinikler,
            'seciliPoliklinikId' => $seciliPoliklinikId,
            'doktorToplam' => $doktorToplam,
            'routeName' => $this->institutionRouteName('ayarlar'),
            'poliklinikFiltreTemizRoute' => $this->institutionRouteName('ayarlar'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $hastane = $this->authorizedHospital();

        $normalizedIntervals = $this->workingHoursService->normalizeIntervalsFromRequest($request);
        $generator = app(DoctorRandevuSlotGenerator::class);

        DB::transaction(function () use ($hastane, $normalizedIntervals) {
            $this->workingHoursService->sync($hastane->id, $normalizedIntervals);
        });

        $generator->resyncFutureSlotsForHospital((int) $hastane->id);

        $redirectQuery = [];
        if ($request->filled('poliklinik')) {
            $pid = (int) $request->input('poliklinik');
            if (Doctor::query()->where('hospital_id', $hastane->id)->where('department_id', $pid)->exists()) {
                $redirectQuery['poliklinik'] = $pid;
            }
        }

        return redirect()
            ->route($this->institutionRouteName('ayarlar'), $redirectQuery)
            ->with('success', 'Çalışma saatleri güncellendi; gelecekteki boş randevu slotları yenilendi.');
    }

    public function profil(): View
    {
        $user = $this->authorizedHospitalUser();
        $hastane = $this->authorizedHospital();

        return $this->institutionView('hospital.profil', [
            'user' => $user->fresh(),
            'hastane' => $hastane,
        ]);
    }

    public function profilGuncelle(Request $request): RedirectResponse
    {
        $user = $this->authorizedHospitalUser();

        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', 'current_password:hospital'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [], [
            'current_password' => 'mevcut şifre',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route($this->institutionRouteName('profil'))
            ->with('success', 'Profiliniz güncellendi.');
    }

    protected function authorizedHospitalUser(): User
    {
        $user = auth('hospital')->user();
        if (! $user instanceof User || ! $user->isHospitalAdmin() || ((int) $user->managed_hospital_id) < 1) {
            abort(403);
        }

        return $user;
    }

    protected function authorizedHospital(): Hospital
    {
        $user = $this->authorizedHospitalUser();

        return Hospital::query()
            ->whereKey($user->managed_hospital_id)
            ->where('is_saglik_merkezi', false)
            ->firstOrFail();
    }

    /**
     * Geçerli kurumda bu birime bağlı en az bir doktor yoksa filtre uygulanmaz.
     */
    private function seciliPoliklinikId(Request $request, Hospital $hastane): ?int
    {
        $validated = $request->validate([
            'poliklinik' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $id = isset($validated['poliklinik']) ? (int) $validated['poliklinik'] : null;
        if ($id === null || $id === 0) {
            return null;
        }

        $exists = Doctor::query()
            ->where('hospital_id', $hastane->id)
            ->where('department_id', $id)
            ->exists();

        return $exists ? $id : null;
    }

    /** @return Collection<int, Department> */
    private function polikliniklerForHospital(Hospital $hastane): Collection
    {
        return Department::query()
            ->whereHas('doctors', fn ($q) => $q->where('hospital_id', $hastane->id))
            ->withCount([
                'doctors as hospital_doctor_count' => fn ($q) => $q->where('hospital_id', $hastane->id),
            ])
            ->orderBy('name')
            ->get();
    }

    /** @return Collection<int, Doctor> */
    private function doktorlarOrdered(Hospital $hastane, ?int $departmentId = null): Collection
    {
        $q = Doctor::query()
            ->where('hospital_id', $hastane->id)
            ->with(['user', 'department']);

        if ($departmentId !== null) {
            $q->where('department_id', $departmentId);
        }

        return $q->get()
            ->sortBy(function (Doctor $d) {
                return mb_strtolower($d->user?->name ?? $d->title ?? (string) $d->id);
            })
            ->values();
    }

    private function formatTimeInput(mixed $t): string
    {
        if ($t instanceof \DateTimeInterface) {
            return $t->format('H:i');
        }

        try {
            return Carbon::parse((string) $t)->format('H:i');
        } catch (\Throwable) {
            return '09:00';
        }
    }
}
