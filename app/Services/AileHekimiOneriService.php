<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Support\GeoDistance;
use Illuminate\Support\Collection;

final class AileHekimiOneriService
{
    /**
     * Hasta il / ilçesine göre referans nokta (hastane koordinatlarının ortalaması) ve mesafeye göre sıralı aile hekimleri.
     *
     * @return list<array{id:int, name:string, hospital_name:string, title:?string, distance_km: float|null}>
     */
    public function listNearest(?string $city, ?string $district, int $limit = 20): array
    {
        $city = $city !== null ? trim($city) : '';
        $district = $district !== null ? trim($district) : '';

        if ($city === '') {
            return [];
        }

        $ref = $this->referencePointForLocation($city, $district !== '' ? $district : null);
        $doctors = Doctor::query()
            ->where('is_aile_hekimi', true)
            ->where('is_active', true)
            ->whereHas('hospital', fn ($q) => $q->where('is_active', true))
            ->with(['hospital', 'user'])
            ->get();

        $rows = $doctors->map(function (Doctor $d) use ($ref) {
            $h = $d->hospital;
            $lat = $h?->latitude;
            $lng = $h?->longitude;
            $dist = null;
            if ($ref !== null && $lat !== null && $lng !== null) {
                $dist = round(GeoDistance::haversineKm($ref['lat'], $ref['lng'], (float) $lat, (float) $lng), 2);
            }

            return [
                'id' => (int) $d->id,
                'name' => $d->user?->name ?? trim((string) $d->title) ?: 'İsimsiz',
                'hospital_name' => $h?->name ?? '—',
                'title' => $d->title,
                'distance_km' => $dist,
            ];
        });

        $rows = $this->sortByDistanceThenName($rows);

        return $rows->take($limit)->values()->all();
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    public function referencePointForLocation(string $city, ?string $district): ?array
    {
        $hospitals = $this->hospitalsForPatientArea($city, $district);
        $coords = $hospitals
            ->filter(fn (Hospital $h) => $h->latitude !== null && $h->longitude !== null)
            ->values();

        if ($coords->isEmpty() && $district !== null && $district !== '') {
            $coords = $this->hospitalsForPatientArea($city, null)
                ->filter(fn (Hospital $h) => $h->latitude !== null && $h->longitude !== null)
                ->values();
        }

        if ($coords->isEmpty()) {
            return null;
        }

        $lat = $coords->avg(fn (Hospital $h) => (float) $h->latitude);
        $lng = $coords->avg(fn (Hospital $h) => (float) $h->longitude);

        return ['lat' => (float) $lat, 'lng' => (float) $lng];
    }

    /** @return Collection<int, Hospital> */
    private function hospitalsForPatientArea(string $city, ?string $district): Collection
    {
        $q = Hospital::query()
            ->where('is_active', true)
            ->where('city', $city);

        if ($district !== null && $district !== '') {
            $q->where(function ($w) use ($district) {
                $w->whereJsonContains('districts', $district)
                    ->orWhereNull('districts')
                    ->orWhereJsonLength('districts', 0);
            });
        }

        return $q->get();
    }

    /**
     * @param  Collection<int, array{id:int, name:string, hospital_name:string, title:?string, distance_km: float|null}>  $rows
     * @return Collection<int, array{id:int, name:string, hospital_name:string, title:?string, distance_km: float|null}>
     */
    private function sortByDistanceThenName(Collection $rows): Collection
    {
        return $rows->sort(function (array $a, array $b) {
            $da = $a['distance_km'];
            $db = $b['distance_km'];
            if ($da !== null && $db !== null && $da !== $db) {
                return $da <=> $db;
            }
            if ($da !== null && $db === null) {
                return -1;
            }
            if ($da === null && $db !== null) {
                return 1;
            }

            return strcasecmp($a['hospital_name'].' '.$a['name'], $b['hospital_name'].' '.$b['name']);
        })->values();
    }

    public function doctorMatchesPatientArea(Doctor $doctor, string $patientCity, ?string $patientDistrict): bool
    {
        $doctor->loadMissing('hospital');
        $h = $doctor->hospital;
        if (! $h || ! $h->is_active) {
            return false;
        }
        if (trim($h->city ?? '') !== trim($patientCity)) {
            return false;
        }
        $d = $patientDistrict !== null ? trim($patientDistrict) : '';
        if ($d === '') {
            return true;
        }
        $hds = $h->districts ?? [];
        if ($hds === [] || $hds === null) {
            return true;
        }

        return in_array($d, $hds, true);
    }
}
