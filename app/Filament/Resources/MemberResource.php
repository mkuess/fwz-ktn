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
                    ->required(),
                Forms\Components\TextInput::make('first_name')
                    ->label('Vorname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Nachname')
                    ->required()
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Vorname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Nachname')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ])
            ->actions([
                Tables\Actions\Action::make('approveMember')
                    ->label('Mitglied freischalten')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Member $record): bool => $record->status !== 'approved')
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
