<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\HospitalWorkingHour;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $titles = ['Uzman Dr.', 'Doç. Dr.', 'Prof. Dr.', 'Dr.'];

        $departments = Department::query()->orderBy('sort_order')->get();
        if ($departments->isEmpty()) {
            return;
        }

        $hospital = Hospital::query()->firstOrCreate(
            ['name' => 'Örnek Eğitim Hastanesi'],
            [
                'city' => 'İstanbul',
                'districts' => ['Kadıköy'],
                'is_active' => true,
                'latitude' => 40.9903,
                'longitude' => 29.0263,
            ]
        );
        $hospital->fill([
            'latitude' => 40.9903,
            'longitude' => 29.0263,
        ])->save();

        $this->seedHospitalWeekdaySlots($hospital);

        $doctorUser = User::query()->firstOrCreate(
            ['email' => 'doktor@example.com'],
            [
                'name' => 'Örnek Doktor',
                'tc_kimlik_no' => '10000000244',
                'role' => 'doctor',
                'password' => 'password',
            ]
        );

        $firstDept = $departments->first();
        Doctor::query()->updateOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'department_id' => $firstDept->id,
                'hospital_id' => $hospital->id,
                'title' => 'Aile Hekimi',
                'license_number' => 'DEMO-001',
                'bio' => 'Demo hesap — giriş: doktor@example.com — örnek aile hekimi',
                'is_active' => true,
                'is_aile_hekimi' => true,
            ]
        );

        $t = 0;
        foreach ($departments->skip(1) as $dept) {
            Doctor::query()->updateOrCreate(
                ['license_number' => 'SEED-DEPT-'.$dept->id],
                [
                    'user_id' => null,
                    'department_id' => $dept->id,
                    'hospital_id' => $hospital->id,
                    'title' => $titles[$t % count($titles)],
                    'is_active' => true,
                ]
            );
            $t++;
        }
    }

    private function seedHospitalWeekdaySlots(Hospital $hospital): void
    {
        HospitalWorkingHour::query()->where('hospital_id', $hospital->id)->delete();

        foreach ([1, 2, 3, 4, 5] as $sort => $weekday) {
            HospitalWorkingHour::query()->create([
                'hospital_id' => $hospital->id,
                'weekday' => $weekday,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'sort_order' => $sort,
            ]);
        }
    }
}
