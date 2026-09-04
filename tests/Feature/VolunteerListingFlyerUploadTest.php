<?php

namespace Tests\Feature;

use App\Filament\Resources\VolunteerListingResource\Pages\EditVolunteerListing;
use App\Models\Organisation;
use App\Models\User;
use App\Models\VolunteerListing;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class VolunteerListingFlyerUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_a_flyer(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);
        $listing = $this->createListing();

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(EditVolunteerListing::class, ['record' => $listing->getRouteKey()])
            ->fillForm([
                'flyer_path' => UploadedFile::fake()->create('flyer.pdf', 200, 'application/pdf'),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $flyerPath = $listing->refresh()->flyer_path;

        $this->assertNotNull($flyerPath);
        Storage::disk('public')->assertExists($flyerPath);
    }

    public function test_existing_flyer_uses_a_same_origin_url(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('volunteer-listings/flyers/test-flyer.pdf', 'test-pdf-content');

        $admin = User::factory()->create(['is_admin' => true]);
        $listing = $this->createListing([
            'flyer_path' => 'volunteer-listings/flyers/test-flyer.pdf',
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $form = Livewire::test(EditVolunteerListing::class, ['record' => $listing->getRouteKey()])
            ->instance()
            ->form;
        $component = collect($form->getFlatComponents())
            ->first(fn ($component): bool => method_exists($component, 'getName')
                && $component->getName() === 'flyer_path');
        $uploadedFile = collect($component->getUploadedFiles())->first();

        $this->assertSame('/storage/volunteer-listings/flyers/test-flyer.pdf', $uploadedFile['url']);
        $this->assertSame('test-flyer.pdf', $uploadedFile['name']);
    }

    private function createListing(array $attributes = []): VolunteerListing
    {
        $organisation = Organisation::withoutEvents(
            fn (): Organisation => Organisation::create([
                'type' => 'verein',
                'role' => 'org_admin',
                'name' => 'Flyer-Testorganisation',
                'email' => fake()->unique()->safeEmail(),
                'password' => 'test-password',
                'is_approved' => true,
                'is_active' => true,
            ])
        );

        return VolunteerListing::create(array_merge([
            'organisation_id' => $organisation->id,
            'title' => 'Flyer-Testgesuch',
            'description' => 'Beschreibung',
            'is_spontaneous' => false,
            'is_active' => true,
        ], $attributes));
    }
}
