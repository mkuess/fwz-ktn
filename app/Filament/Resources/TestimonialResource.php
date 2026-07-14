<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Sonstiges';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Stimmen';

    protected static ?string $modelLabel = 'Stimme';

    protected static ?string $pluralModelLabel = 'Stimmen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('photo_path')
                    ->label('Foto')
                    ->image()
                    ->directory('testimonials')
                    ->nullable(),
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('organisation')
                    ->label('Organisation')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\Textarea::make('quote')
                    ->label('Zitat')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktiv')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Reihenfolge')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(null),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organisation')
                    ->label('Organisation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quote')
                    ->label('Zitat')
                    ->limit(60),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktiv')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Reihenfolge')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Aktiv'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit'   => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
