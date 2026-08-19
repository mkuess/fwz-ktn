<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Services\GeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeocodingOrganisationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_persists_coordinates_for_an_unchanged_address(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '46.6249000',
                    'lon' => '14.3075000',
                ],
            ]),
        ]);

        $organisation = $this->createOrganisationWithoutEvents();

        $this->assertTrue((new GeocodingService)->geocodeOrganisation($organisation));

        $organisation->refresh();

        $this->assertSame(46.6249, $organisation->latitude);
        $this->assertSame(14.3075, $organisation->longitude);
        $this->assertNotNull($organisation->geocoded_at);
    }

    public function test_it_does_not_persist_coordinates_when_the_address_changes_during_the_request(): void
    {
        $organisation = $this->createOrganisationWithoutEvents();

        Http::fake(function () use ($organisation) {
            Organisation::withoutEvents(fn () => $organisation->update([
                'city' => 'Villach',
            ]));

            return Http::response([
                [
                    'lat' => '46.6249000',
                    'lon' => '14.3075000',
                ],
            ]);
        });

        $this->assertFalse((new GeocodingService)->geocodeOrganisation($organisation));

        $organisation->refresh();

        $this->assertSame('Villach', $organisation->city);
        $this->assertNull($organisation->latitude);
        $this->assertNull($organisation->longitude);
        $this->assertNull($organisation->geocoded_at);
    }

    private function createOrganisationWithoutEvents(): Organisation
    {
        return Organisation::withoutEvents(fn (): Organisation => Organisation::create([
            'type' => 'organisation',
            'role' => 'org_admin',
            'name' => 'Geocoding Test',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'test-password',
            'street' => 'Rosenegger Straße 20',
            'zip' => '9021',
            'city' => 'Klagenfurt am Wörthersee',
        ]));
    }
}
