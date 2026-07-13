<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\HtmlString;
use Filament\Tables;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Verwaltung';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Mitglied';

    protected static ?string $pluralModelLabel = 'Mitglieder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                /* ── Info-Karten ───────────────────────────────────────── */
                Forms\Components\Section::make()
                    ->schema([
                        Placeholder::make('org_card')
                            ->label('')
                            ->content(fn (?Member $record): HtmlString => new HtmlString(
                                '<div style="text-align:center">
                                    <div style="font-size:0.75rem;color:#6b7280;margin-top:0.5rem">Organisation</div>
                                    <div style="font-weight:600">' . e($record?->organisation?->name ?? '—') . '</div>
                                </div>'
                            )),
                        Placeholder::make('address_card')
                            ->label('')
                            ->content(fn (?Member $record): HtmlString => new HtmlString(
                                '<div style="text-align:center">
                                    <div style="font-size:0.75rem;color:#6b7280;margin-top:0.5rem">Adresse</div>
                                    <div style="font-weight:600">' . e($record?->street ?? '—') . '</div>
                                    <div>' . e(trim(($record?->zip ?? '') . ' ' . ($record?->city ?? ''))) . '</div>
                                </div>'
                            )),
                        Placeholder::make('info_card')
                            ->label('')
                            ->content(fn (?Member $record): HtmlString => new HtmlString(
                                '<div style="text-align:center">
                                    <div style="font-size:0.75rem;color:#6b7280;margin-top:0.5rem">Angemeldet</div>
                                    <div style="font-weight:600">' . e($record?->created_at?->diffForHumans() ?? '—') . '</div>
                                </div>'
                            )),
                    ])
                    ->columns(3),

                /* ── Status & Verwaltung ────────────────────────────────── */
                Forms\Components\Section::make('Status & Verwaltung')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending'  => 'Ausstehend',
                                'approved' => 'Geprüft',
                                'rejected' => 'Abgelehnt',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Ablehnungsgrund')
                            ->placeholder('Bitte Grund angeben...')
                            ->visible(fn (Get $get): bool => $get('status') === 'rejected')
                            ->nullable(),
                        Forms\Components\Select::make('card_status')
                            ->label('Karte zugesendet')
                            ->options([
                                'ausstehend' => '📭 Noch ausstehend',
                                'zugesendet' => '📬 Zugesendet',
                            ])
                            ->default('ausstehend')
                            ->live()
                            ->required(),
                        Forms\Components\DatePicker::make('card_sent_at')
                            ->label('Karte zugesendet am')
                            ->visible(fn (Get $get): bool => $get('card_status') === 'zugesendet')
                            ->nullable(),
                    ])
                    ->columns(2),

                /* ── Stammdaten ─────────────────────────────────────────── */
                Forms\Components\Select::make('organisation_id')
                    ->label('Organisation')
                    ->relationship('organisation', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->placeholder('Keine Organisation'),
                Forms\Components\TextInput::make('first_name')
                    ->label('Vorname')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Nachname')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('E-Mail')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Ausstehend',
                        'approved' => 'Genehmigt',
                        'rejected' => 'Abgelehnt',
                    ])
                    ->default('pending')
                    ->required()
                    ->hidden(),
                Forms\Components\Select::make('role')
                    ->label('Rolle')
                    ->options([
                        'member'    => '👤 Mitglied',
                        'org_admin' => '🏢 Organisations-Admin',
                        'admin'     => '🔑 FWZ Admin',
                    ])
                    ->default('member')
                    ->required(),
                Forms\Components\Hidden::make('source')
                    ->default('self'),
                Forms\Components\TextInput::make('password')
                    ->label('Passwort')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => ! empty($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText(fn (string $operation): string => $operation === 'edit'
                        ? 'Nur ausfüllen wenn das Passwort geändert werden soll'
                        : 'Pflichtfeld beim Erstellen')
                    ->maxLength(255),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Passwort bestätigen')
                    ->password()
                    ->revealable()
                    ->dehydrated(false)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->same('password')
                    ->maxLength(255),
                Forms\Components\TextInput::make('membership_number')
                    ->label('Mitgliedsnummer')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Toggle::make('newsletter_optin')
                    ->label('Newsletter-Einwilligung')
                    ->default(false),

                /* ── Adresse ────────────────────────────────────────────── */
                Forms\Components\Section::make('Adresse')
                    ->schema([
                        Forms\Components\TextInput::make('street')
                            ->label('Straße & Hausnummer')
                            ->nullable(),
                        Forms\Components\TextInput::make('zip')
                            ->label('PLZ')
                            ->maxLength(10)
                            ->nullable(),
                        Forms\Components\TextInput::make('city')
                            ->label('Ort')
                            ->nullable(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                /* ── Organisationszugang ────────────────────────────────── */
                Forms\Components\Section::make('Organisationszugang')
                    ->description('Welche Organisationen kann dieses Mitglied verwalten (z. B. als Organisations-Admin)?')
                    ->schema([
                        Forms\Components\Select::make('managedOrganisations')
                            ->label('Organisationszugang')
                            ->helperText('Welche Organisationen kann dieses Mitglied verwalten?')
                            ->multiple()
                            ->relationship(
                                'managedOrganisations',
                                'name',
                                fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('is_active', true)->orderBy('name')
                            )
                            ->searchable()
                            ->preload(false)
                            ->placeholder('Organisation suchen...')
                            ->nullable(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(30)
            ->paginationPageOptions([10, 30, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Mitglied')
                    ->state(fn (?Member $record): string => trim(($record?->first_name ?? '').' '.($record?->last_name ?? '')) ?: '-')
                    ->description(fn (?Member $record): string => $record?->email ?? '')
                    ->searchable(['first_name', 'last_name', 'email'])
                    ->sortable(['last_name']),
                Tables\Columns\TextColumn::make('organisation.name')
                    ->label('Organisation')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'  => 'Ausstehend',
                        'approved' => 'Genehmigt',
                        'rejected' => 'Abgelehnt',
                        default    => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->label('Quelle')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'self' => 'Selbst registriert',
                        'csv'  => 'CSV-Import',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rolle')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'member'    => 'Mitglied',
                        'org_admin' => 'Org-Admin',
                        'admin'     => 'Admin',
                        default     => $state ?? 'Mitglied',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'org_admin' => 'info',
                        'admin'     => 'danger',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('managedOrganisations.name')
                    ->label('Organisationszugang')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->placeholder('-')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Gelöscht')
                    ->badge()
                    ->state(fn (?Member $record): ?string => $record?->deleted_at ? 'Gelöscht' : null)
                    ->color('danger')
                    ->visible(fn (?Member $record): bool => $record?->deleted_at !== null),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Angemeldet')
                    ->dateTime('D, d. M Y \u\m H:i')
                    ->since()
                    ->tooltip(fn (?Member $record): ?string => $record?->created_at?->format('d.m.Y H:i'))
                    ->sortable(),
            ])
            ->recordUrl(fn (Member $record): string => static::getUrl('edit', ['record' => $record]))
            ->striped()
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Ausstehend',
                        'approved' => 'Genehmigt',
                        'rejected' => 'Abgelehnt',
                    ]),
                Tables\Filters\SelectFilter::make('organisation_id')
                    ->label('Organisation')
                    ->relationship('organisation', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rolle')
                    ->options([
                        'member'    => 'Mitglied',
                        'org_admin' => 'Organisations-Admin',
                        'admin'     => 'FWZ Admin',
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
                    Tables\Actions\BulkAction::make('assignRole')
                        ->label('Rolle zuweisen')
                        ->icon('heroicon-o-shield-check')
                        ->form([
                            Forms\Components\Select::make('role')
                                ->label('Rolle')
                                ->options([
                                    'member'    => '👤 Mitglied',
                                    'org_admin' => '🏢 Organisations-Admin',
                                    'admin'     => '🔑 FWZ Admin',
                                ])
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $records->each->update(['role' => $data['role']]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Rolle erfolgreich zugewiesen'),
                    Tables\Actions\BulkAction::make('approveSelected')
                        ->label('Ausgewählte freischalten')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $records->each(function (Member $record) {
                                $record->update([
                                    'status'      => 'approved',
                                    'approved_at' => now(),
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->modalHeading('Mitglieder freischalten')
                        ->modalDescription('Möchten Sie alle ausgewählten Mitglieder freischalten?'),
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
            'index'  => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit'   => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
