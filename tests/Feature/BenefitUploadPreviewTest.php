<?php

namespace Tests\Feature;

use App\Filament\Resources\BenefitResource\Pages\EditBenefit;
use App\Models\Benefit;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BenefitUploadPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_logo_uses_a_same_origin_preview_url(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('benefits/logos/test-logo.png', 'test-image-content');

        $admin = User::factory()->create(['is_admin' => true]);
        $benefit = Benefit::create([
            'name' => 'Test-Benefit',
            'description' => 'Testbeschreibung',
            'logo_path' => 'benefits/logos/test-logo.png',
            'is_active' => true,
            'is_teaser' => false,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $form = Livewire::test(EditBenefit::class, ['record' => $benefit->getRouteKey()])
            ->instance()
            ->form;
        $component = collect($form->getFlatComponents())
            ->first(fn ($component): bool => method_exists($component, 'getName')
                && $component->getName() === 'logo_path');
        $uploadedFile = collect($component->getUploadedFiles())->first();

        $this->assertSame('/storage/benefits/logos/test-logo.png', $uploadedFile['url']);
        $this->assertSame('test-logo.png', $uploadedFile['name']);
    }
}
