<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganisationResource\Pages;
use App\Models\Organisation;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class OrganisationResource extends Resource
{
    protected static ?string $model = Organisation::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Verwaltung';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Organisation';

    protected static ?string $pluralModelLabel = 'Organisationen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                /* ── Info-Karten ─────────────────────────────────────────── */
                Forms\Components\Section::make()
                    ->schema([
                        Placeholder::make('card_members')
                            ->label('')
                            ->content(function ($record): HtmlString {
                                $count = $record?->members()->count() ?? 0;
                                return new HtmlString('
                                    <div style="text-align:center;padding:0.5rem">
                                        <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.25rem">👥 Mitglieder</div>
                                        <div style="font-weight:600;font-size:1.25rem">' . $count . '</div>
                                    </div>
                                ');
                            }),
                        Placeholder::make('card_status')
                            ->label('')
                            ->content(function ($record): HtmlString {
                                $approved = $record?->is_approved ? '✓ Freigeschaltet' : '✗ Ausstehend';
                                $color    = $record?->is_approved ? '#22c55e' : '#f59e0b';
                                $since    = $record?->created_at?->diffForHumans() ?? '—';
                                return new HtmlString('
                                    <div style="text-align:center;padding:0.5rem">
                                        <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.25rem">📋 Status</div>
                                        <div style="font-weight:600;color:' . $color . '">' . $approved . '</div>
                                        <div style="font-size:0.75rem;color:#6b7280">Angemeldet ' . e($since) . '</div>
                                    </div>
                                ');
                            }),
                        Placeholder::make('card_location')
                            ->label('')
                            ->content(function ($record): HtmlString {
                                $location = trim(($record?->zip ?? '') . ' ' . ($record?->city ?? '')) ?: '—';
                                $phone    = $record?->phone ?? '—';
                                return new HtmlString('
                                    <div style="text-align:center;padding:0.5rem">
                                        <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.25rem">📍 Standort</div>
                                        <div style="font-weight:600">' . e($location) . '</div>
                                        <div style="font-size:0.75rem">' . e($phone) . '</div>
                                    </div>
                                ');
                            }),
                    ])
                    ->columns(3)
                    ->visibleOn('edit'),

                /* ── Grunddaten ──────────────────────────────────────────── */
                Forms\Components\Section::make('Grunddaten')
                    ->schema([
                        Forms\Components\Radio::make('type')
                            ->label('Typ')
                            ->options([
                                'verein'       => 'Verein (ZVR)',
                                'organisation' => 'Organisation / Initiative',
                            ])
                            ->inline()
                            ->live()
                            ->required()
                            ->columnSpanFull(),
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
                    ])
                    ->columns(2),

                /* ── Profil ──────────────────────────────────────────────── */
                Forms\Components\Section::make('Profil')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Beschreibung')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->directory('organisations/logos'),
                    ])
                    ->columns(2),

                /* ── Standort & Kontakt ──────────────────────────────────── */
                Forms\Components\Section::make('Standort & Kontakt')
                    ->schema([
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
                    ])
                    ->columns(2),

                /* ── Ansprechpersonen ────────────────────────────────────── */
                Forms\Components\Section::make('Ansprechpersonen')
                    ->schema([
                        Forms\Components\TextInput::make('representative')
                            ->label('Vertretungsberechtigte Person')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contact_person')
                            ->label('Ansprechpartner:in')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                /* ── Einstellungen ───────────────────────────────────────── */
                Forms\Components\Section::make('Einstellungen')
                    ->schema([
                        Forms\Components\Select::make('categories')
                            ->label('Kategorien')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Freigeschaltet')
                            ->helperText('Organisation wurde vom FWZ-Team geprüft und genehmigt')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktiv')
                            ->helperText('Organisation ist technisch aktiv und kann sich einloggen')
                            ->default(true),
                    ])
                    ->columns(2),
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
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Gelöscht')
                    ->badge()
                    ->state(fn (?Organisation $record): ?string => $record?->deleted_at ? 'Gelöscht' : null)
                    ->color('danger')
                    ->visible(fn (?Organisation $record): bool => $record?->deleted_at !== null),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->filters([
                Tables\Filters\TrashedFilter::make(),
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
                Tables\Actions\RestoreAction::make()
                    ->label('Wiederherstellen'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('Endgültig löschen'),
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
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Ausgewählte wiederherstellen'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Ausgewählte endgültig löschen'),
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
