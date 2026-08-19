<?php

namespace App\Services;

use App\Models\Organisation;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    private const ENDPOINT = 'https://nominatim.openstreetmap.org/search';

    private const MINIMUM_REQUEST_INTERVAL_MICROSECONDS = 1_100_000;

    private const RATE_LIMIT_LOCK_KEY = 'geocoding:nominatim:lock';

    private const LAST_REQUEST_STARTED_AT_KEY = 'geocoding:nominatim:last-request-started-at';

    /**
     * @return array{latitude: float, longitude: float}|null
     */
    public function geocode(string $address): ?array
    {
        $address = trim($address);

        if ($address === '') {
            return null;
        }

        try {
            return Cache::lock(self::RATE_LIMIT_LOCK_KEY, 10)
                ->block(10, fn (): ?array => $this->performGeocodeRequest($address));
        } catch (LockTimeoutException $exception) {
            Log::warning('Geocoding rate-limit lock timed out.', [
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    public function geocodeOrganisation(Organisation $organisation): bool
    {
        if (blank($organisation->city) && blank($organisation->zip)) {
            return false;
        }

        $addressSnapshot = [
            'street' => $organisation->street,
            'zip' => $organisation->zip,
            'city' => $organisation->city,
        ];

        $address = $this->buildOrganisationAddress($addressSnapshot);
        $result = $this->geocode($address);

        if ($result === null) {
            return false;
        }

        $query = Organisation::query()->whereKey($organisation->getKey());

        foreach ($addressSnapshot as $column => $value) {
            $value === null
                ? $query->whereNull($column)
                : $query->where($column, $value);
        }

        $updated = $query->update([
            'latitude' => $result['latitude'],
            'longitude' => $result['longitude'],
            'geocoded_at' => now(),
        ]);

        if ($updated !== 1) {
            return false;
        }

        $organisation->refresh();

        return true;
    }

    /**
     * @return array{latitude: float, longitude: float}|null
     */
    private function performGeocodeRequest(string $address): ?array
    {
        $this->waitForRateLimit();
        Cache::put(self::LAST_REQUEST_STARTED_AT_KEY, microtime(true), now()->addMinute());

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'User-Agent' => 'FWZ-Kaernten/1.0 (info@fwz-ktn.at)',
                ])
                ->timeout(5)
                ->get(self::ENDPOINT, [
                    'q' => $address,
                    'format' => 'jsonv2',
                    'limit' => 1,
                    'countrycodes' => 'at',
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Geocoding request failed.', [
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Geocoding provider returned an unsuccessful response.', [
                'status' => $response->status(),
            ]);

            return null;
        }

        $result = $response->json('0');

        if (
            ! is_array($result)
            || ! isset($result['lat'], $result['lon'])
            || ! is_numeric($result['lat'])
            || ! is_numeric($result['lon'])
        ) {
            return null;
        }

        $latitude = (float) $result['lat'];
        $longitude = (float) $result['lon'];

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return null;
        }

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }

    /**
     * @param  array{street: mixed, zip: mixed, city: mixed}  $address
     */
    private function buildOrganisationAddress(array $address): string
    {
        return implode(', ', array_filter([
            $address['street'],
            $address['zip'],
            $address['city'],
            'Kärnten',
            'Österreich',
        ], static fn (mixed $part): bool => filled($part)));
    }

    private function waitForRateLimit(): void
    {
        $lastRequestStartedAt = Cache::get(self::LAST_REQUEST_STARTED_AT_KEY);

        if (! is_numeric($lastRequestStartedAt)) {
            return;
        }

        $elapsedMicroseconds = (int) ((microtime(true) - (float) $lastRequestStartedAt) * 1_000_000);
        $remainingMicroseconds = self::MINIMUM_REQUEST_INTERVAL_MICROSECONDS - $elapsedMicroseconds;

        if ($remainingMicroseconds > 0) {
            usleep($remainingMicroseconds);
        }
    }
}
