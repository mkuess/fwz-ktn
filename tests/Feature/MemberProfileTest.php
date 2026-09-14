<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_member_can_view_profile_form_and_organisation_is_read_only(): void
    {
        $organisation = Organisation::create([
            'type' => 'verein',
            'role' => 'org_admin',
            'name' => 'Testverein',
            'email' => 'verein@example.test',
            'password' => 'temporary-password',
            'is_approved' => true,
            'is_active' => true,
        ]);
        $member = $this->createMember($organisation);

        $this->actingAs($member, 'member')
            ->get(route('member.profile.edit'))
            ->assertOk()
            ->assertSee('Daten ändern')
            ->assertSee('class="btn primary"', false)
            ->assertSee('Testverein')
            ->assertSee('readonly', false);
    }

    public function test_member_can_update_name_and_address_without_changing_protected_data(): void
    {
        $organisation = Organisation::create([
            'type' => 'verein',
            'role' => 'org_admin',
            'name' => 'Unveränderter Verein',
            'email' => 'verein@example.test',
            'password' => 'temporary-password',
            'is_approved' => true,
            'is_active' => true,
        ]);
        $member = $this->createMember($organisation);

        $this->actingAs($member, 'member')
            ->patch(route('member.profile.update'), [
                'first_name' => 'Neuer',
                'last_name' => 'Name',
                'street' => 'Neue Straße 12',
                'zip' => '9020',
                'city' => 'Klagenfurt',
                'organisation_id' => null,
                'email' => 'manipuliert@example.test',
                'role' => 'admin',
            ])
            ->assertRedirect(route('member.portal'))
            ->assertSessionHas('success');

        $member->refresh();
        $this->assertSame('Neuer', $member->first_name);
        $this->assertSame('Name', $member->last_name);
        $this->assertSame('Neue Straße 12', $member->street);
        $this->assertSame('9020', $member->zip);
        $this->assertSame('Klagenfurt', $member->city);
        $this->assertSame($organisation->id, $member->organisation_id);
        $this->assertSame('mitglied@example.test', $member->email);
        $this->assertSame('member', $member->role);
    }

    public function test_member_can_optionally_change_password_with_current_password(): void
    {
        $member = $this->createMember();

        $this->actingAs($member, 'member')
            ->patch(route('member.profile.update'), [
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'street' => $member->street,
                'zip' => $member->zip,
                'city' => $member->city,
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('member.portal'));

        $this->assertTrue(Hash::check('new-password', $member->refresh()->password));
    }

    public function test_password_change_rejects_incorrect_current_password(): void
    {
        $member = $this->createMember();

        $this->actingAs($member, 'member')
            ->from(route('member.profile.edit'))
            ->patch(route('member.profile.update'), [
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'street' => $member->street,
                'zip' => $member->zip,
                'city' => $member->city,
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('member.profile.edit'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $member->refresh()->password));
    }

    public function test_guests_cannot_edit_member_data(): void
    {
        $this->get(route('member.profile.edit'))
            ->assertRedirect(route('member.login'));
    }

    private function createMember(?Organisation $organisation = null): Member
    {
        return Member::create([
            'organisation_id' => $organisation?->id,
            'first_name' => 'Altes',
            'last_name' => 'Mitglied',
            'email' => 'mitglied@example.test',
            'password' => 'old-password',
            'status' => 'approved',
            'role' => 'member',
            'street' => 'Alte Straße 1',
            'zip' => '9500',
            'city' => 'Villach',
        ]);
    }
}
