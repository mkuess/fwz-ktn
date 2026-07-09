<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Sonstiges';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Artikel';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titel')
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
                Forms\Components\Textarea::make('excerpt')
                    ->label('Kurztext')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('body')
                    ->label('Inhalt')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('cover_image_path')
                    ->label('Titelbild')
                    ->image()
                    ->directory('articles/covers'),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Veröffentlicht am'),
                Forms\Components\Toggle::make('is_published')
                    ->label('Veröffentlicht')
                    ->default(false),
                Forms\Components\Repeater::make('attachments')
                    ->label('Anhänge')
                    ->relationship('attachments')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->required()
                            ->directory('articles/attachments')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                    $set('original_name', $state->getClientOriginalName());
                                    $set('mime_type', $state->getMimeType());
                                    $set('file_size', $state->getSize());
                                }
                            }),
                        Forms\Components\TextInput::make('original_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Hidden::make('mime_type')->default(''),
                        Forms\Components\Hidden::make('file_size')->default(0),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->addActionLabel('Anhang hinzufügen'),
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
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Veröffentlicht')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Veröffentlicht am')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
