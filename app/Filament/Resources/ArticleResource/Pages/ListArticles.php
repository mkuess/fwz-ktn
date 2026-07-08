<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Filament\Support\SmartCsvImportAction;
use App\Models\Article;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SmartCsvImportAction::make(
                name: 'importCsv',
                label: 'CSV importieren',
                fields: [
                    ['key' => 'title', 'label' => 'Titel', 'icon' => '📰', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'body', 'label' => 'Inhalt', 'icon' => '📄', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'slug', 'label' => 'Slug', 'icon' => '🔗', 'special' => ['value' => SmartCsvImportAction::AUTO_SLUG, 'label' => '(auto-generieren aus Titel)']],
                    ['key' => 'excerpt', 'label' => 'Kurztext', 'icon' => '📋', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'published_at', 'label' => 'Veröffentlicht am', 'icon' => '📅', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'is_published', 'label' => 'Veröffentlicht', 'icon' => '✅', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                ],
                importRow: function (array $mapped): bool {
                    $title = $mapped['title'] ?? null;
                    $body = $mapped['body'] ?? null;

                    if ($title === null || $title === '' || $body === null || $body === '') {
                        return false;
                    }

                    $slugValue = $mapped['slug'] ?? null;
                    $slug = ($slugValue === SmartCsvImportAction::AUTO_SLUG || $slugValue === null || $slugValue === '')
                        ? Str::slug($title)
                        : $slugValue;

                    $isPublishedRaw = strtolower((string) ($mapped['is_published'] ?? ''));
                    $isPublished = in_array($isPublishedRaw, ['1', 'true', 'yes', 'ja'], true);

                    $publishedAt = $mapped['published_at'] ?: null;

                    Article::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'title' => $title,
                            'body' => $body,
                            'excerpt' => $mapped['excerpt'] ?: null,
                            'published_at' => $publishedAt,
                            'is_published' => $isPublished,
                        ]
                    );

                    return true;
                },
                entityPluralLabel: 'Artikel',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
