<?php

namespace Tests\Feature;

use App\Filament\Resources\MemberResource\Pages\ListMembers;
use App\Filament\Resources\OrganisationResource\Pages\ListOrganisations;
use App\Models\Member;
use App\Models\Organisation;
use Filament\Actions\Action;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use ReflectionMethod;
use Tests\TestCase;

class CsvImportDeduplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organisation_import_updates_existing_rows_restores_deleted_rows_and_creates_new_rows(): void
    {
        $existing = $this->createOrganisation([
            'name' => 'Bestehende Organisation',
            'email' => 'existing-org@example.test',
            'type' => 'verein',
            'description' => 'Bestehende Beschreibung',
            'phone' => 'Alt',
            'is_approved' => true,
            'is_active' => false,
        ]);
        $existingPassword = $existing->getRawOriginal('password');

        $deleted = $this->createOrganisation([
            'name' => 'Gelöschte Organisation',
            'email' => 'deleted-org@example.test',
        ]);
        Organisation::withoutEvents(fn () => $deleted->delete());

        $path = $this->createCsv(
            ['name', 'email', 'type', 'password', 'zvr_number', 'description', 'street', 'zip', 'city', 'phone', 'website', 'representative', 'contact_person'],
            [
                ['Aktualisierte Organisation', 'existing-org@example.test', '', '', '', '', '', '', '', 'Neu', '', '', ''],
                ['Wiederhergestellte Organisation', 'deleted-org@example.test', 'Verein', '', '', '', '', '', '', '', '', '', ''],
                ['Neue Organisation', 'new-org@example.test', 'Organisation', 'csv-password', '', '', '', '', '', '', '', '', ''],
            ],
        );

        $this->runImport(ListOrganisations::class, $path, [
            'name' => 'name',
            'email' => 'email',
            'type' => 'type',
            'password' => 'password',
            'zvr_number' => 'zvr_number',
            'description' => 'description',
            'street' => 'street',
            'zip' => 'zip',
            'city' => 'city',
            'phone' => 'phone',
            'website' => 'website',
            'representative' => 'representative',
            'contact_person' => 'contact_person',
        ]);

        $existing->refresh();
        $deleted->refresh();
        $new = Organisation::query()->where('email', 'new-org@example.test')->sole();

        $this->assertSame('Aktualisierte Organisation', $existing->name);
        $this->assertSame('verein', $existing->type);
        $this->assertSame('Neu', $existing->phone);
        $this->assertSame('Bestehende Beschreibung', $existing->description);
        $this->assertTrue($existing->is_approved);
        $this->assertFalse($existing->is_active);
        $this->assertSame($existingPassword, $existing->getRawOriginal('password'));

        $this->assertFalse($deleted->trashed());
        $this->assertSame('Wiederhergestellte Organisation', $deleted->name);
        $this->assertSame('verein', $deleted->type);

        $this->assertSame('Neue Organisation', $new->name);
        $this->assertTrue(Hash::check('csv-password', $new->password));
        $this->assertSame(1, Organisation::withTrashed()->where('email', 'existing-org@example.test')->count());
        $this->assertSame(1, Organisation::withTrashed()->where('email', 'deleted-org@example.test')->count());
    }

    public function test_member_import_updates_existing_rows_restores_deleted_rows_and_preserves_protected_fields(): void
    {
        $organisation = $this->createOrganisation([
            'name' => 'Testorganisation',
            'email' => 'member-org@example.test',
        ]);

        $existing = Member::withoutEvents(fn (): Member => Member::create([
            'organisation_id' => $organisation->id,
            'first_name' => 'Alt',
            'last_name' => 'Name',
            'email' => 'existing-member@example.test',
            'password' => 'existing-password',
            'membership_number' => 'FWZ-2026-000001',
            'status' => 'approved',
            'role' => 'admin',
            'card_status' => 'zugesendet',
            'newsletter_optin' => true,
            'street' => 'Alte Straße',
        ]));
        $existingPassword = $existing->getRawOriginal('password');

        $deleted = Member::withoutEvents(fn (): Member => Member::create([
            'organisation_id' => $organisation->id,
            'first_name' => 'Gelöscht',
            'last_name' => 'Mitglied',
            'email' => 'deleted-member@example.test',
        ]));
        Member::withoutEvents(fn () => $deleted->delete());

        $path = $this->createCsv(
            ['first_name', 'last_name', 'email', 'organisation_id', 'street', 'zip', 'city', 'newsletter_optin'],
            [
                ['Neu', 'Name', 'existing-member@example.test', '', '', '9020', 'Klagenfurt', ''],
                ['Wiederhergestellt', 'Mitglied', 'deleted-member@example.test', (string) $organisation->id, '', '', '', 'ja'],
                ['Neues', 'Mitglied', 'new-member@example.test', (string) $organisation->id, 'Neue Straße', '', '', 'nein'],
            ],
        );

        $this->runImport(ListMembers::class, $path, [
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'organisation_id' => 'organisation_id',
            'street' => 'street',
            'zip' => 'zip',
            'city' => 'city',
            'newsletter_optin' => 'newsletter_optin',
        ]);

        $existing->refresh();
        $deleted->refresh();
        $new = Member::query()->where('email', 'new-member@example.test')->sole();

        $this->assertSame('Neu', $existing->first_name);
        $this->assertSame('Name', $existing->last_name);
        $this->assertSame('Alte Straße', $existing->street);
        $this->assertSame('9020', $existing->zip);
        $this->assertSame('Klagenfurt', $existing->city);
        $this->assertTrue($existing->newsletter_optin);
        $this->assertSame('approved', $existing->status);
        $this->assertSame('admin', $existing->role);
        $this->assertSame('FWZ-2026-000001', $existing->membership_number);
        $this->assertSame('zugesendet', $existing->card_status);
        $this->assertSame($existingPassword, $existing->getRawOriginal('password'));

        $this->assertFalse($deleted->trashed());
        $this->assertSame('Wiederhergestellt', $deleted->first_name);
        $this->assertTrue($deleted->newsletter_optin);

        $this->assertSame('Neues', $new->first_name);
        $this->assertSame('Neue Straße', $new->street);
        $this->assertFalse($new->newsletter_optin);
        $this->assertSame('member', $new->role);
        $this->assertSame('pending', $new->status);
        $this->assertSame(1, Member::withTrashed()->where('email', 'existing-member@example.test')->count());
        $this->assertSame(1, Member::withTrashed()->where('email', 'deleted-member@example.test')->count());
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, string>>  $rows
     */
    private function createCsv(array $headers, array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'csv-import-');
        $handle = fopen($path, 'w');

        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $path;
    }

    /**
     * @param  class-string  $pageClass
     * @param  array<string, string>  $mappings
     */
    private function runImport(string $pageClass, string $path, array $mappings): void
    {
        $page = app($pageClass);
        $method = new ReflectionMethod($page, 'getHeaderActions');
        $method->setAccessible(true);
        $actions = $method->invoke($page);
        $action = collect($actions)->first(
            fn (Action $action): bool => $action->getName() === 'importCsv',
        );

        $this->assertInstanceOf(Action::class, $action);

        $callback = $action->getActionFunction();

        $this->assertNotNull($callback);

        $callback([
            'csv_file' => $path,
            ...collect($mappings)->mapWithKeys(
                fn (string $header, string $field): array => ["mapping_{$field}" => $header],
            )->all(),
        ]);

        unlink($path);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createOrganisation(array $attributes): Organisation
    {
        return Organisation::withoutEvents(fn (): Organisation => Organisation::create(array_merge([
            'type' => 'organisation',
            'role' => 'org_admin',
            'name' => 'Import Test Organisation',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'test-password',
        ], $attributes)));
    }
}
