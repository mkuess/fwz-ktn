<?php

namespace App\Filament\Support;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;
use Throwable;

class SmartCsvImportAction
{
    public const IGNORE = '__ignore__';

    public const AUTO_SLUG = '__auto_slug__';

    /**
     * @param  array<int, array{key: string, label: string, icon?: string, required?: bool, special?: array{value: string, label: string}}>  $fields
     * @param  Closure(array<string, string|null>): (bool|string)  $importRow  Returns true when the row was imported, false when it was skipped (generic reason), or a string with the skip reason.
     */
    public static function make(
        string $name,
        string $label,
        array $fields,
        Closure $importRow,
        string $entityPluralLabel,
    ): Action {
        return Action::make($name)
            ->label($label)
            ->icon('heroicon-o-arrow-up-tray')
            ->modalWidth('xl')
            ->modalHeading($label)
            ->modalSubmitActionLabel('Importieren')
            ->modalCancelActionLabel('Abbrechen')
            ->form([
                FileUpload::make('csv_file')
                    ->hiddenLabel()
                    ->required()
                    ->live()
                    ->storeFiles(false)
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel']),
                Placeholder::make('csv_preview')
                    ->hiddenLabel()
                    ->live()
                    ->visible(fn (Get $get) => self::tryAnalyze($get('csv_file')) !== null)
                    ->content(function (Get $get) {
                        $analysis = self::tryAnalyze($get('csv_file'));

                        if ($analysis === null) {
                            return '';
                        }

                        return new HtmlString(sprintf(
                            '<span class="fi-in-text text-sm text-gray-500 dark:text-gray-400">📋 Erkannter Trennzeichen: %s | Spalten: %s</span>',
                            e($analysis['delimiterLabel']),
                            e(implode(', ', $analysis['headers']))
                        ));
                    }),
                Grid::make(2)
                    ->visible(fn (Get $get) => self::tryAnalyze($get('csv_file')) !== null)
                    ->schema(array_map(
                        fn (array $field) => Select::make("mapping_{$field['key']}")
                            ->label(new HtmlString(sprintf(
                                '%s %s%s',
                                $field['icon'] ?? '🏷',
                                e($field['label']),
                                ($field['required'] ?? false) ? '<span class="text-danger-600">*</span>' : ''
                            )))
                            ->required((bool) ($field['required'] ?? false))
                            ->searchable()
                            ->live()
                            ->options(function (Get $get) use ($field) {
                                $options = [];
                                $analysis = self::tryAnalyze($get('csv_file'));

                                if ($analysis !== null) {
                                    foreach ($analysis['headers'] as $header) {
                                        $options[$header] = $header;
                                    }
                                }

                                if ($special = $field['special'] ?? null) {
                                    $options[$special['value']] = $special['label'];
                                }

                                return $options;
                            })
                            ->default(function (Get $get) use ($field) {
                                $analysis = self::tryAnalyze($get('csv_file'));

                                if ($analysis !== null) {
                                    foreach ($analysis['headers'] as $header) {
                                        if (self::normalize($header) === self::normalize($field['key'])
                                            || self::normalize($header) === self::normalize($field['label'])) {
                                            return $header;
                                        }
                                    }
                                }

                                return $field['special']['value'] ?? null;
                            }),
                        $fields
                    )),
            ])
            ->action(function (array $data) use ($fields, $importRow, $entityPluralLabel) {
                $file = $data['csv_file'] ?? null;

                if (! $file) {
                    Notification::make()
                        ->title('Keine Datei hochgeladen')
                        ->danger()
                        ->send();

                    return;
                }

                try {
                    $path = self::resolvePath($file);
                    $analysis = CsvImportHelper::analyze($path);
                    $rows = CsvImportHelper::readRows($path, $analysis['delimiter']);

                    $imported = 0;
                    $skipped = 0;
                    $skipReasons = [];

                    foreach ($rows as $row) {
                        $mapped = [];

                        foreach ($fields as $field) {
                            $mappingKey = $data["mapping_{$field['key']}"] ?? null;

                            if ($mappingKey === null || $mappingKey === self::IGNORE) {
                                $mapped[$field['key']] = null;
                            } elseif ($mappingKey === self::AUTO_SLUG) {
                                $mapped[$field['key']] = self::AUTO_SLUG;
                            } else {
                                $value = $row[$mappingKey] ?? null;
                                $mapped[$field['key']] = $value !== null && $value !== '' ? trim((string) $value) : null;
                            }
                        }

                        $result = $importRow($mapped);

                        if ($result === true) {
                            $imported++;
                        } else {
                            $skipped++;
                            $reason = is_string($result) && $result !== '' ? $result : 'unbekannter Grund';
                            $skipReasons[$reason] = ($skipReasons[$reason] ?? 0) + 1;
                        }
                    }

                    $title = "{$imported} {$entityPluralLabel} importiert, {$skipped} übersprungen";

                    if ($skipReasons !== []) {
                        $breakdown = [];

                        foreach ($skipReasons as $reason => $count) {
                            $breakdown[] = "{$count} {$reason}";
                        }

                        $title .= ' ('.implode(', ', $breakdown).')';
                    }

                    Notification::make()
                        ->title($title)
                        ->success()
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()
                        ->title('CSV-Import fehlgeschlagen')
                        ->body('Die Datei konnte nicht verarbeitet werden. Bitte überprüfen Sie das Format.')
                        ->danger()
                        ->send();
                }
            });
    }

    /**
     * @return array{delimiter: string, delimiterLabel: string, headers: array<int, string>}|null
     */
    protected static function tryAnalyze(mixed $file): ?array
    {
        if (! $file) {
            return null;
        }

        try {
            return CsvImportHelper::analyze(self::resolvePath($file));
        } catch (Throwable $e) {
            return null;
        }
    }

    protected static function normalize(string $value): string
    {
        return strtolower(str_replace(['-', ' '], '_', trim($value)));
    }

    protected static function resolvePath(mixed $file): string
    {
        if ($file instanceof TemporaryUploadedFile) {
            $path = $file->getRealPath() ?: $file->getPathname();

            if (! $path || ! is_file($path)) {
                throw new RuntimeException('Die hochgeladene Datei konnte nicht gefunden werden.');
            }

            return $path;
        }

        // Filament's FileUpload component exposes its *raw* (non-dehydrated)
        // form state as an array keyed by a random UUID, even for a single,
        // non-multiple upload, e.g. ['9f2b...' => TemporaryUploadedFile].
        // $get() inside reactive closures returns this raw state, so we need
        // to unwrap it before resolving the actual file.
        if (is_array($file)) {
            foreach ($file as $item) {
                if ($item) {
                    return self::resolvePath($item);
                }
            }

            throw new RuntimeException('Es wurde keine Datei hochgeladen.');
        }

        if (is_string($file) && $file !== '' && is_file($file)) {
            return $file;
        }

        throw new RuntimeException('Unbekannter Dateityp.');
    }
}
