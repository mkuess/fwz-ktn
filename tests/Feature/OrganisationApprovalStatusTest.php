<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganisationResource\Pages\EditOrganisation;
use App\Models\Organisation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrganisationApprovalStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejected_organisation_requires_and_saves_a_reason(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $organisation = Organisation::withoutEvents(fn (): Organisation => Organisation::create([
            'type' => 'verein',
            'role' => 'org_admin',
            'name' => 'Ablehnungs-Testverein',
            'email' => 'ablehnung@example.test',
            'password' => 'test-password',
            'is_approved' => false,
            'is_active' => true,
        ]));

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(EditOrganisation::class, ['record' => $organisation->getRouteKey()])
            ->assertFormSet(['approval_status' => 'pending'])
            ->fillForm(['approval_status' => 'rejected'])
            ->call('save')
            ->assertHasFormErrors(['rejection_reason'])
            ->fillForm([
                'approval_status' => 'rejected',
                'rejection_reason' => 'Die Organisation erfüllt die Voraussetzungen nicht.',
            ])
            ->call('save')
            ->assertHasNoErrors();

        $organisation->refresh();

        $this->assertSame('rejected', $organisation->approval_status);
        $this->assertFalse($organisation->is_approved);
        $this->assertSame(
            'Die Organisation erfüllt die Voraussetzungen nicht.',
            $organisation->rejection_reason
        );
        $this->assertNull($organisation->approved_at);
    }
}
