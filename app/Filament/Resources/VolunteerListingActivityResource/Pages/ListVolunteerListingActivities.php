<?php

namespace App\Filament\Resources\VolunteerListingActivityResource\Pages;

use App\Filament\Resources\VolunteerListingActivityResource;
use App\Filament\Support\SmartCsvImportAction;
use App\Models\VolunteerListingActivity;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListVolunteerListingActivities extends ListRecords
{
    protected static string $resource = VolunteerListingActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SmartCsvImportAction::make(
                name: 'importCsv',
                label: 'CSV importieren',
                fields: [
                    ['key' => 'name', 'label' => 'Name', 'icon' => '🏷️', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'slug', 'label' => 'Slug', 'icon' => '🔗', 'special' => ['value' => SmartCsvImportAction::AUTO_SLUG, 'label' => '(auto-generieren aus Name)']],
                    ['key' => 'sort_order', 'label' => 'Reihenfolge', 'icon' => '🔢', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                ],
                importRow: function (array $mapped): bool {
                    $name = $mapped['name'] ?? null;

                    if ($name === null || $name === '') {
                        return false;
                    }

                    $slugValue = $mapped['slug'] ?? null;
                    $slug = ($slugValue === SmartCsvImportAction::AUTO_SLUG || $slugValue === null || $slugValue === '')
                        ? Str::slug($name)
                        : $slugValue;

                    $sortOrderRaw = $mapped['sort_order'] ?? null;
                    $sortOrder = is_numeric($sortOrderRaw) ? (int) $sortOrderRaw : 0;

                    VolunteerListingActivity::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $name,
                            'sort_order' => $sortOrder,
                        ]
                    );

                    return true;
                },
                entityPluralLabel: 'Gesuch-Aktivitäten',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
