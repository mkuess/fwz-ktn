<?php

namespace App\Filament\Resources\VolunteerListingActivityResource\Pages;

use App\Filament\Resources\VolunteerListingActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVolunteerListingActivity extends EditRecord
{
    protected static string $resource = VolunteerListingActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
