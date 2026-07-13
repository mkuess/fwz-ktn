# FWZ Kärnten – Rollback auf 1:1-Vorlage (v2)

## Was hier passiert ist

Die vorherige Version wurde teilweise **aus dem Gedächtnis/Chat-Text rekonstruiert**, weil die Original-Datei
`fwz_Kopie.html` wegen eingebetteter Bilder (Base64) im Chat nie vollständig lesbar war — auch für mich nicht.
Das war der Fehler.

Für diese Version habe ich die Originaldatei direkt im Dateisystem verarbeitet (nicht über den Chat gelesen)
und:

1. Das komplette `<style>`-Element extrahiert → **exakt 156 Zeilen Original-CSS**, byte-für-byte.
2. Den kompletten `<body>` extrahiert → **exakt 320 Zeilen Original-HTML**, byte-für-byte.
3. Alle 6 eingebetteten Base64-Bilder decodiert und als echte Dateien gespeichert (siehe unten) — vorher
   waren das für mich nur "Datenmüll" im Chat, jetzt sind es normale Bilddateien.

**CSS- und HTML-Struktur, Klassennamen, Texte, Reihenfolge sind jetzt 1:1 aus der Vorlage übernommen.**
Ergänzungen (Skip-Link, Fokusring, Cookie-Banner) stehen **unterhalb einer klaren Trennmarkierung** im CSS
und wurden an keiner Stelle in bestehende Regeln eingemischt.

## Was beim Original mit rausgekommen ist (vorher unbekannt)

- Der Hero-Bereich hat ein **echtes Hintergrundfoto mit dunklem Verlaufsoverlay** (`.hero::before`/`::after`)
  — kein reiner Farbverlauf, wie in der vorherigen Version. Datei: `public/img/hero-background.png`
  (aktuell das Platzhalterfoto aus der Vorlage, 1916×821px).
- Die 4 Feature-Icons sind eigenständige SVG-Dateien, die per CSS-Filter gelb eingefärbt werden
  (`.icon-img{filter:...}`) — nicht die von mir vorher gezeichneten blauen Outline-Icons.
- Der Willkommens-Bereich hat ein echtes Foto (`welcome-photo.png`), kein generiertes Platzhalter-SVG.
- Der Footer verwendet ein **eigenes Logo** (`footer-logo.svg`, Vollwortmarke „Freiwilligenzentrum Kärnten“),
  das sich vom Nav-Logo (`fwz-logo.svg`) unterscheidet — beide waren in der Vorlage bereits verschieden.
- Die drei News-Bilder waren in der Vorlage als `1.avif`/`3.avif`/`4.avif` referenziert, aber nie mitgeliefert
  (defekte Bilder auch im Original). Ich habe echte, korrekt benannte `.avif`-Platzhalterdateien erzeugt,
  damit nichts kaputt verlinkt ist.

## Zwei bekannte Schwachstellen **aus der Vorlage selbst** — bitte bewusst entscheiden

Diese habe ich **nicht** stillschweigend "repariert", weil ihr explizit 1:1 wolltet. Ihr solltet sie aber
kennen, bevor die Seite live geht:

1. **`.eyebrow`-Text ist reines Gelb (`#e4a400`) auf Weiß.** Kontrastverhältnis ca. 2,2:1 — das erfüllt
   WCAG AA (mind. 4,5:1) nicht. Betrifft z. B. "Das Freiwilligenzentrum", "Herzlich willkommen" etc.
2. **Auf Bildschirmen unter 900px Breite verschwindet das Hauptmenü komplett** (`@media(max-width:900px){.menu{display:none}}`) — es gibt in der Vorlage **kein** Ersatz-Menü (kein Hamburger-Icon). Mobile Nutzer:innen erreichen die Menüpunkte dann nur noch durch Scrollen.

Sag mir Bescheid, ob ich das (a) exakt so lasse, (b) nur den Farbkontrast beim Eyebrow-Gelb nachdunkle
(rein optisch minimal anders), oder (c) zusätzlich ein Mobile-Menü ergänze — alles ohne das restliche Design
anzufassen.

## Offener Punkt aus dem vorherigen Chat: horizontales Logo + volle Navbar-Breite

Die Datei `fwz-logo-hor.svg` liegt bereits in `public/img/`, wird aber in dieser 1:1-Version **nicht**
verwendet (Nav-Logo ist wieder das Original `fwz-logo.svg`, wie in der Vorlage referenziert). Sag mir, ob ihr
diese Änderung (horizontales Logo + Navbar über volle Breite) jetzt **on top** von dieser korrekten Basis
wieder haben wollt — dann schreibe ich dir dafür einen sauberen, isolierten zweiten Patch.

## Einbau in Replit

Gleiches Vorgehen wie beim letzten Mal:

```
public/css/style.css                → euer public/css/style.css
public/js/main.js                   → euer public/js/main.js
public/js/cookie-consent.js         → euer public/js/cookie-consent.js
public/img/*                        → euer public/img/
resources/views/layouts/app.blade.php → euer resources/views/layouts/
resources/views/*.blade.php         → euer resources/views/
app/Http/Controllers/HomeController.php → euer app/Http/Controllers/
routes-web-snippet.php              → Inhalt in eure routes/web.php einfügen (vor Fallback-Route)
```

`/verwaltung` (Filament) bleibt unberührt. Rechts-/Kontaktdaten in den drei Rechtsseiten (`impressum.blade.php`
etc.) sind weiterhin mit `[…]` markiert und müssen vor Go-Live ausgefüllt werden — das war nie Teil der
Design-Vorlage und bleibt unverändert bestehen.
