<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Einstellungen extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Verwaltung';

    protected static ?string $navigationLabel = 'Einstellungen';

    protected static ?string $title = 'Einstellungen';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.einstellungen';

    public bool $member_registration_enabled = true;

    public bool $organisation_registration_enabled = true;

    public bool $login_button_enabled = true;

    public function mount(): void
    {
        $this->member_registration_enabled = Setting::enabled('member_registration_enabled');
        $this->organisation_registration_enabled = Setting::enabled('organisation_registration_enabled');
        $this->login_button_enabled = Setting::enabled('login_button_enabled');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Registrierungen')
                    ->description('Steuere, welche öffentlichen Anmeldungen aktiv sind.')
                    ->schema([
                        Toggle::make('member_registration_enabled')
                            ->label('Mitglieder-Anmeldung')
                            ->helperText('Wenn deaktiviert, werden der Anmeldebutton und die Anmeldeseite für Mitglieder ausgeblendet.')
                            ->onColor('success')
                            ->offColor('danger'),
                        Toggle::make('organisation_registration_enabled')
                            ->label('Organisations-Anmeldung')
                            ->helperText('Wenn deaktiviert, werden der Registrierungsbutton und die Anmeldeseite für Organisationen ausgeblendet.')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                Section::make('Navigation')
                    ->description('Steuere die Sichtbarkeit des Login-Buttons im öffentlichen Menü.')
                    ->schema([
                        Toggle::make('login_button_enabled')
                            ->label('Login-Button „Anmelden“')
                            ->helperText('Wenn deaktiviert, wird der Login-Button im Desktop- und Mobilmenü ausgeblendet.')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ])
            ->statePath('');
    }

    public function save(): void
    {
        Setting::set('member_registration_enabled', $this->member_registration_enabled ? '1' : '0');
        Setting::set('organisation_registration_enabled', $this->organisation_registration_enabled ? '1' : '0');
        Setting::set('login_button_enabled', $this->login_button_enabled ? '1' : '0');

        Notification::make()
            ->title('Einstellungen gespeichert')
            ->success()
            ->send();
    }
}
