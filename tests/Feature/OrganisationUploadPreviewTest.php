<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganisationResource\Pages\EditOrganisation;
use App\Models\Organisation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class OrganisationUploadPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_logo_uses_a_same_origin_preview_url(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('organisations/logos/test-logo.png', 'test-image-content');

        $admin = User::factory()->create(['is_admin' => true]);
        $organisation = Organisation::withoutEvents(
            fn (): Organisation => Organisation::create([
                'type' => 'verein',
                'role' => 'org_admin',
                'name' => 'Test-Organisation',
                'email' => 'organisation@example.test',
                'password' => 'test-password',
                'logo_path' => 'organisations/logos/test-logo.png',
                'is_approved' => true,
                'is_active' => true,
            ])
        );

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $form = Livewire::test(EditOrganisation::class, ['record' => $organisation->getRouteKey()])
            ->instance()
            ->form;
        $component = collect($form->getFlatComponents())
            ->first(fn ($component): bool => method_exists($component, 'getName')
                && $component->getName() === 'logo_path');
        $uploadedFile = collect($component->getUploadedFiles())->first();

        $this->assertSame('/storage/organisations/logos/test-logo.png', $uploadedFile['url']);
        $this->assertSame('test-logo.png', $uploadedFile['name']);
    }
}
