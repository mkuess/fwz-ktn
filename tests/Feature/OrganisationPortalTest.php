<?php

namespace Tests\Feature;

use App\Filament\Organisation\Pages\Dashboard;
use App\Filament\Organisation\Pages\Members;
use App\Filament\Organisation\Pages\Profile;
use App\Models\Category;
use App\Models\Member;
use App\Models\Organisation;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrganisationPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_organisation_can_login_using_existing_credentials_and_previous_login_is_preserved(): void
    {
        $organisation = $this->organisation('login');
        $lastLogin = now()->subDays(2)->startOfSecond();
        $organisation->forceFill(['last_login_at' => $lastLogin])->save();

        $this->post(route('member.login.post'), [
            'email' => $organisation->email,
            'password' => 'organisation-password',
        ])->assertRedirect('/verwaltung/organisation')
            ->assertSessionHas('organisation_login_since', $lastLogin->toDateTimeString());

        $this->assertAuthenticatedAs($organisation, 'organisation');
        $this->assertGuest('web');
        $this->assertGuest('member');
        $this->assertTrue($organisation->refresh()->last_login_at->greaterThan($lastLogin));
        $this->get('/verwaltung/organisation')->assertOk()->assertSee('Neue Anmeldungen');
        $this->get('/verwaltung/organisation/angemeldete-user')->assertOk()->assertSee('Angemeldete User');
        $this->get('/verwaltung/organisation/meine-daten')->assertOk()->assertSee('Meine Organisationsdaten');
        $this->get('/verwaltung/members')->assertRedirect();
        $this->get('/verwaltung/organisations')->assertRedirect();
        $this->assertFalse($organisation->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_inactive_unapproved_or_deleted_organisations_cannot_login(): void
    {
        $inactive = $this->organisation('inactive');
        $inactive->update(['is_active' => false]);
        $pending = $this->organisation('pending');
        $pending->update(['approval_status' => 'pending']);
        $deleted = $this->organisation('deleted');
        $deleted->delete();

        foreach ([$inactive, $pending, $deleted] as $organisation) {
            $this->post(route('member.login.post'), [
                'email' => $organisation->email,
                'password' => 'organisation-password',
            ])->assertSessionHasErrors('email');
            $this->assertGuest('organisation');
        }
    }

    public function test_organisation_dashboard_only_shows_own_members_since_previous_login(): void
    {
        $organisation = $this->organisation('dashboard');
        $other = $this->organisation('other');
        $old = $this->member($organisation, 'old');
        $old->forceFill(['created_at' => now()->subDays(3)])->save();
        $new = $this->member($organisation, 'new');
        $foreign = $this->member($other, 'foreign');
        $this->actingAs($organisation, 'organisation');
        Filament::setCurrentPanel(Filament::getPanel('organisation'));
        $this->withSession(['organisation_login_since' => now()->subDay()->toDateTimeString()]);

        Livewire::test(Dashboard::class)
            ->assertCanSeeTableRecords([$new])
            ->assertCanNotSeeTableRecords([$old, $foreign]);

        $this->withSession(['organisation_login_since' => null]);
        Livewire::test(Dashboard::class)
            ->assertCanSeeTableRecords([$old, $new])
            ->assertCanNotSeeTableRecords([$foreign]);
    }

    public function test_organisation_can_only_block_and_unblock_own_members(): void
    {
        $organisation = $this->organisation('blocking');
        $own = $this->member($organisation, 'own');
        $foreign = $this->member($this->organisation('foreign'), 'foreign');
        $this->actingAs($organisation, 'organisation');
        Filament::setCurrentPanel(Filament::getPanel('organisation'));

        Livewire::test(Members::class)
            ->assertCanSeeTableRecords([$own])
            ->assertCanNotSeeTableRecords([$foreign])
            ->callTableAction('block', $own)
            ->assertHasNoErrors();

        $this->assertTrue($own->refresh()->is_login_blocked);
        $this->assertFalse($foreign->refresh()->is_login_blocked);

        Livewire::test(Members::class)
            ->callTableAction('block', $foreign);
        $this->assertFalse($foreign->refresh()->is_login_blocked);

        Livewire::test(Members::class)
            ->callTableAction('unblock', $own)
            ->assertHasNoErrors();
        $this->assertFalse($own->refresh()->is_login_blocked);
        $this->assertSame('approved', $own->status);
    }

    public function test_blocked_member_cannot_login_or_use_existing_session(): void
    {
        $member = $this->member($this->organisation('blocked'), 'blocked');
        $member->update(['is_login_blocked' => true]);

        $this->post(route('member.login.post'), [
            'email' => $member->email,
            'password' => 'member-password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest('member');

        $this->actingAs($member, 'member')
            ->get(route('member.portal'))
            ->assertRedirect(route('member.login'));
        $this->assertGuest('member');
        $this->actingAs($member, 'member')
            ->patch(route('member.profile.update'), ['first_name' => 'Manipuliert'])
            ->assertRedirect(route('member.login'));
        $this->assertSame('Mitglied', $member->refresh()->first_name);
    }

    public function test_organisation_can_edit_only_own_profile_and_cannot_change_approval_or_roles(): void
    {
        $organisation = $this->organisation('profile');
        $foreign = $this->organisation('unchanged');
        $category = Category::create(['name' => 'Sport', 'slug' => 'sport']);
        $this->actingAs($organisation, 'organisation');
        Filament::setCurrentPanel(Filament::getPanel('organisation'));

        Livewire::test(Profile::class)
            ->fillForm([
                'name' => 'Neuer Vereinsname',
                'email' => $organisation->email,
                'city' => 'Klagenfurt',
                'zip' => '9020',
                'categories' => [$category->id],
            ])
            ->set('data.id', $foreign->id)
            ->set('data.is_active', false)
            ->set('data.is_approved', false)
            ->set('data.role', 'admin')
            ->call('save')
            ->assertHasNoFormErrors();

        $organisation->refresh();
        $this->assertSame('Neuer Vereinsname', $organisation->name);
        $this->assertTrue($organisation->is_active);
        $this->assertTrue($organisation->is_approved);
        $this->assertSame('org_admin', $organisation->role);
        $this->assertTrue($organisation->categories()->whereKey($category->id)->exists());
        $this->assertSame('Organisation unchanged', $foreign->refresh()->name);
    }

    public function test_access_is_revoked_for_existing_organisation_session_and_members_cannot_enter(): void
    {
        $organisation = $this->organisation('revoked');
        $this->actingAs($organisation, 'organisation');
        $organisation->update(['is_active' => false]);
        $this->get('/verwaltung/organisation')->assertForbidden();

        auth('organisation')->logout();
        $member = $this->member($this->organisation('member-only'), 'member-only');
        $this->actingAs($member, 'member');
        $this->get('/verwaltung/organisation')->assertRedirect('/verwaltung/organisation/login');
    }

    private function organisation(string $suffix): Organisation
    {
        return Organisation::create([
            'type' => 'verein',
            'role' => 'org_admin',
            'name' => 'Organisation '.$suffix,
            'email' => $suffix.'@organisation.example.test',
            'password' => 'organisation-password',
            'is_active' => true,
            'approval_status' => 'approved',
        ]);
    }

    private function member(Organisation $organisation, string $suffix): Member
    {
        return Member::create([
            'organisation_id' => $organisation->id,
            'first_name' => 'Mitglied',
            'last_name' => $suffix,
            'email' => $suffix.'@mitglied.example.test',
            'password' => 'member-password',
            'status' => 'approved',
            'role' => 'member',
        ]);
    }
}
