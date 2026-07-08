<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;
use League\Csv\Reader;
use Throwable;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importCsv')
                ->label('CSV importieren')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Forms\Components\FileUpload::make('csv_file')
                        ->label('CSV-Datei')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                        ->required()
                        ->storeFiles(false)
                        ->helperText(new \Illuminate\Support\HtmlString(
                            'Pflichtfelder: name | Optionale Felder: slug, color, icon, sort_order<br>'
                            .'Beispiel: Feuerwehren,feuerwehren,#dc2626,fire,1'
                        )),
                ])
                ->action(function (array $data) {
                    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file */
                    $file = $data['csv_file'];

                    try {
                        $csv = Reader::createFromPath($file->getRealPath(), 'r');
                        $csv->setHeaderOffset(null);

                        $imported = 0;

                        foreach ($csv->getRecords() as $record) {
                            $name = trim((string) ($record[0] ?? ''));

                            if ($name === '') {
                                continue;
                            }

                            $slug = trim((string) ($record[1] ?? ''));
                            $slug = $slug !== '' ? $slug : Str::slug($name);

                            $color = trim((string) ($record[2] ?? ''));
                            $color = $color !== '' ? $color : null;

                            $icon = trim((string) ($record[3] ?? ''));
                            $icon = $icon !== '' ? $icon : null;

                            $sortOrder = trim((string) ($record[4] ?? ''));
                            $sortOrder = $sortOrder !== '' ? (int) $sortOrder : 0;

                            Category::updateOrCreate(
                                ['slug' => $slug],
                                [
                                    'name' => $name,
                                    'color' => $color,
                                    'icon' => $icon,
                                    'sort_order' => $sortOrder,
                                ]
                            );

                            $imported++;
                        }

                        Notification::make()
                            ->title("{$imported} Kategorien importiert")
                            ->success()
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('CSV-Import fehlgeschlagen')
                            ->body('Die Datei konnte nicht verarbeitet werden. Bitte überprüfen Sie das Format.')
                            ->danger()
                            ->send();
                    }
                })
                ->modalSubmitActionLabel('Importieren'),
            Actions\CreateAction::make(),
        ];
    }
}
