<x-filament-panels::page>
    <x-filament::section heading="Vereinsliste mit Anmeldungen vergleichen">
        <p class="text-sm text-gray-600 dark:text-gray-300">
            Lade die aktuelle Mitgliederliste {{ $this->isAdminComparison() ? 'des ausgewählten Vereins' : 'deiner Organisation' }} hoch.
            Der Abgleich berücksichtigt alle Anmeldungen {{ $this->isAdminComparison() ? 'des ausgewählten Vereins' : 'deiner Organisation' }}
            – nicht nur die seit dem letzten Login.
            Es werden keine Mitglieder importiert, verändert, gelöscht oder gesperrt.
        </p>
    </x-filament::section>

    @if ($this->isAdminComparison())
        <x-filament::section heading="Verein auswählen">
            {{ $this->selectionForm }}
        </x-filament::section>
    @endif

    @if (! $this->isAdminComparison() || $this->hasSelectedOrganisation())
    @if ($headers === [])
        <x-filament::section heading="1. CSV hochladen">
            <form wire:submit="prepare" class="space-y-6">
                {{ $this->uploadForm }}
                <x-filament::button type="submit" icon="heroicon-o-document-arrow-up"
                    wire:loading.attr="disabled" wire:target="prepare,uploadData">
                    Spalten erkennen
                </x-filament::button>
            </form>
        </x-filament::section>
    @else
        <x-filament::section heading="2. Spalten zuordnen">
            <form wire:submit="compare" class="space-y-6">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Prüfe die vorgeschlagene Zuordnung. Jede CSV kann andere Spaltenüberschriften verwenden.
                    Leere E-Mail-Felder sind erlaubt, sofern Vor- und Nachname vorhanden sind.
                </p>
                {{ $this->mappingForm }}
                <div class="flex flex-wrap gap-3">
                    <x-filament::button type="submit" icon="heroicon-o-magnifying-glass"
                        wire:loading.attr="disabled" wire:target="compare">
                        Abgleich starten
                    </x-filament::button>
                    <x-filament::button color="gray" wire:click="startOver" wire:loading.attr="disabled">
                        Andere CSV hochladen
                    </x-filament::button>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Die Datei wird nur vorübergehend für diesen Abgleich verarbeitet. Die Auswertung läuft nach 30 Minuten ab.
                </p>
            </form>
        </x-filament::section>
    @endif

    @if ($result = $this->comparison)
        <x-filament::section heading="3. Ergebnis">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Mitgliederzeilen in der CSV</dt>
                    <dd class="text-xl font-semibold">{{ $result['csv_rows'] }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">
                        Anmeldungen {{ $this->isAdminComparison() ? 'des ausgewählten Vereins' : 'deiner Organisation' }}
                    </dt>
                    <dd class="text-xl font-semibold">{{ $result['total'] }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Über E-Mail und Namen gefunden</dt>
                    <dd class="text-xl font-semibold">{{ $result['matched'] }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Nicht in CSV gefunden / manuell prüfen</dt>
                    <dd class="text-xl font-semibold">{{ $result['missing'] }} / {{ $result['review'] }}</dd>
                </div>
            </dl>
            <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                Unten siehst du alle fehlenden und unklaren Zuordnungen. „Nicht in CSV gefunden“ ist kein
                Beweis, dass die Person kein Vereinsmitglied ist. Namen ohne passende E-Mail,
                abweichende Namen und Mehrdeutigkeiten müssen manuell geprüft werden.
                Groß-/Kleinschreibung und zusätzliche Leerzeichen werden beim Abgleich ignoriert.
            </p>
            @if ($result['invalid_rows'] > 0)
                <p class="mt-3 text-sm font-medium text-amber-600 dark:text-amber-400">
                    {{ $result['invalid_rows'] }} CSV-Zeile(n) enthalten weder eine gültige E-Mail-Adresse
                    noch einen vollständigen Namen und konnten nicht berücksichtigt werden.
                </p>
            @endif
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                Momentaufnahme zum Zeitpunkt des Abgleichs. Bei neuen Anmeldungen den Abgleich erneut starten.
            </p>
        </x-filament::section>
        {{ $this->table }}
    @endif
    @endif
</x-filament-panels::page>
