<?php

namespace Database\Seeders;

use App\Services\DoctorRandevuSlotGenerator;
use Illuminate\Database\Seeder;

class RandevuSlotSeeder extends Seeder
{
    /**
     * Çalışma saatlerine göre önümüzdeki günler için 30 dakikalık müsait slotlar üretir.
     */
    public function run(): void
    {
        app(DoctorRandevuSlotGenerator::class)->ensureSlotsForAllActiveDoctors(30, 30);
    }
}
