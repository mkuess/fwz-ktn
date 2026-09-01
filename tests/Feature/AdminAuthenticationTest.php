<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_admin_member_is_synchronized_with_filament_user(): void
    {
        $member = Member::create([
            'first_name' => 'Neue',
            'last_name' => 'Adminperson',
            'email' => 'new-admin@example.test',
            'password' => 'admin-password',
            'status' => 'approved',
            'role' => 'admin',
        ]);

        $user = User::where('member_id', $member->id)->firstOrFail();

        $this->assertTrue($user->is_admin);
        $this->assertSame($member->email, $user->email);
        $this->assertTrue(Hash::check('admin-password', $user->password));
        $this->assertTrue($user->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_only_admin_users_can_access_filament(): void
    {
        $regularUser = User::factory()->create();
        $adminUser = User::factory()->create(['is_admin' => true]);

        $this->assertFalse($regularUser->canAccessPanel(Filament::getPanel('admin')));
        $this->assertTrue($adminUser->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_admin_member_cannot_use_the_regular_member_login(): void
    {
        Member::create([
            'first_name' => 'Nur',
            'last_name' => 'Verwaltung',
            'email' => 'verwaltung@example.test',
            'password' => 'admin-password',
            'status' => 'approved',
            'role' => 'admin',
        ]);

        $this->post(route('member.login.post'), [
            'email' => 'verwaltung@example.test',
            'password' => 'admin-password',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest('member');
    }

    public function test_removing_admin_role_revokes_filament_access(): void
    {
        $member = Member::create([
            'first_name' => 'Ehemalige',
            'last_name' => 'Adminperson',
            'email' => 'former-admin@example.test',
            'password' => 'admin-password',
            'status' => 'approved',
            'role' => 'admin',
        ]);

        $member->update(['role' => 'member']);

        $this->assertFalse($member->adminUser()->firstOrFail()->is_admin);
    }

    public function test_force_deleting_admin_member_does_not_recreate_admin_user(): void
    {
        $member = Member::create([
            'first_name' => 'Gelöschte',
            'last_name' => 'Adminperson',
            'email' => 'deleted-admin@example.test',
            'password' => 'admin-password',
            'status' => 'approved',
            'role' => 'admin',
        ]);
        $user = User::where('member_id', $member->id)->firstOrFail();

        $member->forceDelete();

        $user->refresh();
        $this->assertNull($user->member_id);
        $this->assertFalse($user->is_admin);
        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }
}
