<?php

namespace Tests\Feature;

use App\Mail\MemberPasswordResetCodeMail;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MemberPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_reset_password_with_emailed_six_digit_code(): void
    {
        Mail::fake();

        $member = Member::create([
            'first_name' => 'Reset',
            'last_name' => 'Person',
            'email' => 'reset@example.test',
            'password' => 'old-password',
            'status' => 'approved',
        ]);

        $this->post(route('member.forgot.post'), [
            'email' => 'reset@example.test',
        ])->assertRedirect(route('member.reset.code'));

        $code = null;
        Mail::assertSent(MemberPasswordResetCodeMail::class, function (MemberPasswordResetCodeMail $mail) use (&$code) {
            $code = $mail->code;

            return $mail->hasTo('reset@example.test')
                && preg_match('/^\d{6}$/', $mail->code) === 1;
        });

        $this->assertNotNull($code);
        $this->assertTrue(Hash::check(
            $code,
            DB::table('member_password_reset_codes')->where('email', 'reset@example.test')->value('code_hash'),
        ));

        $this->get(route('member.reset.code'))->assertOk()->assertSee('Sechsstelliger Code');

        $this->post(route('member.reset.code.post'), [
            'code' => '000000',
        ])->assertSessionHasErrors('code');

        $this->post(route('member.reset.code.post'), [
            'code' => $code,
        ])->assertRedirect(route('member.reset.password'));

        $this->get(route('member.reset.password'))->assertOk()->assertSee('Neues Passwort');

        $this->post(route('member.reset.password.post'), [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('member.login'));

        $this->assertTrue(Hash::check('new-secure-password', $member->fresh()->password));
        $this->assertDatabaseMissing('member_password_reset_codes', [
            'email' => 'reset@example.test',
        ]);
    }

    public function test_unknown_email_receives_the_same_public_response_without_mail(): void
    {
        Mail::fake();

        $this->post(route('member.forgot.post'), [
            'email' => 'unknown@example.test',
        ])
            ->assertRedirect(route('member.reset.code'))
            ->assertSessionHas('status');

        Mail::assertNothingSent();
    }

    public function test_mail_transport_failure_does_not_leave_request_hanging_or_code_usable(): void
    {
        $member = Member::create([
            'first_name' => 'Mail',
            'last_name' => 'Failure',
            'email' => 'mail-failure@example.test',
            'password' => 'old-password',
            'status' => 'approved',
        ]);

        Mail::shouldReceive('to')
            ->once()
            ->with($member->email)
            ->andThrow(new \RuntimeException('Mail transport unavailable.'));

        $this->post(route('member.forgot.post'), [
            'email' => $member->email,
        ])
            ->assertRedirect(route('member.reset.code'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('member_password_reset_codes', [
            'email' => $member->email,
        ]);
    }

    public function test_resetting_an_admin_member_password_updates_the_filament_login(): void
    {
        Mail::fake();

        $member = Member::create([
            'first_name' => 'Admin',
            'last_name' => 'Reset',
            'email' => 'admin-reset@example.test',
            'password' => 'old-admin-password',
            'status' => 'approved',
            'role' => 'admin',
        ]);

        $this->post(route('member.forgot.post'), [
            'email' => $member->email,
        ]);

        $code = null;
        Mail::assertSent(MemberPasswordResetCodeMail::class, function (MemberPasswordResetCodeMail $mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $this->post(route('member.reset.code.post'), ['code' => $code]);
        $this->post(route('member.reset.password.post'), [
            'password' => 'new-admin-password',
            'password_confirmation' => 'new-admin-password',
        ])->assertRedirect(route('member.login'));

        $adminUser = User::query()->where('member_id', $member->id)->firstOrFail();
        $this->assertTrue(Hash::check('new-admin-password', $adminUser->password));
    }

    public function test_expired_code_cannot_be_used(): void
    {
        Mail::fake();

        Member::create([
            'first_name' => 'Expired',
            'last_name' => 'Code',
            'email' => 'expired@example.test',
            'password' => 'old-password',
            'status' => 'approved',
        ]);

        $this->post(route('member.forgot.post'), [
            'email' => 'expired@example.test',
        ])->assertRedirect(route('member.reset.code'));

        DB::table('member_password_reset_codes')
            ->where('email', 'expired@example.test')
            ->update(['expires_at' => now()->subMinute()]);

        $this->post(route('member.reset.code.post'), [
            'code' => '123456',
        ])->assertSessionHasErrors('code');

        $this->assertDatabaseMissing('member_password_reset_codes', [
            'email' => 'expired@example.test',
        ]);
    }
}
