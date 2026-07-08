<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Imports\MemberImporter;
use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(MemberImporter::class)
                ->label('Import CSV')
                ->modalDescription('Required columns: first_name, last_name, email, organisation_id. Optional columns: newsletter_optin (1/0). Imported members are created with status = pending and source = csv.'),
            Actions\CreateAction::make(),
        ];
    }
}
