<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListNeuanmeldungen extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected static ?string $title = 'Neuanmeldungen';

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Neuanmeldungen';

    protected static ?string $navigationGroup = 'Mitglieder';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return (string) Member::where('status', 'pending')
            ->where('role', 'member')
            ->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', 'pending')
            ->where('role', 'member');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
