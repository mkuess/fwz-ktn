<?php

namespace App\Filament\Organisation\Support;

use App\Models\Member;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MemberTable
{
    public static function configure(Table $table, Builder $query, bool $allowBlocking = false): Table
    {
        return $table
            ->query($query->where('organisation_id', auth('organisation')->id()))
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->state(fn (Member $record): string => trim($record->first_name.' '.$record->last_name))
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-Mail')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Angemeldet am')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Freigabe')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => 'Geprüft',
                        'rejected' => 'Abgelehnt',
                        default => 'Ausstehend',
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('login_access')
                    ->label('Anmeldung möglich')
                    ->state(fn (Member $record): string => match (true) {
                        $record->is_login_blocked => 'Gesperrt',
                        $record->status !== 'approved' => 'Nicht freigeschaltet',
                        blank($record->password) => 'Passwort noch nicht eingerichtet',
                        default => 'Ja',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ja' => 'success',
                        'Gesperrt' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Freigabe')
                    ->options(['pending' => 'Ausstehend', 'approved' => 'Geprüft', 'rejected' => 'Abgelehnt']),
                Tables\Filters\TernaryFilter::make('is_login_blocked')
                    ->label('Zugangssperre')
                    ->trueLabel('Gesperrt')
                    ->falseLabel('Nicht gesperrt'),
            ])
            ->actions($allowBlocking ? [
                Tables\Actions\Action::make('block')
                    ->label('Sperren')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->visible(fn (Member $record): bool => ! $record->is_login_blocked)
                    ->requiresConfirmation()
                    ->modalHeading('Mitgliedszugang sperren')
                    ->modalDescription('Dieses Mitglied kann sich nicht mehr anmelden. Auch bestehende Mitgliedersitzungen werden beim nächsten Zugriff gesperrt.')
                    ->action(fn (Member $record) => self::setBlocked($record, true)),
                Tables\Actions\Action::make('unblock')
                    ->label('Entsperren')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->visible(fn (Member $record): bool => $record->is_login_blocked)
                    ->requiresConfirmation()
                    ->modalDescription('Die Sperre wird aufgehoben. Eine noch ausstehende Freigabe bleibt weiterhin erforderlich.')
                    ->action(fn (Member $record) => self::setBlocked($record, false)),
            ] : [])
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([10, 30, 50]);
    }

    private static function setBlocked(Member $member, bool $blocked): void
    {
        abort_unless(
            auth('organisation')->check()
                && (int) $member->organisation_id === (int) auth('organisation')->id(),
            403
        );

        Member::query()
            ->where('organisation_id', auth('organisation')->id())
            ->whereKey($member->id)
            ->update(['is_login_blocked' => $blocked]);

        Notification::make()
            ->title($blocked ? 'Mitgliedszugang gesperrt' : 'Zugangssperre aufgehoben')
            ->success()
            ->send();
    }
}
