<?php

namespace Tests\Feature;

use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganisationPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organisation_index_has_one_german_styled_pagination(): void
    {
        foreach (range(1, 13) as $number) {
            $this->createOrganisation($number);
        }

        $response = $this->get(route('organisations.index'));
        $content = $response->getContent();

        $response->assertOk()
            ->assertSee('Seitennavigation')
            ->assertSee('Zeige')
            ->assertSee('bis')
            ->assertSee('von')
            ->assertSee('Ergebnisse')
            ->assertSee('Zurück')
            ->assertSee('Weiter')
            ->assertSee('Gehe zu Seite 2')
            ->assertDontSee('Previous')
            ->assertDontSee('Next')
            ->assertDontSee('Showing');

        $this->assertSame(1, substr_count($content, 'class="pagination"'));
    }

    public function test_organisation_pagination_uses_german_labels_on_the_next_page(): void
    {
        foreach (range(1, 13) as $number) {
            $this->createOrganisation($number);
        }

        $this->get(route('organisations.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Zeige')
            ->assertSee('13')
            ->assertSee('von')
            ->assertSee('Ergebnisse')
            ->assertSee('Zurück')
            ->assertDontSee('Previous')
            ->assertDontSee('Next');
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
