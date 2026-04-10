<?php

namespace App\Services;

use App\Models\HospitalWorkingHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $normalized = [];
        foreach ($validated['intervals'] as $idx => $row) {
            try {
                $start = Carbon::parse($row['start_time'])->format('H:i:s');
                $end = Carbon::parse($row['end_time'])->format('H:i:s');
            } catch (\Throwable) {
                throw ValidationException::withMessages([
                    "intervals.$idx.start_time" => 'Geçerli bir saat girin.',
                ]);
            }

            if ($end <= $start) {
                throw ValidationException::withMessages([
                    "intervals.$idx.end_time" => 'Bitiş saati başlangıçtan sonra olmalıdır.',
                ]);
            }

            $normalized[] = [
                'weekday' => (int) $row['weekday'],
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
}
