<?php

namespace App\Filament\Resources\OrganisationResource\Pages;

use App\Filament\Resources\OrganisationResource;
use App\Filament\Support\SmartCsvImportAction;
use App\Models\Organisation;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListOrganisations extends ListRecords
{
    protected static string $resource = OrganisationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SmartCsvImportAction::make(
                name: 'importCsv',
                label: 'CSV importieren',
                fields: [
                    ['key' => 'name', 'label' => 'Name', 'icon' => '🏷', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'email', 'label' => 'E-Mail', 'icon' => '📧', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'type', 'label' => 'Typ', 'icon' => '🏢', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'password', 'label' => 'Passwort', 'icon' => '🔑', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'zvr_number', 'label' => 'ZVR-Nummer', 'icon' => '🔢', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'description', 'label' => 'Beschreibung', 'icon' => '📝', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'street', 'label' => 'Straße', 'icon' => '🏠', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'zip', 'label' => 'PLZ', 'icon' => '📮', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'city', 'label' => 'Ort', 'icon' => '🌆', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'phone', 'label' => 'Telefon', 'icon' => '📞', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'website', 'label' => 'Webseite', 'icon' => '🌐', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'representative', 'label' => 'Vertretung', 'icon' => '👤', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'contact_person', 'label' => 'Ansprechpartner:in', 'icon' => '👥', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                ],
                importRow: function (array $mapped): bool|string {
                    $name = $mapped['name'] ?? null;

                    if (! $name) {
                        return 'ohne Name';
                    }

                    $email = $mapped['email'] ?? null;

                    if ($email !== null && $email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return 'mit ungültiger E-Mail';
                    }

                    if (! $email) {
                        $email = Str::slug($name).'@import.local';
                    }

                    $typeRaw = strtoupper(trim((string) ($mapped['type'] ?? '')));
                    $type = match (true) {
                        in_array($typeRaw, ['ASSOCIATION', 'VEREIN', 'V'], true) => 'verein',
                        in_array($typeRaw, ['ORGANISATION', 'O'], true) => 'organisation',
                        default => 'organisation',
                    };

                    $password = $mapped['password'] ?? null;
                    $password = ($password !== null && $password !== '') ? $password : Str::random(12);

                    Organisation::updateOrCreate(
                        ['email' => $email],
                        [
                            'type' => $type,
                            'name' => $name,
                            'password' => $password,
                            'zvr_number' => $mapped['zvr_number'] ?: null,
                            'description' => $mapped['description'] ?: null,
                            'street' => $mapped['street'] ?: null,
                            'zip' => $mapped['zip'] ?: null,
                            'city' => $mapped['city'] ?: null,
                            'phone' => $mapped['phone'] ?: null,
                            'website' => $mapped['website'] ?: null,
                            'representative' => $mapped['representative'] ?: null,
                            'contact_person' => $mapped['contact_person'] ?: null,
                            'is_approved' => false,
                            'is_active' => true,
                        ]
                    );

                    return true;
                },
                entityPluralLabel: 'Organisationen',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
