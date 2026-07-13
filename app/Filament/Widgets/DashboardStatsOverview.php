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
            Stat::make('Organisationen gesamt', Organisation::count())
                ->description('Alle registrierten Organisationen')
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('Ausstehende Organisationen', Organisation::where('is_approved', false)->count())
                ->description('Warten auf Freischaltung')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Mitglieder gesamt', Member::count())
                ->description('Alle registrierten Mitglieder')
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Ausstehende Mitglieder', Member::where('status', 'pending')->count())
                ->description('Warten auf Freischaltung')
                ->icon('heroicon-o-user-plus')
                ->color('warning'),
        ];
    }
}
