<?php

namespace Tests\Feature;

use App\Filament\Organisation\Pages\CsvComparison;
use App\Models\Member;
use App\Models\Organisation;
use App\Services\OrganisationMemberCsvComparison;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class OrganisationCsvComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_parser_preserves_empty_and_duplicate_header_positions_and_normalizes_encoding(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'test-csv');
        file_put_contents($path, mb_convert_encoding(
            "Vorname;;Nachname;E-Mail Adresse;Spalte;Spalte\n\"Anna\";;\"Müller\";\"anna@example.test;\";x;x\n\n",
            'Windows-1252', 'UTF-8'
        ));
        try {
            $service = app(OrganisationMemberCsvComparison::class);
            $csv = $service->read($path);
            $this->assertCount(6, $csv['headers']);
            $this->assertSame('', $csv['headers'][1]);
            $this->assertSame('Müller', $csv['rows'][0][2]);
            $this->assertSame(['email' => '3', 'first_name' => '0', 'last_name' => '2'], $service->suggest($csv['headers']));
            $this->assertCount(1, $csv['rows']);
        } finally {
            unlink($path);
        }
    }

    public function test_comparison_reports_missing_and_ambiguous_members_without_changing_any_records(): void
    {
        $org = $this->organisation('compare');
        $matched = $this->member($org, 'Anna', 'Müller', 'anna@example.test');
        $missing = $this->member($org, 'Nicht', 'Vorhanden', 'missing@example.test');
        $nameOnly = $this->member($org, 'Nur', 'Name', 'new@example.test');
        $emailOnly = $this->member($org, 'Anderer', 'Name', 'shared@example.test');
        $ambiguous = $this->member($org, 'Doppelter', 'Name', 'duplicate-new@example.test');
        $foreign = $this->member($this->organisation('foreign'), 'Foreign', 'Member', 'foreign@example.test');
        $before = Member::all()->toArray();
        $csv = ['headers' => ['Email', 'Vorname', 'Nachname'], 'rows' => [
            [' ANNA@example.test; ', ' anna ', ' müller '],
            ['', 'Nur', 'Name'],
            ['shared@example.test', 'Falscher', 'Name'],
            ['old1@example.test', 'Doppelter', 'Name'],
            ['old2@example.test', 'Doppelter', 'Name'],
            ['invalid', '', ''],
        ]];
        $result = app(OrganisationMemberCsvComparison::class)->compare(
            $csv, ['email' => '0', 'first_name' => '1', 'last_name' => '2'], $org->id
        );
        $this->assertSame(5, $result['total']);
        $this->assertSame(1, $result['matched']);
        $this->assertSame(1, $result['missing']);
        $this->assertSame(3, $result['review']);
        $this->assertSame(1, $result['invalid_rows']);
        $this->assertArrayNotHasKey($matched->id, $result['issues']);
        $this->assertArrayNotHasKey($foreign->id, $result['issues']);
        $this->assertSame('missing', $result['issues'][$missing->id]['kind']);
        foreach ([$nameOnly, $emailOnly, $ambiguous] as $member) {
            $this->assertSame('review', $result['issues'][$member->id]['kind']);
        }
        $this->assertSame('old1@example.test; old2@example.test', $result['issues'][$ambiguous->id]['csv_emails']);
        $this->assertSame($before, Member::all()->toArray());
    }

    public function test_upload_mapping_and_result_table_are_scoped_to_current_organisation(): void
    {
        $org = $this->organisation('page');
        $matched = $this->member($org, 'Anna', 'Muster', 'anna@example.test');
        $missing = $this->member($org, 'Fehlend', 'Mitglied', 'missing@example.test');
        $review = $this->member($org, 'Nur', 'Name', 'review@example.test');
        $foreign = $this->member($this->organisation('page-foreign'), 'Foreign', 'Member', 'foreign@example.test');
        $this->actingAs($org, 'organisation');
        Filament::setCurrentPanel(Filament::getPanel('organisation'));
        $csv = UploadedFile::fake()->createWithContent('members.csv', "E-Mail Adresse;Vorname;Nachname\nanna@example.test;Anna;Muster\n;Nur;Name\n");
        $component = Livewire::test(CsvComparison::class)
            ->set('uploadData.csv', $csv)
            ->call('prepare')->assertHasNoErrors()
            ->assertSet('mappingData.email', '0')
            ->call('compare')->assertHasNoErrors()
            ->assertSee('Nicht in CSV gefunden')
            ->assertCanSeeTableRecords([$missing, $review])
            ->assertCanNotSeeTableRecords([$matched, $foreign])
            ->filterTable('comparison_kind', 'missing')
            ->assertCanSeeTableRecords([$missing])
            ->assertCanNotSeeTableRecords([$review]);

        $component->set('mappingData.first_name', '0')
            ->assertDontSee('3. Ergebnis')
            ->call('compare')->assertHasErrors(['mappingData.email']);
        $component->call('startOver')->assertSet('headers', [])->assertSet('comparisonToken', null);
        $this->get(CsvComparison::getUrl(panel: 'organisation'))->assertOk();
    }

    public function test_invalid_or_expired_upload_and_duplicate_mapping_do_not_produce_false_results(): void
    {
        $org = $this->organisation('invalid');
        $this->actingAs($org, 'organisation');
        Filament::setCurrentPanel(Filament::getPanel('organisation'));
        $component = Livewire::test(CsvComparison::class)
            ->set('uploadData.csv', [UploadedFile::fake()->createWithContent('empty.csv', "Email;Vorname;Nachname\n")])
            ->call('prepare')->assertHasErrors(['uploadData.csv']);

        $component->set('uploadData.csv', [UploadedFile::fake()->createWithContent('valid.csv', "Email;Vorname;Nachname\nanna@example.test;Anna;Muster\n")])
            ->call('prepare')->assertHasNoErrors()
            ->set('mappingData.first_name', '0')
            ->call('compare')->assertHasErrors(['mappingData.email']);

        Cache::flush();
        $component->set('mappingData.first_name', '1')
            ->call('compare')->assertHasErrors(['mappingData.email']);
    }

    public function test_header_only_or_unusable_csv_is_rejected_and_comma_and_tab_csv_are_supported(): void
    {
        $service = app(OrganisationMemberCsvComparison::class);
        foreach ([',', "\t"] as $separator) {
            $path = tempnam(sys_get_temp_dir(), 'test-csv');
            file_put_contents($path, "\xEF\xBB\xBF".implode($separator, ['Email', 'Vorname', 'Nachname'])."\n".
                implode($separator, ['anna@example.test', 'Anna', 'Muster'])."\n");
            try {
                $this->assertSame($separator, $service->read($path)['delimiter']);
            } finally {
                unlink($path);
            }
        }
        $this->expectException(ValidationException::class);
        $service->compare(['headers' => ['Email', 'Vorname', 'Nachname'], 'rows' => [['invalid', '', '']]],
            ['email' => 0, 'first_name' => 1, 'last_name' => 2], 1);
    }

    public function test_csv_with_multiline_values_and_reordered_headers_can_be_mapped(): void
    {
        $org = $this->organisation('custom-columns');
        $matched = $this->member($org, 'Anna', 'Muster', 'anna@example.test');
        $path = tempnam(sys_get_temp_dir(), 'test-csv');
        file_put_contents($path, "Notiz,Familie,Kontakt,Rufname\n\"Zeile eins\nZeile zwei\",Muster,anna@example.test,Anna\n");
        try {
            $service = app(OrganisationMemberCsvComparison::class);
            $csv = $service->read($path);
            $result = $service->compare($csv, ['email' => 2, 'first_name' => 3, 'last_name' => 1], $org->id);
            $this->assertSame(1, $result['csv_rows']);
            $this->assertSame(1, $result['matched']);
            $this->assertArrayNotHasKey($matched->id, $result['issues']);
        } finally {
            unlink($path);
        }
    }

    public function test_upload_cache_cannot_be_read_by_another_organisation_even_with_same_token(): void
    {
        $org = $this->organisation('cache-owner');
        $other = $this->organisation('cache-other');
        $this->actingAs($org, 'organisation');
        Filament::setCurrentPanel(Filament::getPanel('organisation'));
        $component = Livewire::test(CsvComparison::class)
            ->set('uploadData.csv', UploadedFile::fake()->createWithContent('valid.csv', "Email;Vorname;Nachname\nanna@example.test;Anna;Muster\n"))
            ->call('prepare')->assertHasNoErrors();
        $token = $component->get('comparisonToken');
        $this->actingAs($other, 'organisation');
        $page = new CsvComparison;
        $page->comparisonToken = $token;
        $this->assertNull($page->comparison());
        $component->call('compare')->assertHasErrors(['mappingData.email']);
    }

    public function test_mapping_options_refresh_on_reupload_and_custom_columns_can_be_selected(): void
    {
        $org = $this->organisation('reupload');
        $this->actingAs($org, 'organisation');
        Filament::setCurrentPanel(Filament::getPanel('organisation'));
        $component = Livewire::test(CsvComparison::class)
            ->set('uploadData.csv', UploadedFile::fake()->createWithContent('first.csv', "Email;Vorname;Nachname\nanna@example.test;Anna;Muster\n"))
            ->call('prepare')->assertHasNoErrors()
            ->assertFormFieldExists('email', 'mappingForm', fn (Select $field): bool => $field->getOptions() === [
                0 => '1: Email', 1 => '2: Vorname', 2 => '3: Nachname',
            ])
            ->call('startOver')
            ->set('uploadData.csv', [UploadedFile::fake()->createWithContent('second.csv', "Familie,Kontakt,Rufname\nMuster,anna@example.test,Anna\n")])
            ->call('prepare')->assertHasNoErrors();
        foreach (['email', 'first_name', 'last_name'] as $field) {
            $component->assertFormFieldExists($field, 'mappingForm', fn (Select $select): bool => $select->getOptions() === [
                0 => '1: Familie', 1 => '2: Kontakt', 2 => '3: Rufname',
            ]);
        }
        $component->set('mappingData.email', '1')->set('mappingData.first_name', '2')->set('mappingData.last_name', '0')
            ->call('compare')->assertHasNoErrors()->assertSee('3. Ergebnis');
    }

    private function organisation(string $suffix): Organisation
    {
        return Organisation::create(['type' => 'verein', 'name' => 'CSV '.$suffix,
            'email' => $suffix.'@example.test', 'password' => 'csv-test-password',
            'is_active' => true, 'is_approved' => true, 'approval_status' => 'approved']);
    }

    private function member(Organisation $org, string $first, string $last, string $email): Member
    {
        return Member::create(['organisation_id' => $org->id, 'first_name' => $first,
            'last_name' => $last, 'email' => $email, 'password' => 'member-password', 'status' => 'approved']);
    }
}
