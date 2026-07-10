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

    protected static ?string $navigationGroup = 'Gesuche';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Gesuch';

    protected static ?string $pluralModelLabel = 'Gesuche';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\Select::make('organisation_id')
                    ->label('Organisation')
                    ->relationship('organisation', 'name')
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('title')
                    ->label('Titel')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Beschreibung')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('website_link')
                    ->label('Website-Link')
                    ->url()
                    ->columnSpanFull(),

                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('street')->label('Straße'),
                    Forms\Components\TextInput::make('zip')->label('PLZ')->maxLength(10),
                    Forms\Components\TextInput::make('city')->label('Ort'),
                ])->columnSpanFull(),

                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\DatePicker::make('valid_until')->label('Gültig bis'),
                    Forms\Components\Toggle::make('is_active')->label('Aktiv')->default(true)->inline(false),
                ])->columnSpanFull(),

                Forms\Components\Section::make('Spontansuche')
                    ->description('Aktivieren wenn Freiwillige spontan und flexibel gesucht werden')
                    ->schema([
                        Forms\Components\Toggle::make('is_spontaneous')
                            ->label('Spontansuche')
                            ->live()
                            ->default(false),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\CheckboxList::make('weekdays')
                                    ->label('Wochentage')
                                    ->options([
                                        'montag' => 'Montag',
                                        'dienstag' => 'Dienstag',
                                        'mittwoch' => 'Mittwoch',
                                        'donnerstag' => 'Donnerstag',
                                        'freitag' => 'Freitag',
                                        'samstag' => 'Samstag',
                                        'sonntag' => 'Sonntag',
                                    ])
                                    ->nullable()
                                    ->visible(fn (Forms\Get $get): bool => ! $get('is_spontaneous')),

                                Forms\Components\CheckboxList::make('daytimes')
                                    ->label('Tageszeiten')
                                    ->options([
                                        'vormittags' => 'Vormittags',
                                        'mittags' => 'Mittags',
                                        'nachmittags' => 'Nachmittags',
                                        'abends' => 'Abends',
                                    ])
                                    ->nullable()
                                    ->visible(fn (Forms\Get $get): bool => ! $get('is_spontaneous')),
                            ]),

                        Forms\Components\TextInput::make('hours_per_week')
                            ->label('Zeitaufwand pro Woche (in Stunden)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(168)
                            ->nullable()
                            ->visible(fn (Forms\Get $get): bool => ! $get('is_spontaneous')),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('categories')
                        ->label('Kategorien')
                        ->multiple()
                        ->relationship('categories', 'name', fn ($query) => $query->where('is_active', true)->orderBy('sort_order'))
                        ->searchable()
                        ->preload()
                        ->columnSpan(1),

                    Forms\Components\Select::make('activities')
                        ->label('Aktivitäten')
                        ->multiple()
                        ->relationship('activities', 'name', fn ($query) => $query->where('is_active', true)->orderBy('sort_order'))
                        ->searchable()
                        ->preload()
                        ->columnSpan(1),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(30)
            ->paginationPageOptions([10, 30, 50, 100])
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
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Kategorien')
                    ->badge()
                    ->color('success')
                    ->placeholder('-'),
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
