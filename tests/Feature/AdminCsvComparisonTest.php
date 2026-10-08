<?php

namespace Tests\Feature;

use App\Filament\Pages\CsvComparison;
use App\Models\Member;
use App\Models\Organisation;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class AdminCsvComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_organisations_by_name_or_city_and_deleted_records_are_excluded(): void
    {
        $org = $this->organisation('Suchverein', 'Klagenfurt');
        $deleted = $this->organisation('Gelöschter Suchverein', 'Klagenfurt');
        $deleted->delete();
        $this->loginAdmin();
        $component = Livewire::test(CsvComparison::class)
            ->assertSee('Verein / Organisation auswählen')
            ->assertDontSee('1. CSV hochladen');
        $field = $component->instance()->selectionForm->getFlatFields()['organisation_id'];
        $this->assertInstanceOf(Select::class, $field);
        foreach (['Suchverein', 'Klagenfurt'] as $search) {
            $results = $field->getSearchResults($search);
            $this->assertArrayHasKey($org->id, $results);
            $this->assertArrayNotHasKey($deleted->id, $results);
            $this->assertStringContainsString('Klagenfurt', $results[$org->id]);
        }
        $this->assertSame('Verwaltung', CsvComparison::getNavigationGroup());
        $this->assertSame('CSV Abgleich', CsvComparison::getNavigationLabel());
        $this->get(CsvComparison::getUrl(panel: 'admin'))->assertOk();
    }

    public function test_admin_comparison_uses_only_selected_organisation_and_switching_clears_the_result(): void
    {
        $first = $this->organisation('Erster Verein');
        $second = $this->organisation('Zweiter Verein');
        $match = $this->member($first, 'Anna', 'Muster', 'anna@example.test');
        $missing = $this->member($first, 'Fehlend', 'Mitglied', 'missing@example.test');
        $foreign = $this->member($second, 'Anderer', 'Verein', 'foreign@example.test');
        $before = Member::all()->toArray();
        $this->loginAdmin();
        $component = Livewire::test(CsvComparison::class)
            ->set('selectionData.organisation_id', (string) $first->id)
            ->assertSee('1. CSV hochladen')
            ->set('uploadData.csv', UploadedFile::fake()->createWithContent('members.csv', "Email;Vorname;Nachname\nanna@example.test;Anna;Muster\n"))
            ->call('prepare')->assertHasNoErrors()
            ->call('compare')->assertHasNoErrors()
            ->assertCanSeeTableRecords([$missing])
            ->assertCanNotSeeTableRecords([$match, $foreign]);
        $this->assertSame(2, $component->instance()->comparison['total']);
        $this->assertSame(1, $component->instance()->comparison['matched']);

        $component->set('selectionData.organisation_id', (string) $second->id)
            ->assertSet('headers', [])->assertSet('comparisonToken', null)
            ->assertDontSee('3. Ergebnis')
            ->set('uploadData.csv', [UploadedFile::fake()->createWithContent('second.csv', "Email;Vorname;Nachname\nother@example.test;Andere;Person\n")])
            ->call('prepare')->assertHasNoErrors()
            ->call('compare')->assertHasNoErrors()
            ->assertCanSeeTableRecords([$foreign])
            ->assertCanNotSeeTableRecords([$match, $missing]);
        $this->assertSame(1, $component->instance()->comparison['total']);
        $this->assertSame($before, Member::all()->toArray());
    }

    public function test_selection_is_required_and_nonexistent_or_deleted_organisations_cannot_be_compared(): void
    {
        $org = $this->organisation('Gelöschter Verein');
        $org->delete();
        $this->loginAdmin();
        $component = Livewire::test(CsvComparison::class)
            ->call('prepare')->assertHasErrors(['selectionData.organisation_id']);
        foreach ([$org->id, 999999] as $invalid) {
            $component->set('selectionData.organisation_id', (string) $invalid)
                ->call('prepare')->assertHasErrors(['selectionData.organisation_id']);
        }
    }

    public function test_admin_comparison_is_not_accessible_to_organisation_accounts_or_non_admin_users(): void
    {
        $this->loginAdmin();
        $url = CsvComparison::getUrl(panel: 'admin');
        auth('web')->logout();
        $this->actingAs($this->organisation('Nicht Admin'), 'organisation');
        $this->get($url)->assertRedirect('/verwaltung/login');
        $this->assertFalse(CsvComparison::canAccess());
        $this->actingAs(User::factory()->create(['is_admin' => false]), 'web');
        $this->get($url)->assertForbidden();
        $this->assertFalse(CsvComparison::canAccess());
    }

    public function test_admin_comparison_cache_is_separate_from_other_admin_sessions(): void
    {
        $org = $this->organisation('Cache Verein');
        $this->loginAdmin();
        $component = Livewire::test(CsvComparison::class)
            ->set('selectionData.organisation_id', (string) $org->id)
            ->set('uploadData.csv', UploadedFile::fake()->createWithContent('members.csv', "Email;Vorname;Nachname\nanna@example.test;Anna;Muster\n"))
            ->call('prepare')->call('compare')->assertHasNoErrors();
        $token = $component->get('comparisonToken');
        $this->actingAs(User::factory()->create(['is_admin' => true]), 'web');
        $page = new CsvComparison;
        $page->selectionData = ['organisation_id' => $org->id];
        $page->comparisonToken = $token;
        $this->assertNull($page->comparison());
    }

    private function loginAdmin(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]), 'web');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function organisation(string $name, ?string $city = null): Organisation
    {
        return Organisation::create(['name' => $name, 'city' => $city, 'type' => 'verein',
            'email' => uniqid('admin-csv-').'@example.test', 'password' => 'test-password',
            'is_active' => true, 'is_approved' => true, 'approval_status' => 'approved']);
    }

    private function member(Organisation $org, string $first, string $last, string $email): Member
    {
        return Member::create(['organisation_id' => $org->id, 'first_name' => $first,
            'last_name' => $last, 'email' => $email, 'password' => 'test-password', 'status' => 'approved']);
    }
}
