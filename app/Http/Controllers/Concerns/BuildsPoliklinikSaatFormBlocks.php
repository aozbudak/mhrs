<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Services\HospitalWorkingHoursService;

trait BuildsPoliklinikSaatFormBlocks
{
    /** @return list<int> */
    protected function allActiveDepartmentIds(): array
    {
        return Department::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    /** @return list<int> */
    protected function departmentIdsWithDoctorsAtHospital(Hospital $hastane): array
    {
        return Doctor::query()
            ->where('hospital_id', $hastane->id)
            ->distinct()
            ->pluck('department_id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();
    }

    /** @return list<int> */
    protected function departmentIdsForHospitalPoliklinik(Hospital $hastane): array
    {
        $ids = collect()
            ->merge(
                Doctor::query()
                    ->where('hospital_id', $hastane->id)
                    ->pluck('department_id')
                    ->all()
            )
            ->merge($hastane->departmentWorkingHours->pluck('department_id')->all())
            ->merge($hastane->departmentSettings->pluck('department_id')->all())
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $ids;
    }

    /** @return list<int> */
    protected function departmentIdsFromDoctorOldInput(): array
    {
        $raw = old('doctors', []);
        if (! is_array($raw)) {
            return [];
        }

        $ids = [];
        foreach ($raw as $row) {
            if (is_array($row) && ! empty($row['department_id'])) {
                $ids[] = (int) $row['department_id'];
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * @param  list<int>  $deptIds
     * @param  list<int>  $requiredDepartmentIds  Bu birimlerde doktor varken muayene saatleri zorunludur
     * @return list<array{department: Department, dept_id: int, ogle_once_baslangic: string, ogle_once_bitis: string, ogle_sonra_baslangic: string, ogle_sonra_bitis: string, slot_dakika: int}>
     */
    protected function buildPoliklinikSaatFormBlocks(?Hospital $hastane, array $deptIds, array $requiredDepartmentIds = []): array
    {
        $deptIds = array_values(array_unique(array_filter(array_map('intval', $deptIds))));
        if ($deptIds === []) {
            return [];
        }

        $requiredDept = array_values(array_unique(array_filter(array_map('intval', $requiredDepartmentIds))));

        $depts = Department::query()->whereIn('id', $deptIds)->orderBy('sort_order')->orderBy('name')->get()->keyBy('id');
        $whGrouped = $hastane
            ? $hastane->departmentWorkingHours->groupBy('department_id')
            : collect();

        $slotByDept = $hastane
            ? $hastane->departmentSettings->keyBy('department_id')
            : collect();

        $hospitalVarsayilanSlot = (int) ($hastane?->randevu_slot_dakika ?? 30);

        $whSvc = app(HospitalWorkingHoursService::class);
        $defaultBas = '09:00';
        $defaultBit = '17:00';

        $oldSimple = old('dept_muayene_simple');
        $oldSimple = is_array($oldSimple) ? $oldSimple : [];

        $blocks = [];
        foreach ($deptIds as $deptId) {
            $dept = $depts->get($deptId);
            if (! $dept) {
                continue;
            }

            $isRequired = in_array($deptId, $requiredDept, true);

            $hadOldDeptKey = array_key_exists((string) $deptId, $oldSimple) || array_key_exists($deptId, $oldSimple);
            $ogleOnceBaslangic = null;
            $ogleOnceBitis = null;
            $ogleSonraBaslangic = null;
            $ogleSonraBitis = null;

            if ($hadOldDeptKey) {
                $sub = $oldSimple[(string) $deptId] ?? $oldSimple[$deptId] ?? [];
                $sub = is_array($sub) ? $sub : [];
                $ogleOnceBaslangic = isset($sub['ogle_once_baslangic']) ? trim((string) $sub['ogle_once_baslangic']) : '';
                $ogleOnceBitis = isset($sub['ogle_once_bitis']) ? trim((string) $sub['ogle_once_bitis']) : '';
                $ogleSonraBaslangic = isset($sub['ogle_sonra_baslangic']) ? trim((string) $sub['ogle_sonra_baslangic']) : '';
                $ogleSonraBitis = isset($sub['ogle_sonra_bitis']) ? trim((string) $sub['ogle_sonra_bitis']) : '';

                // Legacy tek alanlardan gelen old input icin geri uyumluluk.
                if ($ogleOnceBaslangic === '' && isset($sub['baslangic'])) {
                    $ogleOnceBaslangic = trim((string) $sub['baslangic']);
                }
                if ($ogleSonraBitis === '' && isset($sub['bitis'])) {
                    $ogleSonraBitis = trim((string) $sub['bitis']);
                }
            } elseif ($hastane) {
                $deptRows = $whGrouped->get($deptId, collect());
                if ($deptRows->isNotEmpty()) {
                    $weekdayRows = $deptRows->where('weekday', 1)->sortBy('sort_order')->values();
                    $morningRows = $weekdayRows->filter(fn ($r) => $whSvc->toTimeHi($r->end_time) <= '12:00')->values();
                    $afternoonRows = $weekdayRows->filter(fn ($r) => $whSvc->toTimeHi($r->start_time) >= '12:00')->values();

                    $ogleOnceBaslangic = $morningRows->isNotEmpty()
                        ? $whSvc->toTimeHi($morningRows->first()->start_time)
                        : '';
                    $ogleOnceBitis = $morningRows->isNotEmpty()
                        ? $whSvc->toTimeHi($morningRows->last()->end_time)
                        : '';
                    $ogleSonraBaslangic = $afternoonRows->isNotEmpty()
                        ? $whSvc->toTimeHi($afternoonRows->first()->start_time)
                        : '';
                    $ogleSonraBitis = $afternoonRows->isNotEmpty()
                        ? $whSvc->toTimeHi($afternoonRows->last()->end_time)
                        : '';
                }
            }

            if ($ogleOnceBaslangic === null) {
                $ogleOnceBaslangic = '';
            }
            if ($ogleOnceBitis === null) {
                $ogleOnceBitis = '';
            }
            if ($ogleSonraBaslangic === null) {
                $ogleSonraBaslangic = '';
            }
            if ($ogleSonraBitis === null) {
                $ogleSonraBitis = '';
            }

            if (
                $ogleOnceBaslangic === '' &&
                $ogleOnceBitis === '' &&
                $ogleSonraBaslangic === '' &&
                $ogleSonraBitis === '' &&
                $isRequired
            ) {
                $ogleOnceBaslangic = $defaultBas;
                $ogleOnceBitis = '12:00';
                $ogleSonraBaslangic = '13:00';
                $ogleSonraBitis = $defaultBit;
            }

            $slotDakika = (int) ($slotByDept->get($deptId)?->randevu_slot_dakika ?? $hospitalVarsayilanSlot);

            $blocks[] = [
                'department' => $dept,
                'dept_id' => $deptId,
                'ogle_once_baslangic' => $ogleOnceBaslangic,
                'ogle_once_bitis' => $ogleOnceBitis,
                'ogle_sonra_baslangic' => $ogleSonraBaslangic,
                'ogle_sonra_bitis' => $ogleSonraBitis,
                'slot_dakika' => $slotDakika,
            ];
        }

        return $blocks;
    }
}
