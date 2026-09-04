<?php

namespace Tests\Feature;

use App\Models\LoginLog;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_member_login_is_logged(): void
    {
        $member = Member::create([
            'first_name' => 'Erfolgreiche',
            'last_name' => 'Anmeldung',
            'email' => 'success@example.test',
            'password' => 'member-password',
            'status' => 'approved',
            'role' => 'member',
        ]);

        $this->withServerVariables([
            'REMOTE_ADDR' => '203.0.113.10',
            'HTTP_USER_AGENT' => 'LoginLogTestBrowser/1.0',
        ])->post(route('member.login.post'), [
            'email' => $member->email,
            'password' => 'member-password',
        ])->assertRedirect(route('member.portal'));

        $this->assertDatabaseHas('login_logs', [
            'member_id' => $member->id,
            'email' => $member->email,
            'member_name' => 'Erfolgreiche Anmeldung',
            'successful' => true,
            'failure_reason' => null,
            'ip_address' => '203.0.113.10',
        ]);
        $this->assertSame(
            'LoginLogTestBrowser/1.0',
            LoginLog::latest()->value('user_agent')
        );
    }

    public function test_failed_member_login_is_logged_with_a_problem(): void
    {
        $member = Member::create([
            'first_name' => 'Fehlgeschlagene',
            'last_name' => 'Anmeldung',
            'email' => 'failed@example.test',
            'password' => 'member-password',
            'status' => 'approved',
            'role' => 'member',
        ]);

        $this->post(route('member.login.post'), [
            'email' => $member->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseHas('login_logs', [
            'member_id' => $member->id,
            'email' => $member->email,
            'successful' => false,
            'failure_reason' => 'E-Mail oder Passwort ist falsch.',
        ]);
    }

    public function test_successful_admin_login_to_member_area_is_logged(): void
    {
        $member = Member::create([
            'first_name' => 'Admin',
            'last_name' => 'Login',
            'email' => 'admin-login@example.test',
            'password' => 'admin-password',
            'status' => 'approved',
            'role' => 'admin',
        ]);

        $this->post(route('member.login.post'), [
            'email' => $member->email,
            'password' => 'admin-password',
        ])->assertRedirect(route('member.portal'));

        $this->assertDatabaseHas('login_logs', [
            'member_id' => $member->id,
            'successful' => true,
            'failure_reason' => null,
        ]);
    }

    public function test_admin_can_open_the_login_log_in_filament(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/verwaltung/login-logs')
            ->assertOk()
            ->assertSee('Login Log');
    }
}
