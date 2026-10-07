<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\OrganisationPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    OrganisationPanelProvider::class,
];
