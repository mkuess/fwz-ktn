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
                    ['key' => 'first_name', 'label' => 'Vorname', 'icon' => '👤', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'last_name', 'label' => 'Nachname', 'icon' => '👤', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'email', 'label' => 'E-Mail', 'icon' => '📧', 'required' => true, 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'organisation_id', 'label' => 'Organisation', 'icon' => '🏢', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'newsletter_optin', 'label' => 'Newsletter', 'icon' => '📰', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                    ['key' => 'role', 'label' => 'Rolle', 'icon' => '🎭', 'special' => ['value' => SmartCsvImportAction::IGNORE, 'label' => '(ignorieren)']],
                ],
                importRow: function (array $mapped): bool|string {
                    $firstName = $mapped['first_name'] ?? null;
                    $lastName = $mapped['last_name'] ?? null;
                    $email = $mapped['email'] ?? null;

                    if (! $firstName || ! $lastName || ! $email) {
                        return 'ohne Pflichtfelder';
                    }

                    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return 'mit ungültiger E-Mail';
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
                            return 'Organisation nicht gefunden';
                        }
                    }

                    $newsletterRaw = strtolower((string) ($mapped['newsletter_optin'] ?? ''));
                    $newsletterOptin = in_array($newsletterRaw, ['1', 'true', 'yes', 'ja'], true);

                    Member::updateOrCreate(
                        ['email' => $email],
                        [
                            'organisation_id' => $organisationId,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'newsletter_optin' => $newsletterOptin,
                            'role' => $mapped['role'] ?: null,
                            'status' => 'pending',
                            'source' => 'csv',
                        ]
                    );

                    return true;
                },
                entityPluralLabel: 'Mitglieder',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
