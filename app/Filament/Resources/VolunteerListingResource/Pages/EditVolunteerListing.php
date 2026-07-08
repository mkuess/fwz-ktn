<?php

namespace App\Filament\Resources\VolunteerListingResource\Pages;

use App\Filament\Resources\VolunteerListingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVolunteerListing extends EditRecord
{
    protected static string $resource = VolunteerListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
