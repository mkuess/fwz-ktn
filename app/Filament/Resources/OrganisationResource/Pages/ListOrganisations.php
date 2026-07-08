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
                ->label('CSV importieren')
                ->modalDescription('Pflichtspalten: type, name, email, password. Optionale Spalten: zvr_number, description, street, zip, city, phone, website, representative, contact_person. Importierte Organisationen werden als nicht freigeschalten (is_approved = false) angelegt und müssen geprüft werden.'),
            Actions\CreateAction::make(),
        ];
    }
}
