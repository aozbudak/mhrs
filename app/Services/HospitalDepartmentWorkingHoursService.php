<?php

namespace App\Services;

use App\Models\HospitalDepartmentWorkingHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class HospitalDepartmentWorkingHoursService
{
    public function __construct(
        private readonly HospitalWorkingHoursService $workingHoursService
    ) {}

    /**
     * @param  list<int>  $allowedDepartmentIds
     * @return array<int, list<array{weekday:int, start_time:string, end_time:string}>>
     */
    public function normalizeFromRequest(Request $request, array $allowedDepartmentIds, string $rootKey = 'dept_intervals'): array
    {
        $allowed = array_values(array_unique(array_filter(array_map('intval', $allowedDepartmentIds))));
        $out = [];

        foreach ($allowed as $deptId) {
            $raw = $request->input($rootKey.'.'.$deptId);
            if (! is_array($raw)) {
                $raw = [];
            }
            $prefix = $rootKey.'.'.$deptId;
            $out[$deptId] = $this->workingHoursService->normalizeIntervalRowsArray(array_values($raw), $prefix);
        }

        return $out;
    }

    /**
     * Birim başına iki kutu: öğleden önce + öğleden sonra.
     * $requiredDepartmentIds içindeki birimlerde saatler zorunludur; diğerlerinde boş bırakılabilir (kayıt yazılmaz).
     * Saat doluysa dept_randevu_slot_dakika.{id} zorunludur.
     *
     * @param  list<int>  $allDepartmentIds
     * @param  list<int>  $requiredDepartmentIds
     * @return array<int, list<array{weekday:int, start_time:string, end_time:string}>>
     */
    public function normalizeDeptMuayeneSimpleFromRequest(Request $request, array $allDepartmentIds, array $requiredDepartmentIds): array
    {
        $all = array_values(array_unique(array_filter(array_map('intval', $allDepartmentIds))));
        $required = array_values(array_unique(array_filter(array_map('intval', $requiredDepartmentIds))));
        $out = [];

        foreach ($all as $deptId) {
            $ob = $request->input("dept_muayene_simple.$deptId.ogle_once_baslangic");
            $oe = $request->input("dept_muayene_simple.$deptId.ogle_once_bitis");
            $sb = $request->input("dept_muayene_simple.$deptId.ogle_sonra_baslangic");
            $se = $request->input("dept_muayene_simple.$deptId.ogle_sonra_bitis");
            $ob = is_string($ob) ? trim($ob) : '';
            $oe = is_string($oe) ? trim($oe) : '';
            $sb = is_string($sb) ? trim($sb) : '';
            $se = is_string($se) ? trim($se) : '';

            if ($ob === '' && $oe === '' && $sb === '' && $se === '') {
                if (in_array($deptId, $required, true)) {
                    throw ValidationException::withMessages([
                        "dept_muayene_simple.$deptId.ogle_once_baslangic" => 'Bu birimde doktor olduğu için öğleden önce/sonra saatlerini girin.',
                    ]);
                }
                $out[$deptId] = [];

                continue;
            }

            $validator = Validator::make(
                [
                    'ogle_once_baslangic' => $ob,
                    'ogle_once_bitis' => $oe,
                    'ogle_sonra_baslangic' => $sb,
                    'ogle_sonra_bitis' => $se,
                ],
                [
                    'ogle_once_baslangic' => ['required', 'string', 'max:16'],
                    'ogle_once_bitis' => ['required', 'string', 'max:16'],
                    'ogle_sonra_baslangic' => ['required', 'string', 'max:16'],
                    'ogle_sonra_bitis' => ['required', 'string', 'max:16'],
                ],
                [],
                [
                    'ogle_once_baslangic' => 'öğleden önce başlangıç ('.$deptId.')',
                    'ogle_once_bitis' => 'öğleden önce bitiş ('.$deptId.')',
                    'ogle_sonra_baslangic' => 'öğleden sonra başlangıç ('.$deptId.')',
                    'ogle_sonra_bitis' => 'öğleden sonra bitiş ('.$deptId.')',
                ]
            );

            if ($validator->fails()) {
                throw ValidationException::withMessages(
                    collect($validator->errors()->messages())
                        ->mapWithKeys(fn ($msgs, $key) => ["dept_muayene_simple.$deptId.$key" => $msgs])
                        ->all()
                );
            }

            $v = $validator->validated();
            $out[$deptId] = array_merge(
                $this->workingHoursService->buildWeekdayOneToFiveFromSimpleStrings(
                    Carbon::parse($v['ogle_once_baslangic'])->format('H:i'),
                    Carbon::parse($v['ogle_once_bitis'])->format('H:i'),
                    ["dept_muayene_simple.$deptId.ogle_once_baslangic", "dept_muayene_simple.$deptId.ogle_once_bitis"]
                ),
                $this->workingHoursService->buildWeekdayOneToFiveFromSimpleStrings(
                    Carbon::parse($v['ogle_sonra_baslangic'])->format('H:i'),
                    Carbon::parse($v['ogle_sonra_bitis'])->format('H:i'),
                    ["dept_muayene_simple.$deptId.ogle_sonra_baslangic", "dept_muayene_simple.$deptId.ogle_sonra_bitis"]
                )
            );

            $slotRaw = $request->input("dept_randevu_slot_dakika.$deptId");
            $slotValidator = Validator::make(
                ['slot' => $slotRaw],
                ['slot' => ['required', 'integer', 'min:5', 'max:120']],
                [],
                ['slot' => 'randevu dilimi ('.$deptId.')']
            );
            if ($slotValidator->fails()) {
                throw ValidationException::withMessages(
                    collect($slotValidator->errors()->messages())
                        ->mapWithKeys(fn ($msgs, $key) => ["dept_randevu_slot_dakika.$deptId" => $msgs])
                        ->all()
                );
            }
        }

        return $out;
    }

    /**
     * Hastane oluştururken seçilen birimler için öğle aralı varsayılan iki satır üretir.
     *
     * @param  list<int>  $departmentIds
     * @return array<int, list<array{weekday:int, start_time:string, end_time:string}>>
     */
    public function buildDefaultWeekdayRowsWithLunchForDepartments(array $departmentIds): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $departmentIds))));
        $rows = $this->workingHoursService->buildWeekdayOneToFiveWithLunchBreak();
        $out = [];

        foreach ($ids as $departmentId) {
            $out[$departmentId] = $rows;
        }

        return $out;
    }

    /**
     * @param  array<int, list<array{weekday:int, start_time:string, end_time:string}>>  $byDepartment
     */
    public function syncForHospital(int $hospitalId, array $byDepartment): void
    {
        HospitalDepartmentWorkingHour::query()->where('hospital_id', $hospitalId)->delete();

        foreach ($byDepartment as $departmentId => $rows) {
            $departmentId = (int) $departmentId;
            if ($departmentId < 1 || $rows === []) {
                continue;
            }

            foreach (array_values($rows) as $sort => $row) {
                HospitalDepartmentWorkingHour::query()->create([
                    'hospital_id' => $hospitalId,
                    'department_id' => $departmentId,
                    'weekday' => $row['weekday'],
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                    'sort_order' => (int) $sort,
                ]);
            }
        }
    }
}
