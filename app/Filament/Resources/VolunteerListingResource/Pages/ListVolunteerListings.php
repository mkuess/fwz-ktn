<?php

namespace App\Filament\Resources\VolunteerListingResource\Pages;

use App\Filament\Resources\VolunteerListingResource;
use App\Filament\Support\SmartCsvImportAction;
use App\Models\Organisation;
use App\Models\VolunteerListing;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVolunteerListings extends ListRecords
{
    protected static string $resource = VolunteerListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SmartCsvImportAction::make(
                name: 'importCsv',
                label: 'CSV importieren',
                fields: [
                    ['key' => 'title', 'label' => 'Titel', 'icon' => '📰', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'description', 'label' => 'Beschreibung', 'icon' => '📝', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'organisation_id', 'label' => 'Organisation ID', 'icon' => '🏢', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'website_link', 'label' => 'Website', 'icon' => '🌐', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'is_spontaneous', 'label' => 'Spontansuche', 'icon' => '⚡', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'street', 'label' => 'Straße', 'icon' => '🏠', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'zip', 'label' => 'PLZ', 'icon' => '📮', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'city', 'label' => 'Ort', 'icon' => '🌆', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'valid_until', 'label' => 'Gültig bis', 'icon' => '📅', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'is_active', 'label' => 'Aktiv', 'icon' => '✅', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                ],
                importRow: function (array $mapped): bool {
                    $title = $mapped['title'] ?? null;
                    $description = $mapped['description'] ?? null;
                    $organisationId = $mapped['organisation_id'] ?? null;

                    if (! $title || ! $description || ! $organisationId) {
                        return false;
                    }

                    if (! ctype_digit((string) $organisationId) || ! Organisation::whereKey((int) $organisationId)->exists()) {
                        return false;
                    }

                    $isSpontaneousRaw = strtolower((string) ($mapped['is_spontaneous'] ?? ''));
                    $isSpontaneous = in_array($isSpontaneousRaw, ['1', 'true', 'yes', 'ja'], true);

                    $isActiveRaw = strtolower((string) ($mapped['is_active'] ?? ''));
                    $isActive = $isActiveRaw === '' ? true : in_array($isActiveRaw, ['1', 'true', 'yes', 'ja'], true);

                    VolunteerListing::updateOrCreate(
                        [
                            'title' => $title,
                            'organisation_id' => (int) $organisationId,
                        ],
                        [
                            'description' => $description,
                            'website_link' => $mapped['website_link'] ?: null,
                            'is_spontaneous' => $isSpontaneous,
                            'street' => $mapped['street'] ?: null,
                            'zip' => $mapped['zip'] ?: null,
                            'city' => $mapped['city'] ?: null,
                            'valid_until' => $mapped['valid_until'] ?: null,
                            'is_active' => $isActive,
                        ]
                    );

                    return true;
                },
                entityPluralLabel: 'Freiwilligeneinsätze',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
