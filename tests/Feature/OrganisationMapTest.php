<?php

namespace Tests\Feature;

use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganisationMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_map_only_exposes_active_approved_organisations_with_valid_coordinates(): void
    {
        $this->createOrganisation([
            'name' => 'Kärntner Kartenverein',
            'email' => 'karte@example.test',
            'street' => 'Hauptplatz 1',
            'zip' => '9020',
            'city' => 'Klagenfurt',
            'phone' => '+43 463 123456',
            'website' => 'www.kartenverein.at',
            'latitude' => 46.6249,
            'longitude' => 14.3075,
        ]);
        $withoutCoordinates = $this->createOrganisation([
            'name' => 'Ohne Koordinaten',
            'email' => 'ohne-koordinaten@example.test',
        ]);
        $outsideRange = $this->createOrganisation([
            'name' => 'Ungültiger Standort',
            'email' => 'ungueltig@example.test',
            'latitude' => 91,
            'longitude' => 14,
        ]);
        $this->createOrganisation([
            'name' => 'Nicht freigeschaltet',
            'email' => 'nicht-freigeschaltet@example.test',
            'latitude' => 46.6,
            'longitude' => 14.3,
            'is_approved' => false,
        ]);
        $this->createOrganisation([
            'name' => 'Inaktiver Verein',
            'email' => 'inaktiv@example.test',
            'latitude' => 46.6,
            'longitude' => 14.3,
            'is_active' => false,
        ]);

        $response = $this->get(route('organisations.map'));

        $response->assertOk()
            ->assertSee('Kartenverein')
            ->assertSee('karte@example.test')
            ->assertSee('Hauptplatz 1')
            ->assertSee('www.kartenverein.at')
            ->assertSee('organisations-map')
            ->assertDontSee('Ohne Koordinaten')
            ->assertDontSee('ungueltig@example.test')
            ->assertDontSee('nicht-freigeschaltet@example.test')
            ->assertDontSee('inaktiv@example.test');
    }

    public function test_map_shows_an_explanatory_empty_state_when_no_coordinates_exist(): void
    {
        $this->createOrganisation([
            'name' => 'Noch nicht geocodiert',
            'email' => 'noch-nicht-geocodiert@example.test',
        ]);

        $response = $this->get(route('organisations.map'));

        $response->assertOk()
            ->assertSee('Noch keine Kartenstandorte verfügbar')
            ->assertSee('keine freigeschalteten Vereine mit gespeicherten Koordinaten')
            ->assertDontSee('id="organisations-map"');
    }

    private function createOrganisation(array $attributes = []): Organisation
    {
        return Organisation::withoutEvents(fn (): Organisation => Organisation::create(array_merge([
            'type' => 'verein',
            'role' => 'org_admin',
            'name' => 'Testorganisation',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'test-password',
            'is_approved' => true,
            'is_active' => true,
        ], $attributes)));
    }
}
