<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoginLogResource\Pages;
use App\Models\LoginLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoginLogResource extends Resource
{
    protected static ?string $model = LoginLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Verwaltung';

    protected static ?int $navigationSort = 98;

    protected static ?string $navigationLabel = 'Login Log';

    protected static ?string $modelLabel = 'Login-Eintrag';

    protected static ?string $pluralModelLabel = 'Login Log';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Zeitpunkt')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('member_name')
                    ->label('Benutzer')
                    ->placeholder('Unbekannt')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-Mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('successful')
                    ->label('Ergebnis')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Erfolgreich' : 'Fehlgeschlagen')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('failure_reason')
                    ->label('Problem')
                    ->placeholder('—')
                    ->wrap(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP-Adresse')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Browser')
                    ->limit(50)
                    ->tooltip(fn (?LoginLog $record): ?string => $record?->user_agent)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('successful')
                    ->label('Ergebnis')
                    ->trueLabel('Erfolgreich')
                    ->falseLabel('Fehlgeschlagen')
                    ->placeholder('Alle'),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoginLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
