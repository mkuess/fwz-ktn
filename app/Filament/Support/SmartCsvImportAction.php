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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;
use Throwable;

class SmartCsvImportAction
{
    public const IGNORE = '__ignore__';

    public const AUTO_SLUG = '__auto_slug__';

    /**
     * Relative (to storage_path()) path of the detailed skip-reason log
     * written after every import run.
     */
    public const LOG_PATH = 'logs/import.log';

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
                Placeholder::make('csv_row_preview')
                    ->hiddenLabel()
                    ->live()
                    ->visible(fn (Get $get) => self::tryAnalyze($get('csv_file')) !== null)
                    ->content(fn (Get $get) => self::renderPreview($get, $fields)),
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

                // CSV imports can involve hundreds of rows and (for some
                // resources) per-row password hashing, which can easily
                // exceed PHP's default 30 second execution limit. Bulk
                // imports are an explicit, admin-triggered action, so it's
                // safe to grant them a generous ceiling.
                set_time_limit(300);

                try {
                    $path = self::resolvePath($file);
                    $analysis = CsvImportHelper::analyze($path);
                    $rows = iterator_to_array(CsvImportHelper::readRows($path, $analysis['delimiter']));

                    $imported = 0;
                    $skipped = 0;
                    $skipReasons = [];
                    $skipDetails = [];

                    // Process rows in chunks so we can wrap each batch in a
                    // single DB transaction (far fewer commits than one per
                    // row) while keeping memory usage bounded for very large
                    // files.
                    foreach (array_chunk($rows, 50, preserve_keys: true) as $chunk) {
                        DB::transaction(function () use ($chunk, $fields, $data, $importRow, $entityPluralLabel, &$imported, &$skipped, &$skipReasons, &$skipDetails) {
                            foreach ($chunk as $rowIndex => $row) {
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

                                try {
                                    $result = $importRow($mapped);
                                } catch (Throwable $e) {
                                    $result = 'Fehler: '.$e->getMessage();
                                }

                                if ($result === true) {
                                    $imported++;
                                } else {
                                    $skipped++;
                                    $reason = is_string($result) && $result !== '' ? $result : 'unbekannter Grund';
                                    $skipReasons[$reason] = ($skipReasons[$reason] ?? 0) + 1;

                                    $lineNumber = $rowIndex + 2;
                                    $identifyingValue = self::firstNonEmptyMappedValue($mapped);
                                    $skipDetails[] = sprintf(
                                        'Zeile %d: %s%s',
                                        $lineNumber,
                                        $reason,
                                        $identifyingValue !== null ? " ({$identifyingValue})" : ''
                                    );

                                    Log::warning('CSV-Import: Zeile übersprungen', [
                                        'entitaet' => $entityPluralLabel,
                                        'zeile' => $lineNumber,
                                        'grund' => $reason,
                                        'daten' => $mapped,
                                    ]);
                                }
                            }
                        });
                    }

                    if ($skipDetails !== []) {
                        $logHeader = sprintf(
                            "=== CSV-Import %s: %s ===\n",
                            $entityPluralLabel,
                            now()->format('Y-m-d H:i:s')
                        );

                        file_put_contents(
                            storage_path(self::LOG_PATH),
                            $logHeader.implode("\n", $skipDetails)."\n"
                        );
                    }

                    $title = "{$imported} {$entityPluralLabel} importiert, {$skipped} übersprungen";

                    $notification = Notification::make()->title($title);

                    if ($skipDetails !== []) {
                        $shown = array_slice($skipDetails, 0, 10);
                        $body = implode("\n", $shown);

                        $remaining = count($skipDetails) - count($shown);

                        if ($remaining > 0) {
                            $body .= "\n\nWeitere {$remaining} Zeilen übersprungen – Details in storage/".self::LOG_PATH;
                        }

                        $notification
                            ->body($body)
                            ->warning()
                            ->persistent();
                    } else {
                        $notification->success();
                    }

                    $notification->send();
                } catch (Throwable $e) {
                    Log::error('CSV-Import fehlgeschlagen', [
                        'entitaet' => $entityPluralLabel,
                        'fehler' => $e->getMessage(),
                    ]);

                    Notification::make()
                        ->title('CSV-Import fehlgeschlagen')
                        ->body('Die Datei konnte nicht verarbeitet werden. Bitte überprüfen Sie das Format.')
                        ->danger()
                        ->send();
                }
            });
    }

    /**
     * A secondary header button that lets admins open the full detailed
     * skip-reason log (written by the most recent CSV import run, of any
     * resource) in a read-only modal, without needing to open the shell.
     */
    public static function viewLogAction(): Action
    {
        return Action::make('viewImportLog')
            ->label('Log anzeigen')
            ->icon('heroicon-o-document-text')
            ->color('gray')
            ->modalWidth('2xl')
            ->modalHeading('Import-Log')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Schließen')
            ->modalContent(function () {
                $path = storage_path(self::LOG_PATH);

                if (! is_file($path)) {
                    $content = 'Es liegt noch kein Import-Log vor.';
                } else {
                    $content = trim((string) file_get_contents($path));

                    if ($content === '') {
                        $content = 'Das Import-Log ist leer.';
                    }
                }

                return new HtmlString(
                    '<pre class="fi-in-text text-xs text-gray-700 dark:text-gray-300 whitespace-pre-wrap max-h-[60vh] overflow-y-auto bg-gray-50 dark:bg-white/5 rounded-lg p-3">'
                    .e($content)
                    .'</pre>'
                );
            });
    }

    /**
     * Returns the first non-empty value from a mapped row, for use as a
     * human-readable identifier (e.g. a name or email) next to a skip
     * reason, so admins don't have to cross-reference the CSV by line
     * number alone.
     *
     * @param  array<string, string|null>  $mapped
     */
    protected static function firstNonEmptyMappedValue(array $mapped): ?string
    {
        foreach ($mapped as $key => $value) {
            if ($value !== null && $value !== '' && $value !== self::AUTO_SLUG) {
                return "{$key}: {$value}";
            }
        }

        return null;
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

    /**
     * @param  array<int, array{key: string, label: string, icon?: string, required?: bool, special?: array{value: string, label: string}}>  $fields
     */
    protected static function renderPreview(Get $get, array $fields): string|HtmlString
    {
        $analysis = self::tryAnalyze($get('csv_file'));

        if ($analysis === null) {
            return '';
        }

        try {
            $path = self::resolvePath($get('csv_file'));
            $rows = array_slice(CsvImportHelper::readRows($path, $analysis['delimiter']), 0, 3);
        } catch (Throwable $e) {
            return '';
        }

        if ($rows === []) {
            return '';
        }

        $mappedFields = [];

        foreach ($fields as $field) {
            $column = $get("mapping_{$field['key']}");

            if ($column && $column !== self::IGNORE) {
                $mappedFields[] = $field;
            }
        }

        if ($mappedFields === []) {
            return '';
        }

        $headerHtml = '';

        foreach ($mappedFields as $field) {
            $headerHtml .= '<th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">'
                .e(($field['icon'] ?? '').' '.$field['label']).'</th>';
        }

        $bodyHtml = '';

        foreach ($rows as $row) {
            $bodyHtml .= '<tr class="border-t border-gray-100 dark:border-white/5">';

            foreach ($mappedFields as $field) {
                $column = $get("mapping_{$field['key']}");
                $value = ($column === self::AUTO_SLUG) ? '(automatisch)' : ($row[$column] ?? '');
                $bodyHtml .= '<td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-300 truncate max-w-[10rem]">'
                    .e((string) $value).'</td>';
            }

            $bodyHtml .= '</tr>';
        }

        return new HtmlString(
            '<div class="fi-in-text text-sm text-gray-500 dark:text-gray-400 mb-1">👁 Vorschau (erste 3 Zeilen)</div>'
            .'<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10">'
            .'<table class="min-w-full divide-y divide-gray-200 dark:divide-white/10"><thead><tr>'.$headerHtml.'</tr></thead>'
            .'<tbody>'.$bodyHtml.'</tbody></table></div>'
        );
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

            return CsvImportHelper::ensureUtf8Path($path);
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
            return CsvImportHelper::ensureUtf8Path($file);
        }

        throw new RuntimeException('Unbekannter Dateityp.');
    }
}
