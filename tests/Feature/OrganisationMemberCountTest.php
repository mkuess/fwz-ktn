<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganisationResource\Pages\ListOrganisations;
use App\Models\Member;
use App\Models\Organisation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrganisationMemberCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_count_includes_all_registration_states_but_not_deleted_members(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $organisations = collect(['Mit Mitgliedern', 'Ohne Mitglieder'])->map(
            fn (string $name, int $index): Organisation => Organisation::create([
                'type' => 'verein',
                'name' => $name,
                'email' => 'count-'.$index.'@example.test',
                'password' => 'test-password',
            ])
        );

        foreach (['pending', 'approved', 'rejected', 'deleted'] as $index => $status) {
            $member = Member::create([
                'organisation_id' => $organisations[0]->id,
                'first_name' => 'Test',
                'last_name' => 'Mitglied',
                'email' => 'count-member-'.$index.'@example.test',
                'password' => 'test-password',
                'status' => $status === 'deleted' ? 'approved' : $status,
                'is_login_blocked' => $status === 'approved',
            ]);
            if ($status === 'deleted') {
                $member->delete();
            }
        }

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(ListOrganisations::class)
            ->assertTableColumnExists('members_count')
            ->assertTableColumnStateSet('members_count', 3, (string) $organisations[0]->id)
            ->assertTableColumnStateSet('members_count', 0, (string) $organisations[1]->id)
            ->sortTable('members_count', 'desc')
            ->assertCanSeeTableRecords($organisations, inOrder: true);
    }

    public function test_organisation_panel_uses_same_logo_as_fwz_administration(): void
    {
        $adminLogo = Filament::getPanel('admin')->getBrandLogo();
        $organisationLogo = Filament::getPanel('organisation')->getBrandLogo();

        $this->assertSame($adminLogo, $organisationLogo);
        $this->assertStringContainsString('/images/fwz_logo.svg', $organisationLogo);
        $this->assertFileExists(public_path('images/fwz_logo.svg'));
    }
}
