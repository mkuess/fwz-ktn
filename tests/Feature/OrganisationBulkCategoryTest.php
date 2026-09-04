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

class OrganisationBulkCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_category_assignment_adds_the_category_without_removing_existing_ones(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $existingCategory = Category::create([
            'name' => 'Bestehend',
            'slug' => 'bestehend',
            'sort_order' => 1,
        ]);
        $assignedCategory = Category::create([
            'name' => 'Neu zugewiesen',
            'slug' => 'neu-zugewiesen',
            'sort_order' => 2,
        ]);
        $organisations = collect([1, 2])->map(fn (int $number): Organisation => Organisation::withoutEvents(
            fn (): Organisation => Organisation::create([
                'type' => 'verein',
                'role' => 'org_admin',
                'name' => 'Bulk-Testverein '.$number,
                'email' => 'bulk-test-'.$number.'@example.test',
                'password' => 'test-password',
                'is_approved' => true,
                'is_active' => true,
            ])
        ));
        $organisations[0]->categories()->attach($existingCategory);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(ListOrganisations::class)
            ->assertTableColumnExists('categories.name')
            ->assertTableColumnDoesNotExist('email')
            ->assertSee('Bestehend')
            ->callTableBulkAction(
                'assignCategory',
                $organisations,
                ['category_id' => $assignedCategory->id]
            )
            ->assertHasNoErrors();

        $this->assertTrue($organisations[0]->categories()->whereKey($existingCategory->id)->exists());
        $this->assertTrue($organisations[0]->categories()->whereKey($assignedCategory->id)->exists());
        $this->assertTrue($organisations[1]->categories()->whereKey($assignedCategory->id)->exists());
    }
}
