<?php

namespace App\Filament\Organisation\Pages;

use App\Models\Organisation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Arr;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Meine Organisationsdaten';

    protected static ?string $title = 'Meine Organisationsdaten';

    protected static ?string $slug = 'meine-daten';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.organisation.profile';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(auth('organisation')->user()->attributesToArray());
    }

    public function form(Form $form): Form
    {
        $organisation = auth('organisation')->user();

        return $form
            ->model($organisation)
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Organisation / Verein')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('email')->label('E-Mail / Anmeldung')
                            ->email()->required()->maxLength(255)
                            ->unique(Organisation::class, 'email', ignoreRecord: true)
                            ->helperText('Wenn du diese Adresse änderst, verwende sie künftig zum Anmelden.'),
                        Forms\Components\TextInput::make('zvr_number')->label('ZVR-Nummer')->maxLength(255),
                        Forms\Components\TextInput::make('website')->label('Website')->url()->maxLength(255),
                        Forms\Components\TextInput::make('phone')->label('Telefon')->maxLength(255),
                        Forms\Components\TextInput::make('representative')->label('Vertretung')->maxLength(255),
                        Forms\Components\TextInput::make('contact_person')->label('Ansprechperson')->maxLength(255),
                        Forms\Components\Textarea::make('description')->label('Beschreibung')->rows(5)->columnSpanFull(),
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo')->disk('public')->visibility('public')
                            ->image()->maxSize(2048)->directory('organisations/logos')
                            ->getUploadedFileUsing(static function (Forms\Components\BaseFileUpload $component, string $file): ?array {
                                $disk = $component->getDisk();
                                if (! $disk->exists($file)) {
                                    return null;
                                }

                                return [
                                    'name' => basename($file),
                                    'size' => $disk->size($file),
                                    'type' => $disk->mimeType($file),
                                    'url' => '/storage/'.ltrim($file, '/'),
                                ];
                            })->columnSpanFull(),
                        Forms\Components\CheckboxList::make('categories')->label('Kategorien')
                            ->relationship('categories', 'name')->columns(2)->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Adresse')
                    ->schema([
                        Forms\Components\TextInput::make('street')->label('Straße und Hausnummer')->maxLength(255)->columnSpanFull(),
                        Forms\Components\TextInput::make('zip')->label('PLZ')->maxLength(4),
                        Forms\Components\TextInput::make('city')->label('Ort')->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $organisation = auth('organisation')->user();
        $organisation->update(Arr::only($data, [
            'name', 'email', 'zvr_number', 'website', 'phone', 'representative',
            'contact_person', 'description', 'street', 'zip', 'city', 'logo_path',
        ]));
        $this->form->saveRelationships();

        Notification::make()->title('Organisationsdaten gespeichert')->success()->send();
    }
}
