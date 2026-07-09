<?php

namespace App\Filament\Resources\VolunteerListingCategoryResource\Pages;

use App\Filament\Resources\VolunteerListingCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVolunteerListingCategory extends EditRecord
{
    protected static string $resource = VolunteerListingCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
