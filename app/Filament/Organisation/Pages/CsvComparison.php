<?php

namespace App\Filament\Organisation\Pages;

use App\Filament\Organisation\Support\MemberTable;
use App\Models\Member;
use App\Services\OrganisationMemberCsvComparison;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CsvComparison extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?string $navigationLabel = 'CSV hochladen';

    protected static ?string $title = 'CSV-Mitgliederabgleich';

    protected static ?string $slug = 'csv-hochladen';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.organisation.csv-comparison';

    public ?array $uploadData = [];

    public ?array $mappingData = [];

    #[Locked]
    public array $headers = [];

    #[Locked]
    public ?string $comparisonToken = null;

    public function mount(): void
    {
        $this->uploadForm->fill();
        $this->mappingForm->fill();
    }

    protected function getForms(): array
    {
        return ['uploadForm', 'mappingForm'];
    }

    public function uploadForm(Form $form): Form
    {
        return $form->statePath('uploadData')->schema([
            Forms\Components\FileUpload::make('csv')
                ->label('Aktuelle Mitgliederliste (CSV)')
                ->disk('local')->visibility('private')
                ->storeFiles(false)
                ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                ->maxSize(5120)->required()
                ->helperText('Maximal 5 MB / 20.000 Zeilen. Erste Zeile: Spaltenüberschriften. Komma, Semikolon oder Tabulator werden erkannt.'),
        ]);
    }

    public function mappingForm(Form $form): Form
    {
        return $form->statePath('mappingData')->schema([
            Forms\Components\Select::make('email')->label('Spalte für E-Mail')->options(fn (): array => $this->headerOptions())->required()->searchable()
                ->live()->afterStateUpdated(fn () => $this->invalidateResult()),
            Forms\Components\Select::make('first_name')->label('Spalte für Vorname')->options(fn (): array => $this->headerOptions())->required()->searchable()
                ->live()->afterStateUpdated(fn () => $this->invalidateResult()),
            Forms\Components\Select::make('last_name')->label('Spalte für Nachname')->options(fn (): array => $this->headerOptions())->required()->searchable()
                ->live()->afterStateUpdated(fn () => $this->invalidateResult()),
        ])->columns(3);
    }

    private function headerOptions(): array
    {
        $options = [];
        foreach ($this->headers as $index => $header) {
            $options[$index] = ($index + 1).': '.($header !== '' ? $header : '(ohne Überschrift)');
        }

        return $options;
    }

    public function prepare(): void
    {
        $file = $this->uploadForm->getState()['csv'];
        if (! $file instanceof TemporaryUploadedFile) {
            throw ValidationException::withMessages(['uploadData.csv' => 'Bitte lade eine CSV-Datei hoch.']);
        }
        try {
            $csv = app(OrganisationMemberCsvComparison::class)->read($file->getRealPath());
        } finally {
            $file->delete();
            $this->uploadForm->fill();
        }
        $this->forgetComparison();
        $this->comparisonToken = Str::random(40);
        $this->headers = $csv['headers'];
        Cache::put($this->cacheKey(), ['csv' => $csv, 'result' => null], now()->addMinutes(30));
        $this->mappingForm->fill(app(OrganisationMemberCsvComparison::class)->suggest($this->headers));
    }

    public function compare(): void
    {
        $mapping = $this->mappingForm->getState();
        $state = $this->comparisonToken ? Cache::get($this->cacheKey()) : null;
        if (! $state) {
            throw ValidationException::withMessages(['mappingData.email' => 'Der Upload ist abgelaufen. Bitte lade die CSV erneut hoch.']);
        }
        $state['result'] = app(OrganisationMemberCsvComparison::class)->compare($state['csv'], $mapping, (int) auth('organisation')->id());
        Cache::put($this->cacheKey(), $state, now()->addMinutes(30));
        unset($this->comparison);
        $this->resetTable();
    }

    public function startOver(): void
    {
        $this->forgetComparison();
        $this->comparisonToken = null;
        $this->headers = [];
        $this->uploadForm->fill();
        $this->mappingForm->fill();
        unset($this->comparison);
        $this->resetTable();
    }

    #[Computed]
    public function comparison(): ?array
    {
        return $this->comparisonToken ? (Cache::get($this->cacheKey())['result'] ?? null) : null;
    }

    public function table(Table $table): Table
    {
        $issues = $this->comparison['issues'] ?? [];

        return MemberTable::configure($table, Member::query()->whereIn('id', array_keys($issues)))
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Name')
                    ->state(fn (Member $record): string => trim($record->first_name.' '.$record->last_name))
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')->label('E-Mail')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('comparison_kind')->label('Abgleich')
                    ->state(fn (Member $record): string => ($issues[$record->id]['kind'] ?? '') === 'missing' ? 'Nicht in CSV gefunden' : 'Manuell prüfen')
                    ->badge()->color(fn (Member $record): string => ($issues[$record->id]['kind'] ?? '') === 'missing' ? 'danger' : 'warning'),
                Tables\Columns\TextColumn::make('comparison_reason')->label('Grund')
                    ->state(fn (Member $record): string => $issues[$record->id]['reason'] ?? '')->wrap(),
                Tables\Columns\TextColumn::make('csv_names')->label('Name in CSV')
                    ->state(fn (Member $record): string => $issues[$record->id]['csv_names'] ?? '')
                    ->placeholder('—')->wrap(),
                Tables\Columns\TextColumn::make('csv_emails')->label('E-Mail in CSV')
                    ->state(fn (Member $record): string => $issues[$record->id]['csv_emails'] ?? '')
                    ->placeholder('—')->wrap()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->label('Angemeldet am')->dateTime('d.m.Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('comparison_kind')->label('Abgleich')
                    ->options(['missing' => 'Nicht in CSV gefunden', 'review' => 'Manuell prüfen'])
                    ->query(function (Builder $query, array $data) use ($issues): Builder {
                        return empty($data['value']) ? $query : $query->whereIn('id', array_keys(array_filter(
                            $issues, fn (array $issue): bool => $issue['kind'] === $data['value']
                        )));
                    }),
            ])
            ->emptyStateHeading($issues === [] ? 'Keine auffälligen Anmeldungen' : 'Keine passenden Ergebnisse')
            ->emptyStateDescription($issues === []
                ? 'Alle angemeldeten Mitglieder wurden über E-Mail und Namen in der CSV gefunden.'
                : 'Für die aktuelle Suche oder Filterauswahl wurden keine Ergebnisse gefunden.')
            ->actions([]);
    }

    private function cacheKey(): string
    {
        return 'organisation-csv:'.auth('organisation')->id().':'.hash('sha256', session()->getId()).':'.$this->comparisonToken;
    }

    private function forgetComparison(): void
    {
        if ($this->comparisonToken) {
            Cache::forget($this->cacheKey());
        }
    }

    private function invalidateResult(): void
    {
        $state = $this->comparisonToken ? Cache::get($this->cacheKey()) : null;
        if ($state && $state['result'] !== null) {
            $state['result'] = null;
            Cache::put($this->cacheKey(), $state, now()->addMinutes(30));
        }
        unset($this->comparison);
    }
}
