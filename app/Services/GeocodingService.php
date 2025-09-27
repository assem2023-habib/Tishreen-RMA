<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodingService
{
    public static function reverseGeocode(float $lat, float $lng): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'RMA (099assemhb@gmail.com)',
            ])->get("https://nominatim.openstreetmap.org/reverse", [
                'format' => 'jsonv2',
                'lat' => $lat,
                'lon' => $lng,
                'zoom' => 18,
                'addressdetails' => 1,
            ]);
            if ($response->successful()) {
                return $response->json();
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
