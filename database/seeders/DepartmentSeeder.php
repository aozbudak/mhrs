<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Dahiliye', 'code' => 'DAH', 'description' => 'İç hastalıkları polikliniği', 'sort_order' => 10],
            ['name' => 'Kardiyoloji', 'code' => 'KARD', 'description' => 'Kalp ve damar hastalıkları', 'sort_order' => 20],
            ['name' => 'Ortopedi ve Travmatoloji', 'code' => 'ORT', 'description' => 'Kemik, eklem ve kas hastalıkları', 'sort_order' => 30],
            ['name' => 'Kulak Burun Boğaz', 'code' => 'KBB', 'description' => null, 'sort_order' => 40],
            ['name' => 'Göz Hastalıkları', 'code' => 'GOZ', 'description' => null, 'sort_order' => 50],
            ['name' => 'Dermatoloji (Cildiye)', 'code' => 'DERM', 'description' => 'Cilt hastalıkları', 'sort_order' => 60],
            ['name' => 'Nöroloji', 'code' => 'NORO', 'description' => 'Beyin ve sinir sistemi', 'sort_order' => 70],
            ['name' => 'Üroloji', 'code' => 'URO', 'description' => null, 'sort_order' => 80],
            ['name' => 'Kadın Hastalıkları ve Doğum', 'code' => 'KHD', 'description' => null, 'sort_order' => 90],
            ['name' => 'Çocuk Sağlığı ve Hastalıkları', 'code' => 'COCUK', 'description' => null, 'sort_order' => 100],
        ];

        foreach ($rows as $row) {
            Department::query()->updateOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'sort_order' => $row['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
