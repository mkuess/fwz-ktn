<?php

namespace App\Filament\Resources\BenefitResource\Pages;

use App\Filament\Resources\BenefitResource;
use App\Filament\Support\SmartCsvImportAction;
use App\Models\Benefit;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenefits extends ListRecords
{
    protected static string $resource = BenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SmartCsvImportAction::make(
                name: 'importCsv',
                label: 'CSV importieren',
                fields: [
                    ['key' => 'name', 'label' => 'Name', 'icon' => '🏷', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'description', 'label' => 'Beschreibung', 'icon' => '📝', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'website', 'label' => 'Webseite', 'icon' => '🌐', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'logo_path', 'label' => 'Logo', 'icon' => '🖼', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'is_active', 'label' => 'Aktiv', 'icon' => '✅', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'sort_order', 'label' => 'Reihenfolge', 'icon' => '🔢', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                ],
                importRow: function (array $mapped): bool {
                    $name = $mapped['name'] ?? null;
                    $description = $mapped['description'] ?? null;

                    if ($name === null || $name === '' || $description === null || $description === '') {
                        return false;
                    }

                    $isActiveRaw = strtolower((string) ($mapped['is_active'] ?? ''));
                    $isActive = $isActiveRaw === '' ? true : in_array($isActiveRaw, ['1', 'true', 'yes', 'ja'], true);

                    $sortOrder = $mapped['sort_order'];
                    $sortOrder = ($sortOrder !== null && $sortOrder !== '') ? (int) $sortOrder : 0;

                    Benefit::updateOrCreate(
                        ['name' => $name],
                        [
                            'description' => $description,
                            'website' => $mapped['website'] ?: null,
                            'logo_path' => $mapped['logo_path'] ?: null,
                            'is_active' => $isActive,
                            'sort_order' => $sortOrder,
                        ]
                    );

                    return true;
                },
                entityPluralLabel: 'Benefits',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
