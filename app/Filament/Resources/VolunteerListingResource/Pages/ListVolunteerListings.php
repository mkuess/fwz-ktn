<?php

namespace App\Filament\Resources\VolunteerListingResource\Pages;

use App\Filament\Resources\VolunteerListingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVolunteerListings extends ListRecords
{
    protected static string $resource = VolunteerListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
