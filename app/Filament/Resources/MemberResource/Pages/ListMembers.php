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
                ->label('CSV importieren')
                ->modalDescription('Pflichtspalten: first_name, last_name, email, organisation_id. Optionale Spalte: newsletter_optin (1/0). Importierte Mitglieder werden mit Status = Ausstehend und Quelle = CSV-Import angelegt.'),
            Actions\CreateAction::make(),
        ];
    }
}
