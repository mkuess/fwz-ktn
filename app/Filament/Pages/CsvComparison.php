<?php

namespace App\Filament\Pages;

use App\Filament\Organisation\Pages\CsvComparison as OrganisationCsvComparison;
use App\Models\Organisation;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Illuminate\Validation\Rule;

class CsvComparison extends OrganisationCsvComparison
{
    protected static ?string $navigationGroup = 'Verwaltung';

    protected static ?string $navigationLabel = 'CSV Abgleich';

    protected static ?string $slug = 'csv-abgleich';

    protected static ?int $navigationSort = 3;

    public ?array $selectionData = [];

    public static function canAccess(): bool
    {
        return (bool) auth('web')->user()?->is_admin;
    }

    public function mount(): void
    {
        parent::mount();
        $this->selectionForm->fill();
    }

    protected function getForms(): array
    {
        return [...parent::getForms(), 'selectionForm'];
    }

    public function selectionForm(Form $form): Form
    {
        return $form->statePath('selectionData')->schema([
            Select::make('organisation_id')
                ->label('Verein / Organisation auswählen')
                ->placeholder('Name oder Ort eingeben …')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => Organisation::query()
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%')
                            ->orWhere('city', 'like', '%'.$search.'%');
                    })
                    ->orderBy('name')->limit(50)->get()
                    ->mapWithKeys(fn (Organisation $organisation): array => [
                        $organisation->id => $this->organisationLabel($organisation),
                    ])->all())
                ->getOptionLabelUsing(fn ($value): ?string => ($organisation = Organisation::find($value))
                    ? $this->organisationLabel($organisation) : null)
                ->required()->rules(['integer', Rule::exists('organisations', 'id')->whereNull('deleted_at')])
                ->live()->afterStateUpdated(fn () => $this->startOver())
                ->helperText('Beim Wechsel des Vereins wird der bisherige Abgleich verworfen. Lade danach die passende CSV hoch.'),
        ]);
    }

    public function isAdminComparison(): bool
    {
        return true;
    }

    protected function comparisonOrganisationId(): ?int
    {
        $id = $this->selectionData['organisation_id'] ?? null;
        if (! is_scalar($id) || ! ctype_digit((string) $id)) {
            return null;
        }

        return Organisation::query()->whereKey($id)->exists() ? (int) $id : null;
    }

    protected function comparisonCacheIdentity(): string
    {
        return 'admin-organisation-csv:'.auth('web')->id();
    }

    protected function validateComparisonContext(): void
    {
        abort_unless(static::canAccess(), 403);
        $this->selectionForm->getState();
    }

    private function organisationLabel(Organisation $organisation): string
    {
        return $organisation->name.($organisation->city ? ' – '.$organisation->city : '').' (#'.$organisation->id.')';
    }
}
