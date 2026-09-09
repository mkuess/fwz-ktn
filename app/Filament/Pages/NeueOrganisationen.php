<?php

namespace App\Filament\Pages;

use App\Filament\Resources\OrganisationResource;
use App\Models\Organisation;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class NeueOrganisationen extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Neue Organisationen';

    protected static ?string $title = 'Neue Organisationen';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.neue-organisationen';

    public static function getNavigationBadge(): ?string
    {
        $count = Organisation::where('approval_status', 'pending')->count();

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
                Organisation::query()
                    ->where('approval_status', 'pending')
                    ->withoutTrashed()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Organisation')
                    ->description(fn (Organisation $record): string => $record->email ?? '')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->lineClamp(2)
                    ->extraAttributes(['style' => 'width: 40%; max-width: 22rem; overflow-wrap: anywhere;']),
                Tables\Columns\TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->extraAttributes(['style' => 'width: 9rem;'])
                    ->color(fn (string $state): string => match ($state) {
                        'verein' => 'success',
                        'organisation' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ort')
                    ->placeholder('-')
                    ->wrap()
                    ->lineClamp(2)
                    ->extraAttributes(['style' => 'width: 12rem; max-width: 12rem; overflow-wrap: anywhere;']),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Angemeldet')
                    ->since()
                    ->tooltip(fn (Organisation $record): string => $record->created_at?->format('d.m.Y H:i') ?? '')
                    ->sortable()
                    ->extraAttributes(['style' => 'width: 8rem;']),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktiv')
                    ->boolean()
                    ->extraAttributes(['style' => 'width: 4rem;']),
            ])
            ->recordUrl(fn (Organisation $record): string => OrganisationResource::getUrl('edit', ['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 30, 50]);
    }
}
