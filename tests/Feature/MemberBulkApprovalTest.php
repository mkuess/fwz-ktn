<?php

namespace Tests\Feature;

use App\Filament\Pages\Neuanmeldungen;
use App\Mail\MemberApprovedMail;
use App\Models\Member;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
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
        ]));

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(Neuanmeldungen::class)
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
}
