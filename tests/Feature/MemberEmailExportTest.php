<?php

namespace Tests\Feature;

use App\Filament\Resources\MemberResource\Pages\ListMembers;
use App\Models\Member;
use App\Models\Organisation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MemberEmailExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_export_uses_current_table_filters_and_search(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->createMember('pending', 'member', 'pending-member@example.test');
        $this->createMember('approved', 'member', 'approved-member@example.test');
        $this->createMember('approved', 'org_admin', 'approved-org-admin@example.test');
        $this->createMember('rejected', 'admin', 'rejected-admin@example.test');
        $this->createMember('approved', 'member', 'APPROVED-MEMBER@example.test');
        $this->createMember('approved', 'member', 'ungueltig');

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $list = Livewire::test(ListMembers::class)
            ->assertActionExists('exportEmailAddresses')
            ->mountAction('exportEmailAddresses')
            ->assertSet(
                'emailExportAddresses',
                'approved-member@example.test; approved-org-admin@example.test; pending-member@example.test; rejected-admin@example.test'
            )
            ->assertDontSee('member-email-export-status')
            ->assertDontSee('member-email-export-role')
            ->assertSee('E-Mail-Adressen kopieren');

        $list->unmountAction()
            ->filterTable('status', 'approved')
            ->mountAction('exportEmailAddresses')
            ->assertSet(
                'emailExportAddresses',
                'approved-member@example.test; approved-org-admin@example.test'
            );

        $list->unmountAction()
            ->filterTable('role', 'member')
            ->mountAction('exportEmailAddresses')
            ->assertSet('emailExportAddresses', 'approved-member@example.test');

        $list->unmountAction()
            ->searchTable('org-admin')
            ->mountAction('exportEmailAddresses')
            ->assertSet('emailExportAddresses', '');

        $list->unmountAction()
            ->searchTable(null)
            ->filterTable('status', null)
            ->filterTable('role', null)
            ->searchTable('pending-member@example.test')
            ->mountAction('exportEmailAddresses')
            ->assertSet('emailExportAddresses', 'pending-member@example.test');
    }

    public function test_email_export_respects_organisation_card_and_trashed_filters_across_pages(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $organisation = Organisation::create([
            'type' => 'verein',
            'role' => 'org_admin',
            'name' => 'Exportverein',
            'email' => 'exportverein@example.test',
            'password' => 'temporary-password',
        ]);

        foreach (range(1, 12) as $number) {
            $member = $this->createMember(
                'approved',
                'member',
                sprintf('mitglied-%02d@example.test', $number)
            );
            $member->update([
                'organisation_id' => $organisation->id,
                'card_status' => 'zugesendet',
            ]);
        }

        $other = $this->createMember('approved', 'member', 'andere-organisation@example.test');
        $other->update(['card_status' => 'zugesendet']);
        $deleted = $this->createMember('approved', 'member', 'geloescht@example.test');
        $deleted->update(['organisation_id' => $organisation->id, 'card_status' => 'zugesendet']);
        $deleted->delete();

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $list = Livewire::test(ListMembers::class)
            ->set('tableRecordsPerPage', 10)
            ->filterTable('organisation_id', $organisation->id)
            ->filterTable('card_status', 'zugesendet')
            ->mountAction('exportEmailAddresses');

        $this->assertCount(12, explode('; ', $list->get('emailExportAddresses')));
        $list->assertSet(
            'emailExportAddresses',
            implode('; ', array_map(
                fn (int $number): string => sprintf('mitglied-%02d@example.test', $number),
                range(1, 12)
            ))
        );

        $activeAddresses = $list->get('emailExportAddresses');
        $list->unmountAction()
            ->filterTable('trashed', true)
            ->mountAction('exportEmailAddresses')
            ->assertSet('emailExportAddresses', 'geloescht@example.test; '.$activeAddresses);
    }

    private function createMember(string $status, string $role, string $email): Member
    {
        return Member::create([
            'first_name' => 'Export',
            'last_name' => 'Test',
            'email' => $email,
            'password' => 'temporary-password',
            'status' => $status,
            'role' => $role,
        ]);
    }
}
