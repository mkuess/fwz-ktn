<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Neuanmeldungen extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Neuanmeldungen';

    protected static ?string $title = 'Neuanmeldungen';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.neuanmeldungen';

    public static function getNavigationBadge(): ?string
    {
        $count = Member::where('status', 'pending')->where('role', 'member')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Member::query()
                    ->where('status', 'pending')
                    ->where('role', 'member')
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Mitglied')
                    ->state(fn (Member $record): string => trim(($record->first_name ?? '').' '.($record->last_name ?? '')) ?: '-')
                    ->description(fn (Member $record): string => $record->email ?? '')
                    ->searchable(['first_name', 'last_name', 'email']),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Angemeldet')
                    ->since()
                    ->tooltip(fn (Member $record): string => $record->created_at?->format('d.m.Y H:i') ?? '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('organisation.name')
                    ->label('Organisation')
                    ->placeholder('-'),
            ])
            ->recordUrl(fn (Member $record): string => MemberResource::getUrl('edit', ['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 30, 50]);
    }
}
