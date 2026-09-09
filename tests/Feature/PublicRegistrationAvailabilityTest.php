<?php

namespace Tests\Feature;

use App\Filament\Pages\Einstellungen;
use App\Models\Organisation;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicRegistrationAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registrations_are_enabled_by_default(): void
    {
        $this->assertDatabaseHas('settings', [
            'key' => 'member_registration_enabled',
            'value' => '1',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'organisation_registration_enabled',
            'value' => '1',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'login_button_enabled',
            'value' => '1',
        ]);

        $this->get(route('member.register'))->assertOk();
        $this->get(route('registrierung.schritt1'))->assertOk();

        $this->get(route('home'))
            ->assertSee('Benefits als Mitglied sichern')
            ->assertSee('Verein anmelden')
            ->assertSee('Jetzt Verein registrieren')
            ->assertSee('Anmelden');
    }

    public function test_admin_can_persist_each_registration_setting_independently(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(Einstellungen::class)
            ->assertSet('member_registration_enabled', true)
            ->assertSet('organisation_registration_enabled', true)
            ->set('member_registration_enabled', false)
            ->assertSet('login_button_enabled', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse(Setting::enabled('member_registration_enabled'));
        $this->assertTrue(Setting::enabled('organisation_registration_enabled'));

        Livewire::test(Einstellungen::class)
            ->assertSet('member_registration_enabled', false)
            ->assertSet('organisation_registration_enabled', true)
            ->set('organisation_registration_enabled', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse(Setting::enabled('member_registration_enabled'));
        $this->assertFalse(Setting::enabled('organisation_registration_enabled'));
    }

    public function test_admin_can_hide_the_login_button_without_disabling_login(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(Einstellungen::class)
            ->assertSet('login_button_enabled', true)
            ->set('login_button_enabled', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse(Setting::enabled('login_button_enabled'));
        $this->get(route('home'))
            ->assertDontSee('href="'.route('member.login').'"', false)
            ->assertDontSee('Anmelden');
        $this->get(route('member.login'))->assertOk();
    }

    public function test_member_registration_can_be_disabled_independently(): void
    {
        Setting::set('member_registration_enabled', '0');

        $this->get(route('member.register'))->assertNotFound();
        $this->post(route('member.register.store'))->assertNotFound();
        $this->get(route('member.register.danke'))->assertNotFound();
        $this->get(route('registrierung.schritt1'))->assertOk();

        $this->get(route('home'))
            ->assertDontSee('Benefits als Mitglied sichern')
            ->assertSee('Verein anmelden');

        $this->get(route('member.login'))
            ->assertDontSee('Jetzt registrieren');

        $organisation = Organisation::withoutEvents(fn (): Organisation => Organisation::create([
            'type' => 'verein',
            'role' => 'org_admin',
            'name' => 'Testverein ohne Mitglieder-Anmeldung',
            'email' => 'testverein@example.test',
            'password' => 'test-password',
            'is_approved' => true,
            'is_active' => true,
        ]));

        $this->get(route('organisations.show', $organisation))
            ->assertDontSee('Benefits als Mitglied sichern');
    }

    public function test_organisation_registration_can_be_disabled_independently(): void
    {
        Setting::set('organisation_registration_enabled', '0');

        $this->get(route('registrierung.schritt1'))->assertNotFound();
        $this->post(route('registrierung.schritt1.post'))->assertNotFound();
        $this->get(route('registrierung.schritt2'))->assertNotFound();
        $this->post(route('registrierung.schritt2.post'))->assertNotFound();
        $this->get(route('registrierung.schritt3'))->assertNotFound();
        $this->post(route('registrierung.schritt3.post'))->assertNotFound();
        $this->get(route('registrierung.danke'))->assertNotFound();
        $this->get(route('member.register'))->assertOk();

        $this->get(route('home'))
            ->assertDontSee('Verein anmelden')
            ->assertDontSee('Jetzt Verein registrieren')
            ->assertSee('Benefits als Mitglied sichern')
            ->assertDontSee('href="'.route('registrierung.schritt1').'"', false);

        $this->get(route('member.register'))
            ->assertDontSee('Du möchtest einen Verein anmelden?')
            ->assertDontSee('href="'.route('registrierung.schritt1').'"', false);
    }

    public function test_organisation_registration_requires_zvr_number(): void
    {
        $this->post(route('registrierung.schritt1.post'), [
            'name' => 'Verein ohne ZVR',
            'type' => 'verein',
            'description' => 'Test',
        ])->assertSessionHasErrors('zvr_number');
    }

    public function test_initiative_registration_does_not_require_zvr_number(): void
    {
        $this->post(route('registrierung.schritt1.post'), [
            'name' => 'Initiative ohne ZVR',
            'type' => 'organisation',
            'description' => 'Test',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('registrierung.schritt2'));
    }

    public function test_organisation_registration_requires_description(): void
    {
        $this->post(route('registrierung.schritt1.post'), [
            'name' => 'Verein ohne Beschreibung',
            'type' => 'verein',
            'zvr_number' => '123456789',
        ])->assertSessionHasErrors('description');
    }

    public function test_organisation_registration_requires_phone_number(): void
    {
        $this
            ->withSession([
                'reg_data' => [
                    'name' => 'Testverein',
                    'type' => 'verein',
                    'zvr_number' => '123456789',
                    'email' => 'verein@example.test',
                    'password' => 'test-password',
                ],
            ])
            ->post(route('registrierung.schritt3.post'), [])
            ->assertSessionHasErrors('phone');
    }

    public function test_organisation_registration_requires_contact_person(): void
    {
        $this
            ->withSession([
                'reg_data' => [
                    'name' => 'Testverein',
                    'type' => 'verein',
                    'zvr_number' => '123456789',
                    'description' => 'Testbeschreibung',
                    'email' => 'verein@example.test',
                    'password' => 'test-password',
                ],
            ])
            ->post(route('registrierung.schritt3.post'), [
                'phone' => '+43 463 123456',
            ])
            ->assertSessionHasErrors('contact_person');
    }

    public function test_organisation_registration_requires_representative(): void
    {
        $this
            ->withSession([
                'reg_data' => [
                    'name' => 'Testverein',
                    'type' => 'verein',
                    'zvr_number' => '123456789',
                    'description' => 'Testbeschreibung',
                    'email' => 'verein@example.test',
                    'password' => 'test-password',
                ],
            ])
            ->post(route('registrierung.schritt3.post'), [
                'phone' => '+43 463 123456',
                'contact_person' => 'Erika Muster',
            ])
            ->assertSessionHasErrors('representative');
    }
}
