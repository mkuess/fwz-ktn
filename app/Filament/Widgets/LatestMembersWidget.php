<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestMembersWidget extends BaseWidget
{
    protected static ?string $heading = 'Neuanmeldungen Mitglieder';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Member::query()
                    ->where('status', 'pending')
                    ->where('role', 'member')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->state(fn (Member $record): string => trim(($record->first_name ?? '').' '.($record->last_name ?? '')) ?: '-')
                    ->description(fn (Member $record): string => $record->email ?? ''),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Angemeldet')
                    ->since()
                    ->tooltip(fn (Member $record): string => $record->created_at?->format('d.m.Y H:i') ?? ''),
                Tables\Columns\TextColumn::make('organisation.name')
                    ->label('Organisation')
                    ->placeholder('-'),
            ])
            ->actions([
                Tables\Actions\Action::make('bearbeiten')
                    ->label('Bearbeiten')
                    ->url(fn (Member $record): string => MemberResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('alle_anzeigen')
                    ->label('Alle Neuanmeldungen anzeigen')
                    ->url('/verwaltung/neuanmeldungen')
                    ->icon('heroicon-o-arrow-right')
                    ->color('warning'),
            ])
            ->paginated(false);
    }
}
