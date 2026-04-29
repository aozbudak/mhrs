<?php

namespace App\Http\Controllers\Hospital;

use App\Enums\GunlukDegisimTur;
use App\Enums\RandevuDurumu;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorActionHistory;
use App\Models\GunlukDegisim;
use App\Models\HospitalDepartmentSetting;
use App\Models\Randevu;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HospitalDepartmentHeadController extends Controller
{
    public function index(): View
    {
        $user = $this->authorizedDepartmentHead();
        ['doctors' => $doctors, 'dailyAppointmentCounts' => $dailyAppointmentCounts, 'leaveMap' => $leaveMap] = $this->doctorDashboardData($user);
        $settings = $this->departmentSettings($user);

        return view('hospital.department-head-panel', [
            'headUser' => $user,
            'doctors' => $doctors,
            'dailyAppointmentCounts' => $dailyAppointmentCounts,
            'leaveMap' => $leaveMap,
            'settings' => $settings,
        ]);
    }

    public function doctors(): View
    {
        $user = $this->authorizedDepartmentHead();
        ['doctors' => $doctors, 'dailyAppointmentCounts' => $dailyAppointmentCounts, 'leaveMap' => $leaveMap] = $this->doctorDashboardData($user);

        return view('hospital.department-head-doctors', [
            'headUser' => $user,
            'doctors' => $doctors,
            'dailyAppointmentCounts' => $dailyAppointmentCounts,
            'leaveMap' => $leaveMap,
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $user = $this->authorizedDepartmentHead();
        $hospitalId = (int) $user->managed_hospital_id;
        $departmentId = (int) $user->managed_department_id;

        $validated = $request->validate([
            'senior_age_threshold' => ['required', 'integer', 'min:50', 'max:120'],
            'auto_transfer_senior' => ['nullable', 'boolean'],
            'mesai_tasima_aktif' => ['nullable', 'boolean'],
        ]);

        HospitalDepartmentSetting::query()->updateOrCreate(
            ['hospital_id' => $hospitalId, 'department_id' => $departmentId],
            [
                'senior_age_threshold' => (int) $validated['senior_age_threshold'],
                'auto_transfer_senior' => $request->boolean('auto_transfer_senior'),
                'mesai_tasima_aktif' => $request->boolean('mesai_tasima_aktif'),
            ]
        );

        return redirect()
            ->route('bolum-baskanligi.panel')
            ->with('success', 'Bölüm başkanlığı kuralları güncellendi.');
    }

    public function setDoctorRandevuErtelemeGunBySelection(Request $request): RedirectResponse
    {
        $doctor = $this->doctorFromSelection($request);

        $validated = $request->validate([
            'erteleme_tarihi' => ['required', 'date', 'after_or_equal:today'],
            'erteleme_nedeni' => ['required', 'string', 'min:3', 'max:512'],
        ]);

        GunlukDegisim::query()->updateOrCreate(
            [
                'doctor_id' => $doctor->id,
                'tarih' => $validated['erteleme_tarihi'],
            ],
            [
                'tur' => GunlukDegisimTur::Kapali,
                'aciklama' => $validated['erteleme_nedeni'],
            ]
        );
        $this->recordDoctorAction(
            $doctor,
            'randevu_erteleme',
            'Randevu günü ertelendi (gün kapalı): '.date('d.m.Y', strtotime($validated['erteleme_tarihi']))
            .' — '.$validated['erteleme_nedeni']
        );

        return redirect()->back()
            ->with('success', 'Seçilen tarih için randevu günü ertelendi; o gün slot üretimi kapalı gün kuralına göre yönetilir.');
    }

    public function setDoctorLeave(Request $request, Doctor $doctor): RedirectResponse
    {
        $user = $this->authorizedDepartmentHead();
        $hospitalId = (int) $user->managed_hospital_id;
        $departmentId = (int) $user->managed_department_id;

        if ((int) $doctor->hospital_id !== $hospitalId || (int) $doctor->department_id !== $departmentId) {
            abort(403);
        }

        $validated = $request->validate([
            'leave_date' => ['required', 'date', 'after_or_equal:today'],
            'leave_note' => ['nullable', 'string', 'max:255'],
        ]);

        GunlukDegisim::query()->updateOrCreate(
            [
                'doctor_id' => $doctor->id,
                'tarih' => $validated['leave_date'],
            ],
            [
                'tur' => GunlukDegisimTur::Kapali,
                'aciklama' => $validated['leave_note'] ?? 'Bölüm başkanı izni',
            ]
        );
        $this->recordDoctorAction(
            $doctor,
            'izin_ekle',
            'İzin günü eklendi: '.date('d.m.Y', strtotime($validated['leave_date']))
        );

        return redirect()->back()
            ->with('success', 'Doktor izin günü kaydedildi. O tarihte randevu slotu üretimi kapalı gün kuralına göre yönetilir.');
    }

    public function setDoctorLeaveBySelection(Request $request): RedirectResponse
    {
        $doctor = $this->doctorFromSelection($request);

        return $this->setDoctorLeave($request, $doctor);
    }

    public function removeDoctorLeave(Request $request, Doctor $doctor): RedirectResponse
    {
        $user = $this->authorizedDepartmentHead();
        $hospitalId = (int) $user->managed_hospital_id;
        $departmentId = (int) $user->managed_department_id;

        if ((int) $doctor->hospital_id !== $hospitalId || (int) $doctor->department_id !== $departmentId) {
            abort(403);
        }

        $validated = $request->validate([
            'leave_date' => ['required', 'date'],
        ]);

        GunlukDegisim::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('tarih', $validated['leave_date'])
            ->where('tur', GunlukDegisimTur::Kapali)
            ->delete();
        $this->recordDoctorAction(
            $doctor,
            'izin_sil',
            'İzin günü silindi: '.date('d.m.Y', strtotime($validated['leave_date']))
        );

        return redirect()->back()->with('success', 'Seçilen izin günü silindi.');
    }

    public function removeDoctorLeaveBySelection(Request $request): RedirectResponse
    {
        $doctor = $this->doctorFromSelection($request);

        return $this->removeDoctorLeave($request, $doctor);
    }

    private function authorizedDepartmentHead(): User
    {
        $user = auth('hospital')->user();
        if (
            ! $user instanceof User
            || ! $user->isDepartmentHead()
            || ((int) $user->managed_hospital_id) < 1
            || ((int) $user->managed_department_id) < 1
        ) {
            abort(403);
        }

        return $user;
    }

    /** @return array{doctors:\Illuminate\Support\Collection, dailyAppointmentCounts:\Illuminate\Support\Collection, leaveMap:\Illuminate\Support\Collection} */
    private function doctorDashboardData(User $user): array
    {
        $hospitalId = (int) $user->managed_hospital_id;
        $departmentId = (int) $user->managed_department_id;
        $today = now()->toDateString();

        $doctors = Doctor::query()
            ->where('hospital_id', $hospitalId)
            ->where('department_id', $departmentId)
            ->with(['user', 'department', 'actionHistories'])
            ->get()
            ->sortBy(fn (Doctor $doctor) => mb_strtolower($doctor->user?->name ?? ('Doktor '.$doctor->id)))
            ->values();

        $doctorIds = $doctors->pluck('id')->all();

        $dailyAppointmentCounts = Randevu::query()
            ->selectRaw('doctor_id, COUNT(*) as toplam')
            ->whereIn('doctor_id', $doctorIds)
            ->where('durum', '!=', RandevuDurumu::Iptal)
            ->whereHas('slot', fn ($q) => $q->whereDate('baslangic', $today))
            ->groupBy('doctor_id')
            ->pluck('toplam', 'doctor_id');

        $leaveMap = GunlukDegisim::query()
            ->whereIn('doctor_id', $doctorIds)
            ->whereDate('tarih', '>=', $today)
            ->where('tur', GunlukDegisimTur::Kapali)
            ->orderBy('tarih')
            ->get()
            ->groupBy('doctor_id');

        return [
            'doctors' => $doctors,
            'dailyAppointmentCounts' => $dailyAppointmentCounts,
            'leaveMap' => $leaveMap,
        ];
    }

    private function departmentSettings(User $user): HospitalDepartmentSetting
    {
        return HospitalDepartmentSetting::query()->firstOrCreate([
            'hospital_id' => (int) $user->managed_hospital_id,
            'department_id' => (int) $user->managed_department_id,
        ], [
            'randevu_slot_dakika' => 30,
        ]);
    }

    private function recordDoctorAction(Doctor $doctor, string $type, string $text): void
    {
        DoctorActionHistory::query()->create([
            'doctor_id' => $doctor->id,
            'action_type' => $type,
            'action_text' => $text,
        ]);

        $idsToDelete = DoctorActionHistory::query()
            ->where('doctor_id', $doctor->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->skip(5)
            ->pluck('id');

        if ($idsToDelete->isNotEmpty()) {
            DoctorActionHistory::query()->whereIn('id', $idsToDelete->all())->delete();
        }
    }

    private function doctorFromSelection(Request $request): Doctor
    {
        $user = $this->authorizedDepartmentHead();
        $validated = $request->validate([
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
        ]);

        $doctor = Doctor::query()->findOrFail((int) $validated['doctor_id']);
        if (
            (int) $doctor->hospital_id !== (int) $user->managed_hospital_id
            || (int) $doctor->department_id !== (int) $user->managed_department_id
        ) {
            abort(403);
        }

        return $doctor;
    }
}
