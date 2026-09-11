<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganisationResource\Pages\ListOrganisations;
use App\Models\Category;
use App\Models\Organisation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrganisationEmailExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_export_filters_by_type_and_category_and_removes_invalid_duplicates(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $sport = Category::create(['name' => 'Sport', 'slug' => 'sport']);
        $culture = Category::create(['name' => 'Kultur', 'slug' => 'kultur']);

        $matching = $this->createOrganisation('organisation', 'Sport Organisation', 'SPORT@example.test');
        $matching->categories()->attach($sport);

        $duplicate = $this->createOrganisation('organisation', 'Doppelte Organisation', 'sport@example.test');
        $duplicate->categories()->attach($sport);

        $otherCategory = $this->createOrganisation('organisation', 'Kultur Organisation', 'kultur@example.test');
        $otherCategory->categories()->attach($culture);

        $club = $this->createOrganisation('verein', 'Sport Verein', 'verein@example.test');
        $club->categories()->attach($sport);

        $invalid = $this->createOrganisation('organisation', 'Ohne gültige E-Mail', 'ungueltig');
        $invalid->categories()->attach($sport);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(ListOrganisations::class)
            ->assertActionExists('exportEmailAddresses')
            ->mountAction('exportEmailAddresses')
            ->assertSet('emailExportAddresses', 'kultur@example.test, sport@example.test')
            ->set('emailExportCategoryId', (string) $sport->id)
            ->assertSet('emailExportAddresses', 'sport@example.test')
            ->set('emailExportType', 'verein')
            ->assertSet('emailExportAddresses', 'verein@example.test')
            ->assertSee('E-Mail-Adressen kopieren');
    }

    private function createOrganisation(string $type, string $name, string $email): Organisation
    {
        return Organisation::create([
            'type' => $type,
            'role' => 'org_admin',
            'name' => $name,
            'email' => $email,
            'password' => 'temporary-password',
            'is_approved' => true,
            'is_active' => true,
        ]);
    }
}
