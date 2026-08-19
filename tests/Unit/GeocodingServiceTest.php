<?php

namespace Tests\Unit;

use App\Services\GeocodingService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeocodingServiceTest extends TestCase
{
    public function test_it_returns_coordinates_for_a_matching_address(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '46.6249000',
                    'lon' => '14.3075000',
                ],
            ]),
        ]);

        $result = (new GeocodingService)->geocode('Rosenegger Straße 20, Klagenfurt');

        $this->assertSame([
            'latitude' => 46.6249,
            'longitude' => 14.3075,
        ], $result);

        Http::assertSent(function (Request $request): bool {
            return str_starts_with($request->url(), 'https://nominatim.openstreetmap.org/search?')
                && $request['q'] === 'Rosenegger Straße 20, Klagenfurt'
                && $request['format'] === 'jsonv2'
                && (int) $request['limit'] === 1
                && $request['countrycodes'] === 'at'
                && $request->hasHeader('User-Agent', 'FWZ-Kaernten/1.0 (info@fwz-ktn.at)');
        });
    }

    public function test_it_returns_null_when_no_location_matches(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([]),
        ]);

        $this->assertNull((new GeocodingService)->geocode('Unbekannte Adresse'));
    }

    public function test_it_rejects_invalid_coordinates(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '200',
                    'lon' => '14.3075000',
                ],
            ]),
        ]);

        $this->assertNull((new GeocodingService)->geocode('Ungültige Koordinaten'));
    }
}
