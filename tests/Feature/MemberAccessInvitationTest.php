<?php

namespace Tests\Feature;

use App\Filament\Resources\MemberResource\Pages\EditMember;
use App\Mail\MemberApprovedMail;
use App\Models\Member;
use App\Models\User;
use App\Services\MemberAccessInvitationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class MemberAccessInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_member_receives_long_lived_code_and_signed_password_link(): void
    {
        Mail::fake();

        $member = Member::create([
            'first_name' => 'Freigabe',
            'last_name' => 'Test',
            'email' => 'freigabe@example.test',
            'password' => 'temporary-password',
            'status' => 'approved',
        ]);

        app(MemberAccessInvitationService::class)->send($member);

        $code = null;
        $resetUrl = null;

        Mail::assertSent(MemberApprovedMail::class, function (MemberApprovedMail $mail) use (&$code, &$resetUrl): bool {
            $code = $mail->code;
            $resetUrl = $mail->resetUrl;

            return $mail->hasTo('freigabe@example.test')
                && preg_match('/^\d{6}$/', $mail->code) === 1
                && $mail->lifetimeDays === 7;
        });

        $reset = DB::table('member_password_reset_codes')
            ->where('email', 'freigabe@example.test')
            ->first();

        $this->assertNotNull($reset);
        $this->assertTrue(Hash::check($code, $reset->code_hash));
        $this->assertTrue(now()->addDays(6)->lessThan($reset->expires_at));
        $this->assertNotNull($member->fresh()->membership_number);
        $this->assertNotNull($member->fresh()->activation_sent_at);

        $this->get($resetUrl)
            ->assertOk()
            ->assertSessionHas('member_password_reset_email', 'freigabe@example.test')
            ->assertSee('7 Tage');

        $this->post(route('member.reset.code.post'), [
            'code' => $code,
        ])->assertRedirect(route('member.reset.password'));
    }

    public function test_send_access_action_saves_form_before_sending_email(): void
    {
        Mail::fake();
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $member = Member::create([
            'first_name' => 'Noch',
            'last_name' => 'Ausstehend',
            'email' => 'aktion@example.test',
            'password' => 'temporary-password',
            'status' => 'pending',
        ]);

        Livewire::test(EditMember::class, ['record' => $member->getRouteKey()])
            ->set('data.status', 'approved')
            ->set('data.last_name', 'Gespeichert')
            ->assertSee('Zugangsdaten zusenden')
            ->call('saveAndSendAccessCredentials')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'status' => 'approved',
            'last_name' => 'Gespeichert',
        ]);

        Mail::assertSent(MemberApprovedMail::class, fn (MemberApprovedMail $mail): bool => $mail->hasTo('aktion@example.test'));
    }
}
