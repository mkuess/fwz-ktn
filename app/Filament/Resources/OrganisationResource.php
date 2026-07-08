<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganisationResource\Pages;
use App\Models\Organisation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrganisationResource extends Resource
{
    protected static ?string $model = Organisation::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $modelLabel = 'Organisation';

    protected static ?string $pluralModelLabel = 'Organisationen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Radio::make('type')
                    ->label('Typ')
                    ->options([
                        'verein' => 'Verein (ZVR)',
                        'organisation' => 'Organisation / Initiative',
                    ])
                    ->inline()
                    ->live()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('zvr_number')
                    ->label('ZVR-Nummer')
                    ->visible(fn (Forms\Get $get): bool => $get('type') === 'verein')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('E-Mail')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label('Passwort')
                    ->password()
                    ->required()
                    ->visibleOn('create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Beschreibung')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('logo_path')
                    ->label('Logo')
                    ->image()
                    ->directory('organisations/logos'),
                Forms\Components\TextInput::make('street')
                    ->label('Straße')
                    ->maxLength(255),
                Forms\Components\TextInput::make('zip')
                    ->label('PLZ')
                    ->maxLength(10),
                Forms\Components\TextInput::make('city')
                    ->label('Ort')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('website')
                    ->label('Webseite')
                    ->url()
                    ->maxLength(255),
                Forms\Components\TextInput::make('representative')
                    ->label('Vertretungsberechtigte Person')
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_person')
                    ->label('Ansprechpartner:in')
                    ->maxLength(255),
                Forms\Components\Select::make('categories')
                    ->label('Kategorien')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Forms\Components\Toggle::make('is_approved')
                    ->label('Freigeschaltet')
                    ->default(false),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktiv')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(30)
            ->paginationPageOptions([10, 30, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-Mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Stadt')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->boolean()
                    ->label('Freigeschalten'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Freigegeben'),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Typ')
                    ->options([
                        'verein' => 'Verein (ZVR)',
                        'organisation' => 'Organisation / Initiative',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Ausgewählte freischalten')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each(
                            fn (Organisation $organisation) => $organisation->update([
                                'is_approved' => true,
                                'approved_at' => now(),
                            ])
                        ))
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListOrganisations::route('/'),
            'create' => Pages\CreateOrganisation::route('/create'),
            'edit' => Pages\EditOrganisation::route('/{record}/edit'),
        ];
    }
}
