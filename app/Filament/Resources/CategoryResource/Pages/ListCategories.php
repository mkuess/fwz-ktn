<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Filament\Support\SmartCsvImportAction;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SmartCsvImportAction::make(
                name: 'importCsv',
                label: 'CSV importieren',
                fields: [
                    ['key' => 'name', 'label' => 'Name', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'slug', 'label' => 'Slug', 'special' => ['value' => SmartCsvImportAction::AUTO_SLUG, 'label' => '(auto-generieren aus Name)']],
                    ['key' => 'color', 'label' => 'Farbe', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'icon', 'label' => 'Icon', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'sort_order', 'label' => 'Reihenfolge', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
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

                    $sortOrder = $mapped['sort_order'];
                    $sortOrder = ($sortOrder !== null && $sortOrder !== '') ? (int) $sortOrder : 0;

                    Category::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $name,
                            'color' => $mapped['color'] ?: null,
                            'icon' => $mapped['icon'] ?: null,
                            'sort_order' => $sortOrder,
                        ]
                    );

                    return true;
                },
                entityPluralLabel: 'Kategorien',
                description: 'Pflichtfelder: name | Optionale Felder: slug, color, icon, sort_order. Beispiel: Feuerwehren,feuerwehren,#dc2626,fire,1',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
