<?php

namespace App\Http\Controllers\Musteri;

use App\Enums\RandevuDurumu;
use App\Enums\RandevuSlotDurumu;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Hospital;
use App\Support\MusteriAccess;
use App\Models\Doctor;
use App\Models\Randevu;
use App\Models\RandevuSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MusteriPanelController extends Controller
{
    public function index(): View
    {
        $baseQuery = Randevu::query()
            ->where('user_id', MusteriAccess::user()->getAuthIdentifier())
            ->with(['doctor.user', 'doctor.department', 'slot']);

        $yaklasanRandevular = (clone $baseQuery)
            ->where('durum', RandevuDurumu::Bekliyor)
            ->whereHas('slot', function ($q) {
                $q->where('baslangic', '>=', now());
            })
            ->get()
            ->sortBy(function ($r) {
                return $r->slot?->baslangic?->getTimestamp() ?? PHP_INT_MAX;
            })
            ->take(5);

        $sonRandevular = (clone $baseQuery)
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        return view('musteri.panel', compact('yaklasanRandevular', 'sonRandevular'));
    }

    public function randevuAlForm(Request $request): View
    {
        $hospitalId = $request->integer('hospital_id') ?: null;
        $city = $request->string('city')->trim()->value() ?: null;
        $district = $request->string('district')->trim()->value() ?: null;

        if ($hospitalId) {
            $resolvedHospital = Hospital::query()
                ->whereKey($hospitalId)
                ->where('is_active', true)
                ->first();
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

        $cities = Hospital::query()
            ->where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->values();

        $districts = collect();
        if ($city) {
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
        }

        $hospitals = collect();
        if ($city) {
            $hospitalsQuery = Hospital::query()
                ->where('is_active', true)
                ->where('city', $city);

            if ($district) {
                $hospitalsQuery->where(function ($w) use ($district) {
                    $w->whereJsonContains('districts', $district)
                        ->orWhereNull('districts')
                        ->orWhereJsonLength('districts', 0);
                });
            }

            $hospitals = $hospitalsQuery->orderBy('name')->get();
        }

        $departments = collect();
        if ($hospitalId) {
            $departments = Department::query()
                ->where('is_active', true)
                ->whereHas('doctors', function ($q) use ($hospitalId) {
                    $q->where('hospital_id', $hospitalId)->where('is_active', true);
                })
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
        }

        $slots = collect();
        $availableDates = collect();
        if ($doctorId) {
            $baseSlots = RandevuSlot::query()
                ->where('doctor_id', $doctorId)
                ->where('durum', RandevuSlotDurumu::Musait)
                ->where('baslangic', '>', now())
                ->whereBetween('baslangic', [now(), now()->addDays(14)->endOfDay()])
                ->whereDoesntHave('randevu', function ($q) {
                    $q->where('durum', '!=', RandevuDurumu::Iptal);
                })
                ->orderBy('baslangic')
                ->get();

            $availableDates = $baseSlots
                ->groupBy(fn ($s) => $s->baslangic->toDateString())
                ->keys()
                ->sort()
                ->values();

            if ($selectedDate) {
                $slots = $baseSlots->filter(fn ($s) => $s->baslangic->isSameDay($selectedDate))->values();
            } else {
                $first = $baseSlots->first();
                if ($first) {
                    $selectedDate = $first->baslangic->copy()->startOfDay();
                    $slots = $baseSlots->filter(fn ($s) => $s->baslangic->isSameDay($selectedDate))->values();
                }
            }
        }

        return view('musteri.randevu-al', compact(
            'hospitalId',
            'city',
            'district',
            'cities',
            'districts',
            'hospitals',
            'departments',
            'departmentId',
            'doctorId',
            'doctors',
            'slots',
            'selectedDate',
            'availableDates'
        ));
    }

    public function doktorlar(): View
    {
        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $doctors = Doctor::query()
            ->where('is_active', true)
            ->with(['user', 'department'])
            ->orderBy('department_id')
            ->orderBy('title')
            ->get();

        return view('musteri.doktorlar', compact('departments', 'doctors'));
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
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:E,K,D'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->birth_date = $validated['birth_date'] ?? null;
        $user->gender = $validated['gender'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('musteri.profil')
            ->with('success', 'Profiliniz güncellendi.');
    }

    public function gecmisRandevular(): View
    {
        $gecmisRandevular = Randevu::query()
            ->where('user_id', MusteriAccess::user()->getAuthIdentifier())
            ->with(['doctor.user', 'doctor.department', 'slot'])
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

        return view('musteri.gecmis-randevular', compact('gecmisRandevular'));
    }

    public function randevuKaydet(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'randevu_slot_id' => ['required', 'integer', 'exists:randevu_slotlari,id'],
            'sikayet' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($validated) {
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

            $slot->update(['durum' => RandevuSlotDurumu::Rezerve]);

            Randevu::query()->create([
                'user_id' => MusteriAccess::user()->getAuthIdentifier(),
                'doctor_id' => $slot->doctor_id,
                'randevu_slot_id' => $slot->id,
                'sikayet' => $validated['sikayet'] ?? null,
                'durum' => RandevuDurumu::Bekliyor,
            ]);
        });

        return redirect()
            ->route('musteri.panel')
            ->with('success', 'Randevunuz oluşturuldu.');
    }

    public function randevuIptal(Request $request, Randevu $randevu): RedirectResponse
    {
        if ($randevu->user_id !== MusteriAccess::user()->getAuthIdentifier()) {
            abort(403);
        }

        if ($randevu->durum !== RandevuDurumu::Bekliyor) {
            return redirect()
                ->back(fallback: route('musteri.panel'))
                ->with('error', 'Yalnızca bekleyen randevular iptal edilebilir.');
        }

        DB::transaction(function () use ($randevu) {
            $slot = RandevuSlot::query()
                ->whereKey($randevu->randevu_slot_id)
                ->lockForUpdate()
                ->first();

            $randevu->update([
                'durum' => RandevuDurumu::Iptal,
                'iptal_nedeni' => 'Hasta tarafından iptal',
            ]);

            if ($slot) {
                $slot->update(['durum' => RandevuSlotDurumu::Musait]);
            }
        });

        return redirect()
            ->back(fallback: route('musteri.panel'))
            ->with('success', 'Randevu iptal edildi.');
    }
}
