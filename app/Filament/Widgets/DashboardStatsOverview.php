<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use App\Models\Organisation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Organisations', Organisation::count())
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),
            Stat::make('Pending Organisations', Organisation::where('is_approved', false)->count())
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Total Members', Member::count())
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('Pending Members', Member::where('status', 'pending')->count())
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
