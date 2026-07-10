<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use App\Models\Organisation;
use App\Models\VolunteerListing;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Organisationen', Organisation::count())
                ->description('Registrierte Organisationen')
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('Mitglieder gesamt', Member::count())
                ->description('Alle registrierten Mitglieder')
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Neuanmeldungen', Member::where('status', 'pending')->where('role', 'member')->count())
                ->description('Ausstehende Freischaltungen')
                ->icon('heroicon-o-user-plus')
                ->color('warning'),

            Stat::make('Aktuelle Gesuche', VolunteerListing::where('is_active', true)->count())
                ->description('Aktive Gesuche')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('success'),
        ];
    }
}
