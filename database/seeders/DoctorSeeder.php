<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorWorkingHour;
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
        $demoDoctor = Doctor::query()->updateOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'department_id' => $firstDept->id,
                'title' => 'Uzman Dr.',
                'license_number' => 'DEMO-001',
                'bio' => 'Demo hesap — giriş: doktor@example.com',
                'is_active' => true,
            ]
        );

        $this->seedWeekdaySlots($demoDoctor);

        $t = 0;
        foreach ($departments->skip(1) as $dept) {
            $doctor = Doctor::query()->updateOrCreate(
                ['license_number' => 'SEED-DEPT-'.$dept->id],
                [
                    'user_id' => null,
                    'department_id' => $dept->id,
                    'title' => $titles[$t % count($titles)],
                    'is_active' => true,
                ]
            );
            $t++;
            $this->seedWeekdaySlots($doctor);
        }
    }

    private function seedWeekdaySlots(Doctor $doctor): void
    {
        DoctorWorkingHour::query()->where('doctor_id', $doctor->id)->delete();

        foreach ([1, 2, 3, 4, 5] as $weekday) {
            DoctorWorkingHour::query()->create([
                'doctor_id' => $doctor->id,
                'weekday' => $weekday,
                'start_time' => '09:00:00',
                'end_time' => '12:30:00',
                'sort_order' => 0,
            ]);
            DoctorWorkingHour::query()->create([
                'doctor_id' => $doctor->id,
                'weekday' => $weekday,
                'start_time' => '13:30:00',
                'end_time' => '17:00:00',
                'sort_order' => 1,
            ]);
        }
    }
}
