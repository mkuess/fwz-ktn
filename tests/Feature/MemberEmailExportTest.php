<?php

namespace Tests\Feature;

use App\Filament\Resources\MemberResource\Pages\ListMembers;
use App\Models\Member;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MemberEmailExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_export_filters_members_by_status_and_role(): void
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

        Livewire::test(ListMembers::class)
            ->assertActionExists('exportEmailAddresses')
            ->mountAction('exportEmailAddresses')
            ->assertSet(
                'emailExportAddresses',
                'approved-member@example.test, approved-org-admin@example.test, pending-member@example.test, rejected-admin@example.test'
            )
            ->set('emailExportStatus', 'approved')
            ->assertSet(
                'emailExportAddresses',
                'approved-member@example.test, approved-org-admin@example.test'
            )
            ->set('emailExportRole', 'member')
            ->assertSet('emailExportAddresses', 'approved-member@example.test')
            ->set('emailExportStatus', null)
            ->assertSet('emailExportAddresses', 'approved-member@example.test, pending-member@example.test')
            ->assertSee('E-Mail-Adressen kopieren');
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
