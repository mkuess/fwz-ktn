<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VolunteerListingResource\Pages;
use App\Models\VolunteerListing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VolunteerListingResource extends Resource
{
    protected static ?string $model = VolunteerListing::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    protected static ?string $modelLabel = 'Gesuch';

    protected static ?string $pluralModelLabel = 'Gesuche';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('organisation_id')
                    ->label('Organisation')
                    ->relationship('organisation', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('Titel')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Beschreibung')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('website_link')
                    ->label('Website-Link')
                    ->url()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_spontaneous')
                    ->label('Spontansuche')
                    ->default(false),
                Forms\Components\TextInput::make('street')
                    ->label('Straße')
                    ->maxLength(255),
                Forms\Components\TextInput::make('zip')
                    ->label('PLZ')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->label('Ort')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('valid_until')
                    ->label('Gültig bis'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktiv')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organisation.name')
                    ->label('Organisation')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktiv')
                    ->boolean(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Gültig bis')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktiv'),
                Tables\Filters\SelectFilter::make('organisation_id')
                    ->label('Organisation')
                    ->relationship('organisation', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVolunteerListings::route('/'),
            'create' => Pages\CreateVolunteerListing::route('/create'),
            'edit' => Pages\EditVolunteerListing::route('/{record}/edit'),
        ];
    }
}
