<?php

namespace App\Filament\Resources\OrganisationResource\Pages;

use App\Filament\Imports\OrganisationImporter;
use App\Filament\Resources\OrganisationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganisations extends ListRecords
{
    protected static string $resource = OrganisationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(OrganisationImporter::class)
                ->label('Import CSV')
                ->modalDescription('Required columns: type, name, email, password. Optional columns: zvr_number, description, street, zip, city, phone, website, representative, contact_person. Imported organisations are created as unapproved (is_approved = false) and will need to be reviewed.'),
            Actions\CreateAction::make(),
        ];
    }
}
