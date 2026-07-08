<?php

namespace App\Filament\Support;

use Generator;
use League\Csv\Reader;

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
        $csv = Reader::createFromPath($path, 'r');
        $csv->setDelimiter($delimiter);
        $csv->setHeaderOffset(0);

        return array_values(array_filter(
            array_map(fn ($header) => trim((string) $header), $csv->getHeader())
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
