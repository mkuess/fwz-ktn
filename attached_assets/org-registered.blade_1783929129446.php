<x-mail::message>
# Neue Vereinsregistrierung

**{{ $organisation->name }}** ({{ $organisation->type === 'verein' ? 'Verein' : 'Organisation' }}) hat sich soeben über die Website registriert und wartet auf Freischaltung.

- **E-Mail:** {{ $organisation->email }}
- **ZVR-Nummer:** {{ $organisation->zvr_number ?? '–' }}
- **Ort:** {{ $organisation->zip }} {{ $organisation->city }}
- **Ansprechpartner:in:** {{ $organisation->contact_person }}
- **Vertretungsberechtigte Person:** {{ $organisation->representative }}
- **Telefon:** {{ $organisation->phone ?? '–' }}

<x-mail::button :url="url('/verwaltung')">
Im Backend prüfen
</x-mail::button>

Freiwilligenzentrum Kärnten
</x-mail::message>
