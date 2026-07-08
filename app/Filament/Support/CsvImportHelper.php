<?php

namespace App\Filament\Support;

use Generator;
use League\Csv\Reader;
use RuntimeException;

class CsvImportHelper
{
    /**
     * @var array<string, string>
     */
    protected static array $delimiters = [
        ';' => 'Semikolon',
        ',' => 'Komma',
        "\t" => 'Tabulator',
    ];

    /**
     * Detects the most likely delimiter (;, then ,, then tab) and returns the
     * detected delimiter, a human readable label and the header columns.
     *
     * @return array{delimiter: string, delimiterLabel: string, headers: array<int, string>}
     */
    public static function analyze(string $path): array
    {
        foreach (self::$delimiters as $delimiter => $label) {
            $headers = self::readHeaders($path, $delimiter);

            if (count($headers) > 1) {
                return [
                    'delimiter' => $delimiter,
                    'delimiterLabel' => $label,
                    'headers' => $headers,
                ];
            }
        }

        // Nothing produced more than one column, fall back to comma.
        return [
            'delimiter' => ',',
            'delimiterLabel' => self::$delimiters[','],
            'headers' => self::readHeaders($path, ','),
        ];
    }

    /**
     * @return array<int, string>
     */
    protected static function readHeaders(string $path, string $delimiter): array
    {
        return self::cleanRow(self::readRawFirstLine($path, $delimiter));
    }

    /**
     * Reads and parses only the first line of the file with the given
     * delimiter, using str_getcsv() so quoted values (e.g. "creationDate")
     * are unwrapped correctly.
     *
     * @return array<int, string>
     */
    protected static function readRawFirstLine(string $path, string $delimiter): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException("Datei konnte nicht geöffnet werden: {$path}");
        }

        try {
            $line = fgets($handle);

            if ($line === false) {
                return [];
            }

            // Strip a UTF-8 byte order mark, which some spreadsheet exports
            // (e.g. Excel) prepend to the very first header cell.
            $line = self::stripBom($line);

            $fields = str_getcsv($line, $delimiter, '"', '\\');

            return $fields === [null] ? [] : $fields;
        } finally {
            fclose($handle);
        }
    }

    protected static function stripBom(string $value): string
    {
        $bom = "\xEF\xBB\xBF";

        return str_starts_with($value, $bom) ? substr($value, strlen($bom)) : $value;
    }

    /**
     * Ensures the given CSV file is UTF-8 encoded. Many spreadsheet exports
     * (e.g. Excel on Windows) save CSVs as ISO-8859-1 / Windows-1252, which
     * turns German umlauts (ü, ö, ä, ß) into garbage characters once read as
     * UTF-8. When a non-UTF-8 encoding is detected, the file is converted
     * and written to a new temporary file whose path is returned. If the
     * file is already UTF-8 (or its encoding can't be determined), the
     * original path is returned unchanged.
     */
    public static function ensureUtf8Path(string $path): string
    {
        $rawContent = file_get_contents($path);

        if ($rawContent === false || $rawContent === '') {
            return $path;
        }

        // mb_detect_encoding(..., 'UTF-8', true) returns false when the
        // content is NOT valid UTF-8. That's the signal that this is a
        // Windows/Excel export that needs converting — Windows-1252 is by
        // far the most common encoding for Austrian/German spreadsheet
        // exports, so we treat it as the default fallback.
        if (mb_detect_encoding($rawContent, 'UTF-8', true) === false) {
            $rawContent = mb_convert_encoding($rawContent, 'UTF-8', 'Windows-1252');
        }

        // Strip a UTF-8 byte order mark, which some spreadsheet exports
        // (e.g. Excel) prepend to the very first header cell.
        $rawContent = preg_replace('/^\xEF\xBB\xBF/', '', $rawContent);

        $tmpPath = tempnam(sys_get_temp_dir(), 'csv_');

        if ($tmpPath === false) {
            return $path;
        }

        file_put_contents($tmpPath, $rawContent);

        return $tmpPath;
    }

    /**
     * @param  array<int, string|null>  $row
     * @return array<int, string>
     */
    protected static function cleanRow(array $row): array
    {
        return array_values(array_filter(
            array_map(fn ($value) => trim((string) $value, "\xEF\xBB\xBF\"\r\n "), $row),
            fn (string $value) => $value !== ''
        ));
    }

    /**
     * Yields each data row as an associative array keyed by (trimmed) header name.
     *
     * @return Generator<int, array<string, string|null>>
     */
    public static function readRows(string $path, string $delimiter): Generator
    {
        $csv = Reader::createFromPath($path, 'r');
        $csv->setDelimiter($delimiter);
        $csv->setEnclosure('"');
        $csv->setEscape('\\');
        $csv->skipInputBOM();
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            $row = [];

            foreach ($record as $header => $value) {
                $row[trim((string) $header)] = is_string($value) ? trim($value) : $value;
            }

            yield $row;
        }
    }
}
