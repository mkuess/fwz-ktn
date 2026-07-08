<?php

namespace App\Filament\Resources\OrganisationResource\Pages;

use App\Filament\Resources\OrganisationResource;
use App\Filament\Support\SmartCsvImportAction;
use App\Models\Organisation;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
                    ['key' => 'type', 'label' => 'Typ', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'name', 'label' => 'Name', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'email', 'label' => 'E-Mail', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'password', 'label' => 'Passwort', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'zvr_number', 'label' => 'ZVR-Nummer', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'description', 'label' => 'Beschreibung', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'street', 'label' => 'Straße', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'zip', 'label' => 'PLZ', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'city', 'label' => 'Ort', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'phone', 'label' => 'Telefon', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'website', 'label' => 'Website', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'representative', 'label' => 'Vertreter', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'contact_person', 'label' => 'Kontaktperson', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                ],
                importRow: function (array $mapped): bool {
                    $type = $mapped['type'] ?? null;
                    $name = $mapped['name'] ?? null;
                    $email = $mapped['email'] ?? null;
                    $password = $mapped['password'] ?? null;

                    if (! $type || ! $name || ! $email || ! $password) {
                        return false;
                    }

                    $type = strtolower($type);

                    if (! in_array($type, ['verein', 'organisation'], true)) {
                        return false;
                    }

                    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return false;
                    }

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
                description: 'Pflichtspalten: type (verein/organisation), name, email, password. Optionale Spalten: zvr_number, description, street, zip, city, phone, website, representative, contact_person. Importierte Organisationen werden als nicht freigeschalten (is_approved = false) angelegt.',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
