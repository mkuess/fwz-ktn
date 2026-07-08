<?php

namespace App\Filament\Imports;

use App\Models\Organisation;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class OrganisationImporter extends Importer
{
    protected static ?string $model = Organisation::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('type')
                ->requiredMapping()
                ->rules(['required', 'in:verein,organisation']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('password')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('zvr_number'),
            ImportColumn::make('description'),
            ImportColumn::make('street'),
            ImportColumn::make('zip'),
            ImportColumn::make('city'),
            ImportColumn::make('phone'),
            ImportColumn::make('website'),
            ImportColumn::make('representative'),
            ImportColumn::make('contact_person'),
        ];
    }

    public function resolveRecord(): ?Organisation
    {
        return new Organisation();
    }

    protected function beforeSave(): void
    {
        $this->record->is_approved = false;
        $this->record->is_active = true;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your organisation import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
