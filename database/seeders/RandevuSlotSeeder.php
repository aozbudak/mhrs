<?php

namespace Database\Seeders;

use App\Enums\RandevuSlotDurumu;
use App\Models\Doctor;
use App\Models\RandevuSlot;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RandevuSlotSeeder extends Seeder
{
    /**
     * Çalışma saatlerine göre önümüzdeki günler için 30 dakikalık müsait slotlar üretir.
     */
    public function run(): void
    {
        $daysAhead = 14;
        $slotMinutes = 30;

        $doctors = Doctor::query()
            ->where('is_active', true)
            ->with('workingHours')
            ->get();

        foreach ($doctors as $doctor) {
            foreach ($doctor->workingHours as $wh) {
                for ($d = 0; $d < $daysAhead; $d++) {
                    $date = now()->startOfDay()->addDays($d);
                    if ((int) $date->isoWeekday() !== (int) $wh->weekday) {
                        continue;
                    }

                    $day = $date->format('Y-m-d');
                    $startStr = is_string($wh->start_time) ? $wh->start_time : $wh->start_time->format('H:i:s');
                    $endStr = is_string($wh->end_time) ? $wh->end_time : $wh->end_time->format('H:i:s');
                    $start = Carbon::parse($day.' '.$startStr);
                    $end = Carbon::parse($day.' '.$endStr);

                    $cursor = $start->copy();
                    while ($cursor->copy()->addMinutes($slotMinutes)->lte($end)) {
                        $slotEnd = $cursor->copy()->addMinutes($slotMinutes);

                        RandevuSlot::query()->firstOrCreate(
                            [
                                'doctor_id' => $doctor->id,
                                'baslangic' => $cursor->copy(),
                            ],
                            [
                                'bitis' => $slotEnd,
                                'durum' => RandevuSlotDurumu::Musait,
                            ]
                        );

                        $cursor->addMinutes($slotMinutes);
                    }
                }
            }
        }
    }
}
