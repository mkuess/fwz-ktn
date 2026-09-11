<div
    class="space-y-5"
    x-data="{ copied: false }"
>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="email-export-type" class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">
                Organisationsart
            </label>
            <select
                id="email-export-type"
                wire:model.live="emailExportType"
                class="block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white"
            >
                <option value="organisation">Organisation / Initiative</option>
                <option value="verein">Verein</option>
            </select>
        </div>

        <div>
            <label for="email-export-category" class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">
                Kategorie
            </label>
            <select
                id="email-export-category"
                wire:model.live="emailExportCategoryId"
                class="block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white"
            >
                <option value="">Alle Kategorien</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <div class="mb-2 flex items-center justify-between gap-3">
            <label for="email-export-addresses" class="block text-sm font-medium text-gray-950 dark:text-white">
                E-Mail-Adressen
            </label>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ $this->emailExportAddresses === '' ? 0 : substr_count($this->emailExportAddresses, ',') + 1 }} Adresse(n)
            </span>
        </div>
        <textarea
            id="email-export-addresses"
            x-ref="addresses"
            readonly
            rows="9"
            class="block w-full resize-y rounded-lg border-gray-300 bg-gray-50 text-sm text-gray-950 shadow-sm dark:border-white/10 dark:bg-white/5 dark:text-white"
        >{{ $this->emailExportAddresses }}</textarea>
    </div>

    <div class="flex items-center gap-3">
        <button
            type="button"
            class="fi-btn fi-btn-size-md inline-grid grid-flow-col items-center justify-center gap-1.5 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm outline-none transition duration-75 hover:bg-primary-500 focus-visible:ring-2 focus-visible:ring-primary-600"
            x-on:click="
                navigator.clipboard.writeText($refs.addresses.value).then(() => {
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                })
            "
            @disabled($this->emailExportAddresses === '')
        >
            E-Mail-Adressen kopieren
        </button>
        <span
            x-cloak
            x-show="copied"
            class="text-sm font-medium text-success-600 dark:text-success-400"
        >
            Kopiert
        </span>
    </div>
</div>