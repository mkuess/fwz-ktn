<?php

namespace Tests\Feature;

use App\Filament\Pages\Einstellungen;
use App\Models\Organisation;
use App\Models\Setting;
use App\Models\User;
use App\Models\VolunteerListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicVolunteerListingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_volunteer_listings_are_visible_on_homepage_and_index(): void
    {
        $listing = $this->createListing();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Aktuelle Möglichkeiten für dein Engagement')
            ->assertSee($listing->title)
            ->assertSee('href="'.route('volunteer-listings.index').'"', false);

        $this->get(route('volunteer-listings.index'))
            ->assertOk()
            ->assertSee($listing->title)
            ->assertSee($listing->organisation->name);
    }

    public function test_admin_can_hide_the_public_volunteer_listings_area(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(Einstellungen::class)
            ->assertSet('volunteer_listings_enabled', true)
            ->set('volunteer_listings_enabled', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse(Setting::enabled('volunteer_listings_enabled'));
        $this->get(route('home'))
            ->assertDontSee('Aktuelle Möglichkeiten für dein Engagement')
            ->assertDontSee('href="'.route('volunteer-listings.index').'"', false);
        $this->get(route('volunteer-listings.index'))->assertNotFound();
    }

    public function test_expired_or_inactive_listings_are_not_public(): void
    {
        $inactive = $this->createListing([
            'title' => 'Inaktives Gesuch',
            'is_active' => false,
        ]);
        $expired = $this->createListing([
            'title' => 'Abgelaufenes Gesuch',
            'valid_until' => today()->subDay(),
        ]);

        $this->get(route('volunteer-listings.index'))
            ->assertOk()
            ->assertDontSee($inactive->title)
            ->assertDontSee($expired->title);
    }

    private function createListing(array $attributes = []): VolunteerListing
    {
        $organisation = Organisation::withoutEvents(
            fn (): Organisation => Organisation::create([
                'type' => 'verein',
                'role' => 'org_admin',
                'name' => 'Öffentliche Testorganisation',
                'email' => fake()->unique()->safeEmail(),
                'password' => 'test-password',
                'is_approved' => true,
                'approval_status' => 'approved',
                'is_active' => true,
            ])
        );

        return VolunteerListing::create(array_merge([
            'organisation_id' => $organisation->id,
            'title' => 'Freiwillige Unterstützung gesucht',
            'description' => 'Wir suchen engagierte freiwillige Helferinnen und Helfer.',
            'city' => 'Klagenfurt',
            'is_spontaneous' => false,
            'is_active' => true,
        ], $attributes));
    }
}
