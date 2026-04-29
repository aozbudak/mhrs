<?php

use App\Models\Hospital;
use App\Models\HospitalDepartmentSetting;
use App\Models\HospitalDepartmentWorkingHour;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Hospital::query()->select(['id', 'randevu_slot_dakika'])->chunkById(50, function ($hospitals) {
            foreach ($hospitals as $hospital) {
                $deptIds = HospitalDepartmentWorkingHour::query()
                    ->where('hospital_id', $hospital->id)
                    ->distinct()
                    ->pluck('department_id');

                foreach ($deptIds as $departmentId) {
                    HospitalDepartmentSetting::query()->firstOrCreate(
                        [
                            'hospital_id' => $hospital->id,
                            'department_id' => (int) $departmentId,
                        ],
                        [
                            'randevu_slot_dakika' => (int) ($hospital->randevu_slot_dakika ?? 30),
                        ]
                    );
                }
            }
        });
    }

    public function down(): void
    {
        //
    }
};
