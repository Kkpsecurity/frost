<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * GeocodingHelper
 *
 * Helper class to convert addresses to latitude/longitude coordinates
 * using Google Maps Geocoding API. Includes caching to avoid API rate limits.
 */
class GeocodingHelper
{
    /**
     * Geocode an address to get latitude and longitude
     *
     * @param string $address Full address to geocode
     * @return array|null ['latitude' => float, 'longitude' => float] or null on failure
     */
    public static function geocodeAddress(string $address): ?array
    {
        // Return null if no address provided
        if (empty(trim($address))) {
            return null;
        }

        // Create cache key from address
        $cacheKey = 'geocode:' . md5($address);

        // Check cache first (cache for 30 days)
        return Cache::remember($cacheKey, 60 * 60 * 24 * 30, function () use ($address) {
            return self::fetchGeocode($address);
        });
    }

    /**
     * Fetch geocode data from Google Maps API
     *
     * @param string $address
     * @return array|null
     */
    protected static function fetchGeocode(string $address): ?array
    {
        $apiKey = config('services.google_maps.api_key');

        // Check if API key is configured
        if (empty($apiKey)) {
            Log::warning('Google Maps API key not configured');
            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $apiKey,
            ]);

            if (!$response->successful()) {
                Log::error('Google Maps API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            // Check if we got results
            if ($data['status'] !== 'OK' || empty($data['results'])) {
                Log::warning('Google Maps geocoding failed', [
                    'status' => $data['status'],
                    'address' => $address
                ]);
                return null;
            }

            // Extract coordinates from first result
            $location = $data['results'][0]['geometry']['location'];

            return [
                'latitude' => $location['lat'],
                'longitude' => $location['lng'],
            ];
        } catch (\Exception $e) {
            Log::error('Exception during geocoding', [
                'message' => $e->getMessage(),
                'address' => $address
            ]);
            return null;
        }
    }

    /**
     * Clear geocode cache for a specific address
     *
     * @param string $address
     * @return bool
     */
    public static function clearCache(string $address): bool
    {
        $cacheKey = 'geocode:' . md5($address);
        return Cache::forget($cacheKey);
    }

    /**
     * Validate coordinates
     *
     * @param float|null $latitude
     * @param float|null $longitude
     * @return bool
     */
    public static function validateCoordinates(?float $latitude, ?float $longitude): bool
    {
        if ($latitude === null || $longitude === null) {
            return false;
        }

        // Validate latitude range (-90 to 90)
        if ($latitude < -90 || $latitude > 90) {
            return false;
        }

        // Validate longitude range (-180 to 180)
        if ($longitude < -180 || $longitude > 180) {
            return false;
        }

        return true;
    }
}
