<?php

namespace App\Services;

use App\Models\HospitalWorkingHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class HospitalWorkingHoursService
{
    /**
     * @return list<array{weekday:int, start_time:string, end_time:string}>
     */
    public function normalizeIntervalsFromRequest(Request $request): array
    {
        $rawIntervals = $request->input('intervals');
        if (! is_array($rawIntervals)) {
            $rawIntervals = [];
        }
        $request->merge(['intervals' => array_values($rawIntervals)]);

        $validated = $request->validate([
            'intervals' => ['array', 'max:40'],
            'intervals.*.weekday' => ['required', 'integer', 'between:1,7'],
            'intervals.*.start_time' => ['required', 'string', 'max:16'],
            'intervals.*.end_time' => ['required', 'string', 'max:16'],
        ], [], [
            'intervals' => 'çalışma aralığı',
            'intervals.*.weekday' => 'gün',
            'intervals.*.start_time' => 'başlangıç',
            'intervals.*.end_time' => 'bitiş',
        ]);

        return $this->normalizeIntervalRowsArray($validated['intervals'], 'intervals');
    }

    /**
     * @param  list<array{weekday?:mixed, start_time?:mixed, end_time?:mixed}>  $rows
     * @return list<array{weekday:int, start_time:string, end_time:string}>
     */
    public function normalizeIntervalRowsArray(array $rows, string $errorKeyPrefix = 'intervals'): array
    {
        if (count($rows) > 40) {
            throw ValidationException::withMessages([
                $errorKeyPrefix => 'En fazla 40 çalışma aralığı eklenebilir.',
            ]);
        }

        $normalized = [];
        foreach (array_values($rows) as $idx => $row) {
            if (! is_array($row)) {
                continue;
            }
            $weekday = (int) ($row['weekday'] ?? 0);
            if ($weekday < 1 || $weekday > 7) {
                throw ValidationException::withMessages([
                    "$errorKeyPrefix.$idx.weekday" => 'Geçerli bir gün seçin.',
                ]);
            }

            try {
                $start = Carbon::parse((string) ($row['start_time'] ?? ''))->format('H:i:s');
                $end = Carbon::parse((string) ($row['end_time'] ?? ''))->format('H:i:s');
            } catch (\Throwable) {
                throw ValidationException::withMessages([
                    "$errorKeyPrefix.$idx.start_time" => 'Geçerli bir saat girin.',
                ]);
            }

            if ($end <= $start) {
                throw ValidationException::withMessages([
                    "$errorKeyPrefix.$idx.end_time" => 'Bitiş saati başlangıçtan sonra olmalıdır.',
                ]);
            }

            $normalized[] = [
                'weekday' => $weekday,
                'start_time' => $start,
                'end_time' => $end,
            ];
        }

        return $normalized;
    }

    /**
     * @param  list<array{weekday:int, start_time:string, end_time:string}>  $normalized
     */
    public function sync(int $hospitalId, array $normalized): void
    {
        HospitalWorkingHour::query()->where('hospital_id', $hospitalId)->delete();

        foreach ($normalized as $sort => $row) {
            HospitalWorkingHour::query()->create([
                'hospital_id' => $hospitalId,
                'weekday' => $row['weekday'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'sort_order' => $sort,
            ]);
        }
    }

    /**
     * Kayıtlı haftalık aralıklardan tek satırlık form için başlangıç/bitiş (Pzt ilk satır veya ilk kayıt).
     *
     * @param  Collection<int, HospitalWorkingHour|\stdClass>  $hours
     * @return array{baslangic: string, bitis: string}
     */
    public function inferSimpleMuayeneDisplay(Collection $hours): array
    {
        $wd = $hours->filter(fn ($h) => (int) $h->weekday >= 1 && (int) $h->weekday <= 5);
        if ($wd->isEmpty()) {
            return ['baslangic' => '09:00', 'bitis' => '17:00'];
        }

        $start = $wd
            ->map(fn ($h) => $this->toTimeHi($h->start_time))
            ->sort()
            ->first();
        $end = $wd
            ->map(fn ($h) => $this->toTimeHi($h->end_time))
            ->sortDesc()
            ->first();

        return [
            'baslangic' => is_string($start) ? $start : '09:00',
            'bitis' => is_string($end) ? $end : '17:00',
        ];
    }

    /**
     * Hafta içi (Pzt–Cum) her gün aynı tek aralık.
     *
     * @param  array{0?: string, 1?: string}  $errorKeys  [başlangıç alanı, bitiş alanı] doğrulama mesajları için
     * @return list<array{weekday:int, start_time:string, end_time:string}>
     */
    public function buildWeekdayOneToFiveFromSimpleStrings(
        string $baslangic,
        string $bitis,
        array $errorKeys = ['kurum_muayene_baslangic', 'kurum_muayene_bitis']
    ): array {
        $ek0 = $errorKeys[0] ?? 'kurum_muayene_baslangic';
        $ek1 = $errorKeys[1] ?? 'kurum_muayene_bitis';

        try {
            $start = Carbon::parse(trim($baslangic))->format('H:i:s');
            $end = Carbon::parse(trim($bitis))->format('H:i:s');
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                $ek0 => 'Geçerli bir saat girin.',
            ]);
        }

        if ($end <= $start) {
            throw ValidationException::withMessages([
                $ek1 => 'Bitiş saati başlangıçtan sonra olmalıdır.',
            ]);
        }

        $rows = [];
        foreach ([1, 2, 3, 4, 5] as $weekday) {
            $rows[] = [
                'weekday' => $weekday,
                'start_time' => $start,
                'end_time' => $end,
            ];
        }

        return $rows;
    }

    /**
     * Hafta içi (Pzt-Cum) için öğle aralı iki satır (sabah + öğleden sonra) üretir.
     *
     * @return list<array{weekday:int, start_time:string, end_time:string}>
     */
    public function buildWeekdayOneToFiveWithLunchBreak(
        string $morningStart = '09:00',
        string $morningEnd = '12:00',
        string $afternoonStart = '13:00',
        string $afternoonEnd = '17:00'
    ): array {
        $morningRows = $this->buildWeekdayOneToFiveFromSimpleStrings(
            $morningStart,
            $morningEnd,
            ['kurum_muayene_baslangic', 'kurum_muayene_bitis']
        );
        $afternoonRows = $this->buildWeekdayOneToFiveFromSimpleStrings(
            $afternoonStart,
            $afternoonEnd,
            ['kurum_muayene_baslangic_ogle', 'kurum_muayene_bitis_ogle']
        );

        $out = [];
        for ($i = 0; $i < 5; $i++) {
            $out[] = $morningRows[$i];
            $out[] = $afternoonRows[$i];
        }

        return $out;
    }

    /**
     * Hafta ici (Pzt-Cum) icin 4 saat dilimi uretir:
     * sabah 2 aralik + ogleden sonra 2 aralik.
     *
     * @return list<array{weekday:int, start_time:string, end_time:string}>
     */
    public function buildWeekdayOneToFiveWithFourShifts(
        string $morningStart = '09:00',
        string $morningEnd = '12:00',
        string $afternoonStart = '13:00',
        string $afternoonEnd = '17:00'
    ): array {
        $rows = [];
        foreach ([1, 2, 3, 4, 5] as $weekday) {
            $rows[] = ['weekday' => $weekday, 'start_time' => Carbon::parse($morningStart)->format('H:i:s'), 'end_time' => '10:30:00'];
            $rows[] = ['weekday' => $weekday, 'start_time' => '10:30:00', 'end_time' => Carbon::parse($morningEnd)->format('H:i:s')];
            $rows[] = ['weekday' => $weekday, 'start_time' => Carbon::parse($afternoonStart)->format('H:i:s'), 'end_time' => '15:00:00'];
            $rows[] = ['weekday' => $weekday, 'start_time' => '15:00:00', 'end_time' => Carbon::parse($afternoonEnd)->format('H:i:s')];
        }

        return $rows;
    }

    /**
     * @return list<array{weekday:int, start_time:string, end_time:string}>
     */
    public function normalizeKurumMuayeneSimpleFromRequest(Request $request): array
    {
        $validated = $request->validate([
            'kurum_muayene_baslangic' => ['required', 'string', 'max:16'],
            'kurum_muayene_bitis' => ['required', 'string', 'max:16'],
        ], [], [
            'kurum_muayene_baslangic' => 'muayene başlangıç',
            'kurum_muayene_bitis' => 'muayene bitiş',
        ]);

        return $this->buildWeekdayOneToFiveFromSimpleStrings(
            $validated['kurum_muayene_baslangic'],
            $validated['kurum_muayene_bitis']
        );
    }

    public function toTimeHi(mixed $t): string
    {
        if ($t instanceof \DateTimeInterface) {
            return $t->format('H:i');
        }

        try {
            return Carbon::parse((string) $t)->format('H:i');
        } catch (\Throwable) {
            return '09:00';
        }
    }
}
