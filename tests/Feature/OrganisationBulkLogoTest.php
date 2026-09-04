<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganisationResource\Pages\ListOrganisations;
use App\Models\Organisation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class OrganisationBulkLogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_logo_assignment_replaces_existing_logos(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);
        $organisations = collect([1, 2])->map(fn (int $number): Organisation => Organisation::withoutEvents(
            fn (): Organisation => Organisation::create([
                'type' => 'verein',
                'role' => 'org_admin',
                'name' => 'Logo-Testverein '.$number,
                'email' => 'logo-test-'.$number.'@example.test',
                'password' => 'test-password',
                'logo_path' => 'organisations/logos/old-'.$number.'.png',
                'is_approved' => true,
                'is_active' => true,
            ])
        ));

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(ListOrganisations::class)
            ->assertTableBulkActionExists('assignLogo')
            ->callTableBulkAction(
                'assignLogo',
                $organisations,
                ['logo_path' => UploadedFile::fake()->image('gemeinsames-logo.png')]
            )
            ->assertHasNoErrors();

        $assignedPaths = $organisations
            ->map(fn (Organisation $organisation): ?string => $organisation->refresh()->logo_path);

        $this->assertCount(2, $assignedPaths->unique());
        $this->assertNotSame('organisations/logos/old-1.png', $assignedPaths->first());
        $assignedPaths->each(
            fn (string $path) => Storage::disk('public')->assertExists($path)
        );
    }
}
