<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Neue Anmeldungen für {{ auth('organisation')->user()->name }}</x-slot>
        <p class="text-sm text-gray-600 dark:text-gray-300">
            @if(session('organisation_login_since'))
                Hier siehst du Mitglieder, die seit deinem vorherigen Login
                ({{ \Illuminate\Support\Carbon::parse(session('organisation_login_since'))->timezone(config('app.timezone'))->format('d.m.Y H:i') }})
                deine Organisation bei der Anmeldung ausgewählt haben.
            @else
                Willkommen! Bei deinem ersten Login siehst du alle bisherigen Anmeldungen für deine Organisation.
            @endif
        </p>
    </x-filament::section>
    {{ $this->table }}
</x-filament-panels::page>
