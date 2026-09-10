<?php

namespace Tests\Feature;

use App\Filament\Pages\Neuanmeldungen;
use App\Mail\MemberApprovedMail;
use App\Models\Member;
use App\Models\User;
use App\Services\MemberAccessInvitationService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class MemberBulkApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_members_can_be_approved_and_sent_access_credentials_in_bulk(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);

        $members = collect([1, 2])->map(fn (int $number): Member => Member::create([
            'first_name' => 'Bulk',
            'last_name' => 'Mitglied '.$number,
            'email' => 'bulk-mitglied-'.$number.'@example.test',
            'password' => 'temporary-password',
            'status' => 'pending',
            'role' => 'member',
            'street' => $number === 1 ? 'Musterstraße 12' : null,
            'zip' => $number === 1 ? '9020' : null,
            'city' => $number === 1 ? 'Klagenfurt' : null,
        ]));

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(Neuanmeldungen::class)
            ->assertTableColumnExists('address')
            ->assertSee('Musterstraße 12')
            ->assertSee('9020 Klagenfurt')
            ->callTableBulkAction('approveAndSendAccess', $members)
            ->assertHasNoErrors();

        foreach ($members as $member) {
            $this->assertDatabaseHas('members', [
                'id' => $member->id,
                'status' => 'approved',
            ]);
            $this->assertDatabaseHas('member_password_reset_codes', [
                'email' => $member->email,
            ]);
        }

        $this->assertSame(2, DB::table('member_password_reset_codes')->count());
        Mail::assertSent(MemberApprovedMail::class, 2);
    }

    public function test_failed_bulk_email_is_reported_and_member_remains_pending(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $successfulMember = Member::create([
            'first_name' => 'Erfolgreich',
            'last_name' => 'Mitglied',
            'email' => 'erfolgreich@example.test',
            'password' => 'temporary-password',
            'status' => 'pending',
            'role' => 'member',
        ]);
        $failedMember = Member::create([
            'first_name' => 'Fehlerhaft',
            'last_name' => 'Mitglied',
            'email' => 'fehlerhaft@example.test',
            'password' => 'temporary-password',
            'status' => 'pending',
            'role' => 'member',
        ]);

        $invitationService = Mockery::mock(MemberAccessInvitationService::class);
        $invitationService
            ->shouldReceive('send')
            ->twice()
            ->andReturnUsing(function (Member $member): void {
                if ($member->email === 'fehlerhaft@example.test') {
                    throw new \RuntimeException('Test mail failure');
                }
            });
        $this->app->instance(MemberAccessInvitationService::class, $invitationService);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(Neuanmeldungen::class)
            ->callTableBulkAction('approveAndSendAccess', collect([$successfulMember, $failedMember]))
            ->assertHasNoErrors();

        $this->assertDatabaseHas('members', [
            'id' => $successfulMember->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('members', [
            'id' => $failedMember->id,
            'status' => 'pending',
            'approved_at' => null,
        ]);
        Notification::assertNotified(
            Notification::make()
                ->title('Freischaltung abgeschlossen')
                ->body('1 E-Mail(s) versendet, 1 E-Mail(s) fehlgeschlagen. Nicht freigegeben: fehlerhaft@example.test')
                ->warning()
                ->persistent()
        );
    }
}
