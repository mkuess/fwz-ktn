<?php

namespace Tests\Feature;

use App\Filament\Resources\MemberResource\Pages\ListMembers;
use App\Models\Member;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MemberListAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_list_displays_address_instead_of_registration_information(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Member::create([
            'first_name' => 'Adress',
            'last_name' => 'Test',
            'email' => 'adress-liste@example.test',
            'password' => 'temporary-password',
            'status' => 'approved',
            'role' => 'member',
            'street' => 'Musterstraße 12',
            'zip' => '9020',
            'city' => 'Klagenfurt',
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(ListMembers::class)
            ->assertTableColumnExists('address')
            ->assertTableColumnDoesNotExist('registration_info')
            ->assertSee('Musterstraße 12')
            ->assertSee('9020 Klagenfurt');
    }
}
