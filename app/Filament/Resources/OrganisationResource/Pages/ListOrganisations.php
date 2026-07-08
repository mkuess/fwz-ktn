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
                    ['key' => 'type', 'label' => 'Typ', 'icon' => '🏢', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'name', 'label' => 'Name', 'icon' => '🏷', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'email', 'label' => 'E-Mail', 'icon' => '📧', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'password', 'label' => 'Passwort', 'icon' => '🔑', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
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
            ),
            Actions\CreateAction::make(),
        ];
    }
}
