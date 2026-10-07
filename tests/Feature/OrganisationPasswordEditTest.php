<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganisationResource\Pages\EditOrganisation;
use App\Models\Organisation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class OrganisationPasswordEditTest extends TestCase
{
    use RefreshDatabase;

    private function organisation(): Organisation
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        return Organisation::create([
            'type' => 'verein',
            'name' => 'Passwort-Testverein',
            'email' => 'password-edit@example.test',
            'password' => 'old-password',
            'approval_status' => 'approved',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_set_new_password_and_organisation_can_login_with_it(): void
    {
        $organisation = $this->organisation();

        Livewire::test(EditOrganisation::class, ['record' => $organisation->getRouteKey()])
            ->assertFormSet(['password' => ''])
            ->assertSee('Neues Passwort')
            ->fillForm(['password' => 'new-organisation-password'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('new-organisation-password', $organisation->refresh()->password));
        $this->assertFalse(Hash::check('old-password', $organisation->password));

        $this->post(route('member.login.post'), [
            'email' => $organisation->email,
            'password' => 'new-organisation-password',
        ])->assertRedirect('/verwaltung/organisation');
        $this->assertAuthenticatedAs($organisation, 'organisation');
    }

    public function test_empty_password_preserves_existing_password(): void
    {
        $organisation = $this->organisation();
        $oldHash = $organisation->password;

        Livewire::test(EditOrganisation::class, ['record' => $organisation->getRouteKey()])
            ->fillForm(['name' => 'Geänderter Name', 'password' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($oldHash, $organisation->refresh()->password);
        $this->assertSame('Geänderter Name', $organisation->name);
    }

    public function test_too_short_password_is_rejected_without_changing_existing_password(): void
    {
        $organisation = $this->organisation();
        $oldHash = $organisation->password;

        Livewire::test(EditOrganisation::class, ['record' => $organisation->getRouteKey()])
            ->fillForm(['password' => 'short'])
            ->call('save')
            ->assertHasFormErrors(['password' => 'min']);

        $this->assertSame($oldHash, $organisation->refresh()->password);
    }
}
