<?php

namespace App\Services;

use App\Models\HospitalDepartmentSetting;
use Illuminate\Http\Request;

class HospitalDepartmentSettingService
{
    /**
     * @param  array<int, list<mixed>>  $byDepartment  Birim muayene saatleri (boş olmayan anahtarlar için slot satırı yazılır)
     */
    public function syncFromMuayeneRequest(int $hospitalId, Request $request, array $byDepartment, int $fallbackSlotDakika): void
    {
        $existingRules = HospitalDepartmentSetting::query()
            ->where('hospital_id', $hospitalId)
            ->get()
            ->keyBy(fn (HospitalDepartmentSetting $s) => (int) $s->department_id);

        HospitalDepartmentSetting::query()->where('hospital_id', $hospitalId)->delete();

        foreach ($byDepartment as $departmentId => $rows) {
            $departmentId = (int) $departmentId;
            if ($departmentId < 1 || $rows === []) {
                continue;
            }

            $raw = $request->input("dept_randevu_slot_dakika.$departmentId");
            $slot = DoctorRandevuSlotGenerator::normalizeSlotDakika(
                $raw !== null && $raw !== '' ? $raw : $fallbackSlotDakika
            );

            /** @var HospitalDepartmentSetting|null $existing */
            $existing = $existingRules->get($departmentId);
            HospitalDepartmentSetting::query()->create([
                'hospital_id' => $hospitalId,
                'department_id' => $departmentId,
                'randevu_slot_dakika' => $slot,
                'senior_age_threshold' => (int) ($existing?->senior_age_threshold ?? 65),
                'auto_transfer_senior' => (bool) ($existing?->auto_transfer_senior ?? false),
                'mesai_tasima_aktif' => (bool) ($existing?->mesai_tasima_aktif ?? true),
                'ameliyat_blok_baslangic' => $existing?->ameliyat_blok_baslangic,
                'ameliyat_blok_bitis' => $existing?->ameliyat_blok_bitis,
            ]);
        }
    }
}
