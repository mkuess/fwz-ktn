<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrganisationResource;
use App\Models\Organisation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrganisationsWidget extends BaseWidget
{
    protected static ?string $heading = 'Neue Organisationen';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Organisation::query()
                    ->where('approval_status', 'pending')
                    ->withoutTrashed()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Organisation')
                    ->description(fn (Organisation $record): string => $record->email ?? ''),
                Tables\Columns\TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verein' => 'success',
                        'organisation' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Angemeldet')
                    ->since()
                    ->tooltip(fn (Organisation $record): string => $record->created_at?->format('d.m.Y H:i') ?? ''),
            ])
            ->actions([
                Tables\Actions\Action::make('bearbeiten')
                    ->label('Bearbeiten')
                    ->url(fn (Organisation $record): string => OrganisationResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('alle_anzeigen')
                    ->label('Alle neuen Organisationen')
                    ->url('/verwaltung/neue-organisationen')
                    ->icon('heroicon-o-arrow-right')
                    ->color('warning'),
            ])
            ->paginated(false);
    }
}
