<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DepartmentSeeder::class);
        $this->call(DoctorSeeder::class);
        $this->call(RandevuSlotSeeder::class);

        // User::factory(10)->create();

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test Hasta',
                'tc_kimlik_no' => '10000000146',
                'role' => 'patient',
                'password' => 'password',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Yönetici',
                'username' => 'admin',
                'tc_kimlik_no' => '10000000350',
                'role' => 'admin',
                'password' => 'password',
            ]
        );
    }
}
