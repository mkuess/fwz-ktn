<?php

namespace App\Services;

use App\Filament\Support\CsvImportHelper;
use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrganisationMemberCsvComparison
{
    public function read(string $path): array
    {
        $utf8 = CsvImportHelper::ensureUtf8Path($path);
        $handle = null;

        try {
            $delimiter = CsvImportHelper::analyze($utf8)['delimiter'];
            $handle = fopen($utf8, 'r');
            if ($handle === false) {
                $this->invalid('Die CSV-Datei konnte nicht gelesen werden.');
            }

            $headers = fgetcsv($handle, 0, $delimiter, '"', '');
            if (! $headers || count($headers) < 3 || count($headers) > 500) {
                $this->invalid('Die CSV benötigt eine Kopfzeile mit mindestens drei und höchstens 500 Spalten.');
            }
            $headers = array_map(fn ($value): string => trim((string) $value), $headers);
            $rows = [];
            while (($row = fgetcsv($handle, 0, $delimiter, '"', '')) !== false) {
                if (! array_filter($row, fn ($value): bool => trim((string) $value) !== '')) {
                    continue;
                }
                if (count($row) > count($headers)) {
                    $this->invalid('Eine CSV-Zeile enthält mehr Spalten als die Kopfzeile. Bitte prüfe das Trennzeichen und die Datei.');
                }
                if (count($rows) >= 20000) {
                    $this->invalid('Bitte verwende eine CSV mit höchstens 20.000 Mitgliedern.');
                }
                $rows[] = array_map(fn ($value): string => trim((string) $value), $row);
            }
            if ($rows === []) {
                $this->invalid('Die CSV enthält keine Mitgliederzeilen.');
            }

            return compact('headers', 'rows', 'delimiter');
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
            if ($utf8 !== $path) {
                @unlink($utf8);
            }
        }
    }

    public function suggest(array $headers): array
    {
        $aliases = [
            'email' => ['email', 'emailadresse', 'mail', 'mailadresse', 'emailaddress'],
            'first_name' => ['vorname', 'firstname', 'givenname'],
            'last_name' => ['nachname', 'lastname', 'surname', 'familienname'],
        ];
        $mapping = array_fill_keys(array_keys($aliases), null);
        foreach ($headers as $index => $header) {
            $normalized = preg_replace('/[^a-z0-9]/', '', Str::lower(Str::ascii($header)));
            foreach ($aliases as $field => $names) {
                if ($mapping[$field] === null && in_array($normalized, $names, true)) {
                    $mapping[$field] = (string) $index;
                }
            }
        }

        return $mapping;
    }

    public function compare(array $csv, array $mapping, int $organisationId): array
    {
        $columns = [];
        foreach (['email', 'first_name', 'last_name'] as $field) {
            $index = $mapping[$field] ?? null;
            if (! is_scalar($index) || ! ctype_digit((string) $index) || ! array_key_exists((int) $index, $csv['headers'])) {
                throw ValidationException::withMessages(['mappingData.'.$field => 'Bitte wähle eine gültige CSV-Spalte aus.']);
            }
            $columns[$field] = (int) $index;
        }
        if (count(array_unique($columns)) !== 3) {
            throw ValidationException::withMessages(['mappingData.email' => 'E-Mail, Vorname und Nachname müssen unterschiedliche Spalten verwenden.']);
        }

        $emails = [];
        $names = [];
        $invalid = 0;
        $usable = 0;
        foreach ($csv['rows'] as $index => $row) {
            $first = $row[$columns['first_name']] ?? '';
            $last = $row[$columns['last_name']] ?? '';
            $name = $this->name($first, $last);
            $addresses = array_values(array_unique(array_filter(
                preg_split('/[;,\s]+/u', Str::lower($row[$columns['email']] ?? '')),
                fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false
            )));
            if ($addresses === [] && $name === null) {
                $invalid++;

                continue;
            }
            $usable++;
            $entry = ['name' => trim($first.' '.$last), 'name_key' => $name, 'emails' => $addresses];
            foreach ($addresses as $email) {
                $emails[$email][] = $entry;
            }
            if ($name !== null) {
                $names[$name][] = $entry;
            }
        }
        if ($usable === 0) {
            throw ValidationException::withMessages(['mappingData.email' => 'Mit dieser Zuordnung wurden keine gültigen E-Mail-Adressen oder vollständigen Namen gefunden.']);
        }

        $result = ['csv_rows' => count($csv['rows']), 'usable_rows' => $usable, 'invalid_rows' => $invalid,
            'total' => 0, 'matched' => 0, 'missing' => 0, 'review' => 0, 'issues' => []];
        foreach (Member::query()->where('organisation_id', $organisationId)->cursor() as $member) {
            $result['total']++;
            $name = $this->name($member->first_name, $member->last_name);
            $byEmail = $emails[Str::lower(trim((string) $member->email))] ?? [];
            $byName = $name === null ? [] : ($names[$name] ?? []);
            if ($byEmail !== [] && $name !== null && collect($byEmail)->every(fn ($entry): bool => $entry['name_key'] === $name)) {
                $result['matched']++;

                continue;
            }
            $candidates = $byEmail !== [] ? $byEmail : $byName;
            $kind = $candidates === [] ? 'missing' : 'review';
            $result[$kind]++;
            $reason = match (true) {
                $byEmail !== [] => 'E-Mail gefunden, Name fehlt oder weicht ab.',
                count($byName) > 1 => 'Name mehrfach gefunden; E-Mail stimmt nicht überein.',
                $byName !== [] => 'Name gefunden; E-Mail fehlt oder weicht ab.',
                default => 'Weder E-Mail noch vollständiger Name in der CSV gefunden.',
            };
            $result['issues'][$member->id] = [
                'kind' => $kind, 'reason' => $reason,
                'csv_names' => implode(', ', array_unique(array_column($candidates, 'name'))),
                'csv_emails' => implode('; ', array_unique(array_merge([], ...array_column($candidates, 'emails')))),
            ];
        }

        return $result;
    }

    private function name(string $first, string $last): ?string
    {
        $normalize = fn (string $value): string => Str::lower(preg_replace('/\s+/u', ' ', trim($value)));
        $first = $normalize($first);
        $last = $normalize($last);

        return $first !== '' && $last !== '' ? $first."\0".$last : null;
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['uploadData.csv' => $message]);
    }
}
