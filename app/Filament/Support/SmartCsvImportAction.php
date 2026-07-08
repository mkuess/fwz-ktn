<?php

namespace App\Filament\Support;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard\Step;
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
     * @param  array<int, array{key: string, label: string, required?: bool, special?: array{value: string, label: string}}>  $fields
     * @param  Closure(array<string, string|null>): bool  $importRow  Returns true when the row was imported, false when it was skipped.
     */
    public static function make(
        string $name,
        string $label,
        array $fields,
        Closure $importRow,
        string $entityPluralLabel,
        ?string $description = null,
    ): Action {
        return Action::make($name)
            ->label($label)
            ->icon('heroicon-o-arrow-up-tray')
            ->modalWidth('2xl')
            ->modalSubmitActionLabel('Importieren')
            ->steps([
                Step::make('upload')
                    ->label('Datei hochladen')
                    ->description($description)
                    ->schema([
                        FileUpload::make('csv_file')
                            ->label('CSV-Datei')
                            ->required()
                            ->live()
                            ->storeFiles(false)
                            ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                            ->helperText('Das Trennzeichen (Semikolon, Komma oder Tabulator) wird automatisch erkannt.'),
                        Placeholder::make('csv_preview')
                            ->label('Erkannte Spalten')
                            ->live()
                            ->content(function (Get $get) {
                                $analysis = self::tryAnalyze($get('csv_file'));

                                if ($analysis === null) {
                                    return 'Bitte laden Sie zunächst eine CSV-Datei hoch.';
                                }

                                return new HtmlString(sprintf(
                                    'Erkannter Trennzeichen: %s | Gefundene Spalten: %s',
                                    e($analysis['delimiterLabel']),
                                    e(implode(', ', $analysis['headers']))
                                ));
                            }),
                    ]),
                Step::make('mapping')
                    ->label('Spalten zuordnen')
                    ->description('Ordnen Sie jeder Datenbank-Spalte die passende CSV-Spalte zu.')
                    ->schema(array_map(
                        fn (array $field) => Select::make("mapping_{$field['key']}")
                            ->label($field['label'])
                            ->required((bool) ($field['required'] ?? false))
                            ->searchable()
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

                        if ($importRow($mapped)) {
                            $imported++;
                        } else {
                            $skipped++;
                        }
                    }

                    Notification::make()
                        ->title("{$imported} {$entityPluralLabel} importiert, {$skipped} übersprungen")
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
            return $file->getRealPath();
        }

        if (is_string($file) && is_file($file)) {
            return $file;
        }

        throw new RuntimeException('Unbekannter Dateityp.');
    }
}
