<?php

namespace App\Filament\Organisation\Pages;

use App\Filament\Organisation\Support\MemberTable;
use App\Models\Member;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Dashboard extends BaseDashboard implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.organisation.dashboard';

    public function table(Table $table): Table
    {
        $query = Member::query();
        $since = session('organisation_login_since');

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        return MemberTable::configure($table, $query)
            ->emptyStateHeading('Keine Neuanmeldungen')
            ->emptyStateActions([
                Action::make('viewAllMembers')
                    ->label('Alle Mitglieder ansehen')
                    ->url(fn (): string => Members::getUrl(panel: 'organisation'))
                    ->button(),
            ]);
    }

    public function getWidgets(): array
    {
        return [];
    }
}
