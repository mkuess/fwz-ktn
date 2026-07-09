<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Mitglied';

    protected static ?string $pluralModelLabel = 'Mitglieder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                        'pending' => 'Ausstehend',
                        'approved' => 'Genehmigt',
                        'rejected' => 'Abgelehnt',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\Select::make('role')
                    ->label('Rolle')
                    ->options([
                        'member' => '👤 Mitglied',
                        'org_admin' => '🏢 Organisations-Admin',
                        'admin' => '🔑 FWZ Admin',
                    ])
                    ->default('member')
                    ->required(),
                Forms\Components\TextInput::make('source')
                    ->label('Quelle')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('membership_number')
                    ->label('Mitgliedsnummer')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Toggle::make('newsletter_optin')
                    ->label('Newsletter-Einwilligung')
                    ->default(false),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->state(fn (?Member $record): string => trim(($record?->first_name ?? '').' '.($record?->last_name ?? '')) ?: ($record?->email ?? '-'))
                    ->searchable(query: function (\Illuminate\Database\Eloquent\Builder $query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->where(function (\Illuminate\Database\Eloquent\Builder $query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-Mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organisation.name')
                    ->label('Organisation')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Ausstehend',
                        'approved' => 'Genehmigt',
                        'rejected' => 'Abgelehnt',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->label('Quelle')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'self' => 'Selbst registriert',
                        'csv' => 'CSV-Import',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rolle')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'member' => 'Mitglied',
                        'org_admin' => 'Org-Admin',
                        'admin' => 'Admin',
                        default => $state ?? 'Mitglied',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'org_admin' => 'info',
                        'admin' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Gelöscht')
                    ->badge()
                    ->state(fn (?Member $record): ?string => $record?->deleted_at ? 'Gelöscht' : null)
                    ->color('danger')
                    ->visible(fn (?Member $record): bool => $record?->deleted_at !== null),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Ausstehend',
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
                        'member' => 'Mitglied',
                        'org_admin' => 'Organisations-Admin',
                        'admin' => 'FWZ Admin',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approveMember')
                    ->label('Mitglied freischalten')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (?Member $record): bool => $record?->status !== 'approved')
                    ->action(function (Member $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'membership_number' => sprintf(
                                'FWZ-%s-%06d',
                                now()->year,
                                $record->id
                            ),
                        ]);

                        Notification::make()
                            ->title('Mitglied freigeschalten')
                            ->body("Mitgliedsnummer: {$record->membership_number}")
                            ->success()
                            ->send();
                    }),
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
                                    'member' => '👤 Mitglied',
                                    'org_admin' => '🏢 Organisations-Admin',
                                    'admin' => '🔑 FWZ Admin',
                                ])
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $records->each->update(['role' => $data['role']]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Rolle erfolgreich zugewiesen'),
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
