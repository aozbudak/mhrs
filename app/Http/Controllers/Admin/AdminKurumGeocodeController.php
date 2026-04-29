<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminKurumGeocodeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:500'],
            'name' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:5000'],
        ]);

        $q = trim((string) ($validated['q'] ?? ''));
        if ($q === '') {
            $parts = array_filter([
                trim((string) ($validated['name'] ?? '')),
                trim((string) ($validated['address'] ?? '')),
                trim((string) ($validated['city'] ?? '')),
                'Türkiye',
            ], fn (string $s): bool => $s !== '');
            $q = implode(', ', $parts);
        }

        if ($q === '' || $q === 'Türkiye') {
            return response()->json(['message' => 'Arama için kurum adı veya adres girin.'], 422);
        }

        $host = parse_url((string) config('app.url'), PHP_URL_HOST);
        $uaHost = is_string($host) && $host !== '' ? $host : 'localhost';

        $response = Http::timeout(12)
            ->withHeaders([
                'User-Agent' => 'MHRS-Admin/1.0 ('.$uaHost.')',
                'Accept-Language' => 'tr',
            ])
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $q,
                'format' => 'json',
                'limit' => 3,
                'countrycodes' => 'tr',
            ]);

        if (! $response->successful()) {
            Log::warning('Nominatim geocode HTTP hatası', ['status' => $response->status()]);

            return response()->json(['message' => 'Konum servisi şu an yanıt vermiyor. Lütfen daha sonra tekrar deneyin.'], 503);
        }

        $rows = $response->json();
        if (! is_array($rows) || $rows === []) {
            return response()->json(['message' => 'Adres için sonuç bulunamadı; metni netleştirip tekrar deneyin.'], 422);
        }

        $first = $rows[0];
        if (! is_array($first)) {
            return response()->json(['message' => 'Geçersiz sonuç.'], 422);
        }

        $lat = isset($first['lat']) ? (float) $first['lat'] : null;
        $lon = isset($first['lon']) ? (float) $first['lon'] : null;
        if ($lat === null || $lon === null || ($lat === 0.0 && $lon === 0.0)) {
            return response()->json(['message' => 'Geçersiz koordinat döndü.'], 422);
        }

        $alternatives = [];
        foreach (array_slice($rows, 1) as $row) {
            if (! is_array($row)) {
                continue;
            }
            if (! isset($row['lat'], $row['lon'])) {
                continue;
            }
            $alternatives[] = [
                'lat' => (float) $row['lat'],
                'lng' => (float) $row['lon'],
                'label' => (string) ($row['display_name'] ?? ''),
            ];
        }

        return response()->json([
            'lat' => $lat,
            'lng' => $lon,
            'label' => (string) ($first['display_name'] ?? ''),
            'alternatives' => $alternatives,
        ]);
    }
}
