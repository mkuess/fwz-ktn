<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VolunteerListingActivityResource\Pages;
use App\Models\VolunteerListingActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VolunteerListingActivityResource extends Resource
{
    protected static ?string $model = VolunteerListingActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Gesuch-Aktivitäten';

    protected static ?string $navigationGroup = 'Gesuche';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Aktivität';

    protected static ?string $pluralModelLabel = 'Aktivitäten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->live(onBlur: true)
                    ->maxLength(255)
                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    }),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Reihenfolge')
                    ->numeric()
                    ->default(0),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktiv')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Reihenfolge')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                //
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
            'index' => Pages\ListVolunteerListingActivities::route('/'),
            'create' => Pages\CreateVolunteerListingActivity::route('/create'),
            'edit' => Pages\EditVolunteerListingActivity::route('/{record}/edit'),
        ];
    }
}
