# FWZ Kärnten – Laravel-Integration (Frontend + bestehendes Filament-Backend)

Dieses Paket ersetzt die vorherige eigenständige Node/Express-Version. Es ist so gebaut, dass es **in eure
bestehende Laravel-App mit Filament unter `/verwaltung` eingehängt** wird – kein zweiter Server, ein
gemeinsames Deployment.

## Was reinkopiert werden muss

```
public/css/style.css              →  euer public/css/style.css
public/js/main.js                 →  euer public/js/main.js
public/js/cookie-consent.js       →  euer public/js/cookie-consent.js
public/img/*.svg                  →  euer public/img/
resources/views/layouts/app.blade.php  →  euer resources/views/layouts/
resources/views/home.blade.php         →  euer resources/views/
resources/views/impressum.blade.php    →  euer resources/views/
resources/views/datenschutz.blade.php  →  euer resources/views/
resources/views/barrierefreiheit.blade.php → euer resources/views/
resources/views/in-arbeit.blade.php    →  euer resources/views/
app/Http/Controllers/HomeController.php →  euer app/Http/Controllers/
```

Die Route-Definitionen aus `routes-web-snippet.php` in eure bestehende `routes/web.php` einfügen
(Reihenfolge beachten: **vor** einem eventuell vorhandenen Fallback/Catch-all).

**Nicht mehr benötigt / verwerfen:** `server.js`, `package.json`, `.replit` aus dem vorherigen Paket –
Laravel übernimmt das Ausliefern.

## Wo die Backend-Daten reinkommen

`app/Http/Controllers/HomeController.php` liefert aktuell **Demodaten als PHP-Arrays** (Vereine, Aktionen,
Benefits, Kennzahlen), damit die Seite sofort läuft. Jeder Block ist mit `// TODO:` markiert und zeigt exakt,
wie die spätere Eloquent-Abfrage aussehen könnte (z. B. `Verein::where('freigeschaltet', true)->get()`).

Sobald ihr die passenden Models/Filament-Resources habt (vermutlich z. B. `Verein`, `Aktion`, `Benefit`,
`Kategorie`), einfach die markierten Array-Blöcke durch die echten Queries ersetzen – die Blade-View
(`home.blade.php`) erwartet dabei folgende Datenstruktur pro Eintrag:

- **Verein**: `name`, `ort`, `kuerzel` (optional)
- **Aktion**: `typ`, `titel`, `veranstalter`, `ort`, `zeit`, `bild` (URL oder `null` für Platzhalter), `bild_alt`
- **Benefit**: `partner`, `beschreibung`, `code`
- **Kennzahlen** (`$stats`): `vereine`, `freiwillige`, `stunden`, `engagementFelder`

Die Vereinssuche (Formular in `home.blade.php`, Abschnitt „Vereinsverzeichnis“) sendet aktuell `q` und `ort`
als GET-Parameter an dieselbe Seite. Sobald ihr eine echte Filterlogik im Controller habt, einfach
`$request->q` / `$request->ort` (siehe TODO-Kommentar) auswerten – die Formularfelder sind bereits korrekt
mit `name="q"` / `name="ort"` und `value="{{ request('q') }}"` vorbereitet, damit die Sucheingabe nach dem
Reload erhalten bleibt.

## Cookie-Consent & Rechtstexte

Unverändert gegenüber der vorherigen Version:
- `js/cookie-consent.js` lädt vor Einwilligung nichts Nicht-Notwendiges, granulare Kategorien, jederzeit
  widerrufbar über den Footer-Link „Cookie-Einstellungen“.
- `impressum.blade.php`, `datenschutz.blade.php`, `barrierefreiheit.blade.php` enthalten weiterhin mit
  `[…]` markierte Platzhalter für die echten Rechts-/Kontaktdaten – vor Go-Live ausfüllen (siehe Kapitel
  „Rechtlicher Gesamt-Check“ aus dem vorherigen README, gilt unverändert).

## Kurzer Funktionscheck nach dem Einbau

1. `composer dump-autoload` (falls der Controller neu ist)
2. `/` aufrufen → Startseite mit den Demo-Vereinen/Aktionen/Benefits sollte erscheinen
3. `/impressum`, `/datenschutz`, `/barrierefreiheit` aufrufen → Rechtsseiten mit Platzhalter-Hinweisen
4. `/verwaltung` weiterhin normal erreichbar (Filament unverändert)
5. Cookie-Banner erscheint beim ersten Besuch; „Cookie-Einstellungen“ im Footer öffnet den Dialog erneut
