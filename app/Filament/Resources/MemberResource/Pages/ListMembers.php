<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Filament\Support\SmartCsvImportAction;
use App\Models\Member;
use App\Models\Organisation;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SmartCsvImportAction::make(
                name: 'importCsv',
                label: 'CSV importieren',
                fields: [
                    ['key' => 'first_name', 'label' => 'Vorname', 'icon' => '👤', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'last_name', 'label' => 'Nachname', 'icon' => '👤', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'email', 'label' => 'E-Mail', 'icon' => '📧', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'organisation_id', 'label' => 'Organisation', 'icon' => '🏢', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'street', 'label' => 'Straße', 'icon' => '🏠', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'zip', 'label' => 'PLZ', 'icon' => '📮', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'city', 'label' => 'Ort', 'icon' => '🌆', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'newsletter_optin', 'label' => 'Newsletter', 'icon' => '📰', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                ],
                importRow: function (array $mapped): array|bool|string {
                    $firstName = $mapped['first_name'] ?? '';
                    $lastName = $mapped['last_name'] ?? '';
                    $email = $mapped['email'] ?? null;

                    if (! $email) {
                        return 'fehlendes Pflichtfeld: E-Mail';
                    }

                    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return "ungültige E-Mail '{$email}' (Name: {$firstName} {$lastName})";
                    }

                    $organisationRaw = $mapped['organisation_id'] ?? null;
                    $organisationId = null;

                    if ($organisationRaw !== null && $organisationRaw !== '') {
                        if (ctype_digit((string) $organisationRaw)) {
                            $organisationId = Organisation::whereKey((int) $organisationRaw)->exists()
                                ? (int) $organisationRaw
                                : null;
                        } else {
                            $organisationId = Organisation::where('name', 'LIKE', "%{$organisationRaw}%")->first()?->id;
                        }

                        if ($organisationId === null) {
                            return "Organisation '{$organisationRaw}' nicht gefunden (Name: {$firstName} {$lastName})";
                        }
                    }

                    $existing = Member::withTrashed()->where('email', $email)->first();
                    $isNew = $existing === null;

                    $updateData = array_filter([
                        'first_name' => $firstName ?: null,
                        'last_name' => $lastName ?: null,
                        'organisation_id' => $organisationId,
                        'street' => $mapped['street'] ?: null,
                        'zip' => $mapped['zip'] ?: null,
                        'city' => $mapped['city'] ?: null,
                    ], fn ($value) => $value !== null && $value !== '');

                    $newsletterRaw = trim((string) ($mapped['newsletter_optin'] ?? ''));

                    if ($newsletterRaw !== '') {
                        $updateData['newsletter_optin'] = in_array(strtolower($newsletterRaw), ['1', 'true', 'yes', 'ja'], true);
                    }

                    if ($isNew) {
                        $updateData['source'] = 'csv';
                    }

                    try {
                        if ($existing?->trashed()) {
                            $existing->restore();
                        }

                        Member::withTrashed()->updateOrCreate(
                            ['email' => $email],
                            $updateData
                        );
                    } catch (\Throwable $e) {
                        return "Fehler beim Speichern: {$e->getMessage()} (Name: {$firstName} {$lastName})";
                    }

                    return ['status' => $isNew ? 'created' : 'updated'];
                },
                entityPluralLabel: 'Mitglieder',
            ),
            SmartCsvImportAction::viewLogAction(),
            Actions\CreateAction::make(),
        ];
    }
}
