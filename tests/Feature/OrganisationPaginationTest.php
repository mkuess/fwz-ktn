<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganisationPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organisation_index_uses_scroll_loading_without_pagination_links(): void
    {
        foreach (range(1, 13) as $number) {
            $this->createOrganisation($number);
        }

        $response = $this->get(route('organisations.index'));
        $content = $response->getContent();

        $response->assertOk()
            ->assertSee('id="vereine-suche"', false)
            ->assertSee('data-infinite-scroll="true"', false)
            ->assertSee('data-has-more="true"', false)
            ->assertDontSee('id="vereine-listbox"', false)
            ->assertSee('Testverein 01')
            ->assertSee('Testverein 12')
            ->assertDontSee('Testverein 13')
            ->assertDontSee('Seitennavigation')
            ->assertDontSee('Gehe zu Seite 2');

        $this->assertSame(0, substr_count($content, 'class="pagination"'));
    }

    public function test_organisation_search_returns_the_next_batch_for_scroll_loading(): void
    {
        foreach (range(1, 13) as $number) {
            $this->createOrganisation($number);
        }

        $this->getJson(route('vereine.suche', ['page' => 2, 'limit' => 12]))
            ->assertOk()
            ->assertJsonPath('total', 13)
            ->assertJsonPath('page', 2)
            ->assertJsonPath('has_more', false)
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.name', 'Testverein 13');
    }

    public function test_category_filter_is_visible_and_filters_the_search_results(): void
    {
        $category = Category::create([
            'name' => 'Sport',
            'slug' => 'sport',
            'sort_order' => 1,
        ]);
        $sportOrganisation = $this->createOrganisation(1);
        $sportOrganisation->categories()->attach($category);
        $this->createOrganisation(2);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-kategorie="sport"', false);
        $this->get(route('organisations.index'))
            ->assertOk()
            ->assertSee('data-kategorie="sport"', false);
        $this->getJson(route('vereine.suche', ['kategorie' => 'sport']))
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.id', $sportOrganisation->id)
            ->assertJsonPath('results.0.categories.0', 'Sport');
    }

    private function createOrganisation(int $number): Organisation
    {
        return Organisation::withoutEvents(fn (): Organisation => Organisation::create([
            'type' => 'verein',
            'role' => 'org_admin',
            'name' => sprintf('Testverein %02d', $number),
            'email' => sprintf('verein-%02d@example.test', $number),
            'password' => 'test-password',
            'is_approved' => true,
            'is_active' => true,
        ]));
    }
}
