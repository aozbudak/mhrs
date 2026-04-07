<?php

namespace App\Services;

use App\Enums\RandevuSlotDurumu;
use App\Enums\RandevuSlotTipi;
use App\Models\Doctor;
use App\Models\RandevuSlot;
use Carbon\Carbon;

class DoctorRandevuSlotGenerator
{
    public const DEFAULT_DAYS_AHEAD = 30;

    /**
     * Gelecekteki, hiç randevu bağlanmamış ve müsait durumundaki slotları siler (program güncellemesi için).
     */
    public function removeFutureUnbookedMusaitSlots(int $doctorId): void
    {
        RandevuSlot::query()
            ->where('doctor_id', $doctorId)
            ->where('baslangic', '>', now())
            ->where('durum', RandevuSlotDurumu::Musait)
            ->whereDoesntHave('randevu')
            ->delete();
    }

    /**
     * Çalışma saatlerine göre ileri tarihli müsait slotları veritabanına yazar (firstOrCreate, tekrar güvenli).
     */
    public function ensureSlotsForDoctor(int $doctorId, int $daysAhead = 30, int $slotMinutes = 30): void
    {
        $doctor = Doctor::query()
            ->whereKey($doctorId)
            ->where('is_active', true)
            ->with('hospital.workingHours')
            ->first();

        if (! $doctor || ! $doctor->hospital) {
            return;
        }

        $workingHours = $doctor->hospital->workingHours;
        if ($workingHours->isEmpty()) {
            return;
        }

        foreach ($workingHours as $wh) {
            for ($d = 0; $d < $daysAhead; $d++) {
                $date = now()->startOfDay()->addDays($d);
                if ((int) $date->isoWeekday() !== (int) $wh->weekday) {
                    continue;
                }

                $day = $date->format('Y-m-d');
                $startStr = $this->formatTimeForParse($wh->start_time);
                $endStr = $this->formatTimeForParse($wh->end_time);
                $start = Carbon::parse($day.' '.$startStr);
                $end = Carbon::parse($day.' '.$endStr);

                $cursor = $start->copy();
                $ilkAralikSlotu = true;
                while ($cursor->copy()->addMinutes($slotMinutes)->lte($end)) {
                    $slotEnd = $cursor->copy()->addMinutes($slotMinutes);
                    $slotTipi = $ilkAralikSlotu ? RandevuSlotTipi::Oncelikli : RandevuSlotTipi::Normal;

                    RandevuSlot::query()->firstOrCreate(
                        [
                            'doctor_id' => $doctor->id,
                            'baslangic' => $cursor->copy(),
                        ],
                        [
                            'bitis' => $slotEnd,
                            'durum' => RandevuSlotDurumu::Musait,
                            'slot_tipi' => $slotTipi,
                        ]
                    );

                    $ilkAralikSlotu = false;
                    $cursor->addMinutes($slotMinutes);
                }
            }
        }
    }

    public function ensureSlotsForAllActiveDoctors(int $daysAhead = 30, int $slotMinutes = 30): void
    {
        $ids = Doctor::query()->where('is_active', true)->pluck('id');
        foreach ($ids as $id) {
            $this->ensureSlotsForDoctor((int) $id, $daysAhead, $slotMinutes);
        }
    }

    /**
     * Hastane çalışma saati değişince: o hastanedeki tüm aktif doktorlar için gelecek boş slotları yeniler.
     */
    public function resyncFutureSlotsForHospital(int $hospitalId, int $daysAhead = 30, int $slotMinutes = 30): void
    {
        $ids = Doctor::query()
            ->where('hospital_id', $hospitalId)
            ->where('is_active', true)
            ->pluck('id');

        foreach ($ids as $id) {
            $this->removeFutureUnbookedMusaitSlots((int) $id);
            $this->ensureSlotsForDoctor((int) $id, $daysAhead, $slotMinutes);
        }
    }

    private function formatTimeForParse(mixed $t): string
    {
        if ($t instanceof \DateTimeInterface) {
            return $t->format('H:i:s');
        }

        return (string) $t;
    }
}
