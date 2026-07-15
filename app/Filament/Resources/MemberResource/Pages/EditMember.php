<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendActivation')
                ->label('Zugangsdaten zusenden')
                ->icon('heroicon-o-envelope')
                ->color('success')
                ->visible(fn () => $this->record->status === 'approved')
                ->requiresConfirmation()
                ->modalHeading('Zugangsdaten zusenden')
                ->modalDescription(fn () => 'Es wird ein Aktivierungslink an ' . ($this->record->email ?? '') . ' gesendet. Das Mitglied kann damit ein Passwort erstellen und sich einloggen.')
                ->action(function () {
                    $member = $this->record;

                    if (empty($member->membership_number)) {
                        $member->membership_number = $member->generateMembershipNumber();
                    }

                    $token = \Illuminate\Support\Str::random(64);

                    $member->update([
                        'activation_token'   => $token,
                        'activation_sent_at' => now(),
                        'membership_number'  => $member->membership_number,
                    ]);

                    $activationUrl = url('/aktivierung/' . $token);

                    \Filament\Notifications\Notification::make()
                        ->title('Aktivierungslink generiert')
                        ->body('Link: ' . $activationUrl . ' | Mitgliedsnummer: ' . $member->membership_number)
                        ->success()
                        ->persistent()
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
