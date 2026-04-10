<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait HandlesHospitalDistrictInput
{
    /**
     * @return array<int, string>|null
     */
    protected function parseDistrictsInput(Request $request): ?array
    {
        $raw = $request->input('districts_input');
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }

        $parts = preg_split('/[\r\n,]+/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY);
        $clean = collect($parts)
            ->map(fn ($s) => trim((string) $s))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $clean === [] ? null : $clean;
    }

    /**
     * @return array<int, string>|null
     */
    protected function validatedDistrictsFromInput(Request $request): ?array
    {
        $parsed = $this->parseDistrictsInput($request);
        if ($parsed === null) {
            return null;
        }

        Validator::make(
            ['districts' => $parsed],
            [
                'districts' => ['array', 'max:50'],
                'districts.*' => ['string', 'max:100'],
            ],
            [],
            ['districts' => 'ilçe']
        )->validate();

        return $parsed;
    }
}
