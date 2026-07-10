<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardStatsOverview;
use App\Filament\Widgets\LatestSignupsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            DashboardStatsOverview::class,
            LatestSignupsWidget::class,
        ];
    }
}
