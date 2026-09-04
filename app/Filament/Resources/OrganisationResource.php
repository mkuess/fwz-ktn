<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganisationResource\Pages;
use App\Models\Category;
use App\Models\Organisation;
use App\Services\GeocodingService;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
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
                                        <div style="font-weight:600;font-size:1.25rem">'.$count.'</div>
                                    </div>
                                ');
                            }),
                        Placeholder::make('card_status')
                            ->label('')
                            ->content(function ($record): HtmlString {
                                $status = $record?->approval_status ?? ($record?->is_approved ? 'approved' : 'pending');
                                $statusLabel = match ($status) {
                                    'approved' => '✓ Freigeschaltet',
                                    'rejected' => '✗ Abgelehnt',
                                    default => '◷ Ausstehend',
                                };
                                $color = match ($status) {
                                    'approved' => '#22c55e',
                                    'rejected' => '#ef4444',
                                    default => '#f59e0b',
                                };
                                $since = $record?->created_at?->diffForHumans() ?? '—';

                                return new HtmlString('
                                    <div style="text-align:center;padding:0.5rem">
                                        <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.25rem">📋 Status</div>
                                        <div style="font-weight:600;color:'.$color.'">'.$statusLabel.'</div>
                                        <div style="font-size:0.75rem;color:#6b7280">Angemeldet '.e($since).'</div>
                                    </div>
                                ');
                            }),
                        Placeholder::make('card_location')
                            ->label('')
                            ->content(function ($record): HtmlString {
                                $location = trim(($record?->zip ?? '').' '.($record?->city ?? '')) ?: '—';
                                $phone = $record?->phone ?? '—';

                                return new HtmlString('
                                    <div style="text-align:center;padding:0.5rem">
                                        <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.25rem">📍 Standort</div>
                                        <div style="font-weight:600">'.e($location).'</div>
                                        <div style="font-size:0.75rem">'.e($phone).'</div>
                                    </div>
                                ');
                            }),
                    ])
                    ->columns(3)
                    ->visibleOn('edit'),

                /* ── Status ───────────────────────────────────────────────── */
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('approval_status')
                            ->label('Freigabestatus')
                            ->options([
                                'pending' => 'Ausstehend',
                                'approved' => 'Freigeschaltet',
                                'rejected' => 'Abgelehnt',
                            ])
                            ->live()
                            ->required()
                            ->default('pending')
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state): void {
                                if ($state !== 'rejected') {
                                    $set('rejection_reason', null);
                                }
                            }),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Begründung der Ablehnung')
                            ->placeholder('Warum wurde die Organisation abgelehnt?')
                            ->visible(fn (Forms\Get $get): bool => $get('approval_status') === 'rejected')
                            ->required(fn (Forms\Get $get): bool => $get('approval_status') === 'rejected')
                            ->maxLength(5000)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktiv')
                            ->helperText('Organisation ist technisch aktiv und kann sich einloggen')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                /* ── Grunddaten ──────────────────────────────────────────── */
                Forms\Components\Section::make('Grunddaten')
                    ->schema([
                        Forms\Components\Radio::make('type')
                            ->label('Typ')
                            ->options([
                                'verein' => 'Verein (ZVR)',
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
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->directory('organisations/logos')
                            ->getUploadedFileUsing(static function (
                                Forms\Components\BaseFileUpload $component,
                                string $file,
                                string|array|null $storedFileNames,
                            ): ?array {
                                $storage = $component->getDisk();

                                if (! $storage->exists($file)) {
                                    return null;
                                }

                                $relativePath = implode('/', array_map(
                                    'rawurlencode',
                                    explode('/', ltrim($file, '/')),
                                ));

                                return [
                                    'name' => (is_array($storedFileNames)
                                        ? ($storedFileNames[$file] ?? null)
                                        : $storedFileNames) ?? basename($file),
                                    'size' => $storage->size($file),
                                    'type' => $storage->mimeType($file),
                                    'url' => '/storage/'.$relativePath,
                                ];
                            }),
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
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(30)
            ->paginationPageOptions([10, 30, 50, 100])
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->size(40)
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Kategorien')
                    ->badge()
                    ->placeholder('Keine Kategorie'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Stadt')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('latitude')
                    ->label('Standort')
                    ->boolean()
                    ->state(fn (Organisation $record): bool => $record->latitude !== null && $record->longitude !== null)
                    ->trueIcon('heroicon-o-map-pin')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn (Organisation $record): string => $record->latitude !== null && $record->longitude !== null
                        ? 'Koordinaten: '.$record->latitude.', '.$record->longitude
                        : 'Kein Standort ermittelt'),
                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Organisation $record): string => $record->approval_status
                        ?? ($record->is_approved ? 'approved' : 'pending'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => 'Freigeschaltet',
                        'rejected' => 'Abgelehnt',
                        default => 'Ausstehend',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
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
                Tables\Filters\SelectFilter::make('approval_status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Ausstehend',
                        'approved' => 'Freigeschaltet',
                        'rejected' => 'Abgelehnt',
                    ]),
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
                        ->action(fn (Collection $records) => $records->each(
                            fn (Organisation $organisation) => $organisation->update([
                                'is_approved' => true,
                                'approval_status' => 'approved',
                                'approved_at' => now(),
                            ])
                        ))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('assignCategory')
                        ->label('Kategorie zuweisen')
                        ->icon('heroicon-o-tag')
                        ->form([
                            Forms\Components\Select::make('category_id')
                                ->label('Kategorie')
                                ->options(fn (): array => Category::query()
                                    ->orderBy('sort_order')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all())
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(
                                fn (Organisation $organisation) => $organisation->categories()
                                    ->syncWithoutDetaching([(int) $data['category_id']])
                            );
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Kategorie erfolgreich zugewiesen'),
                    Tables\Actions\BulkAction::make('geocode')
                        ->label('Standort ermitteln')
                        ->icon('heroicon-o-map-pin')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Standorte ermitteln')
                        ->modalDescription('Für alle ausgewählten Organisationen wird der Standort über die Adresse ermittelt (OpenStreetMap). Dies kann einige Sekunden dauern.')
                        ->action(function (Collection $records): void {
                            $service = app(GeocodingService::class);
                            $success = 0;
                            $failed = 0;

                            foreach ($records as $record) {
                                if ($service->geocodeOrganisation($record)) {
                                    $success++;
                                } else {
                                    $failed++;
                                }
                            }

                            $notification = Notification::make()
                                ->title("Standorte ermittelt: {$success} erfolgreich, {$failed} fehlgeschlagen");

                            if ($failed === 0) {
                                $notification->success();
                            } else {
                                $notification->warning();
                            }

                            $notification->send();
                        })
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
