<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Services\MemberAccessInvitationService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::End;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
            Actions\DeleteAction::make()
                ->record($this->getRecord()),
            $this->getSaveFormAction(),
        ];
    }

    public function saveAndSendAccessCredentials(): void
    {
        $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

        if ($this->record->status !== 'approved') {
            return;
        }

        try {
            app(MemberAccessInvitationService::class)->send($this->record);

            $this->refreshFormData([
                'membership_number',
            ]);

            Notification::make()
                ->title('Gespeichert und Zugangsdaten versendet')
                ->body('Das Mitglied erhält einen sechsstelligen, sieben Tage gültigen Freischaltcode per E-Mail.')
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Gespeichert, aber E-Mail nicht versendet')
                ->body('Bitte prüfe die Mailserver-Einstellungen und versuche es erneut.')
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
