<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardStatsOverview;
use App\Filament\Widgets\LatestMembersWidget;
use App\Filament\Widgets\LatestOrganisationsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            DashboardStatsOverview::class,
            LatestOrganisationsWidget::class,
            LatestMembersWidget::class,
        ];
    }
}
