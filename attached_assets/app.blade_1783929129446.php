<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Freiwilligenzentrum Kärnten')</title>
<meta name="description" content="@yield('meta_description', 'Das Freiwilligenzentrum Kärnten (FWZ) ist die zentrale Anlaufstelle des Landes Kärnten für ehrenamtliches Engagement.')">
<link rel="icon" href="{{ asset('img/fwz-logo.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

{{-- Ergänzung (nicht Teil der Vorlage, gesetzlich/barrierefrei nötig): Skip-Link --}}
<a class="skip-link" href="#main-content">Zum Hauptinhalt springen</a>

{{-- Ab hier: 1:1 aus der Vorlage übernommene Header-Struktur, unverändert --}}
<header class="header">
  <nav class="nav">
    <div class="nav-left">
      <a href="{{ route('home') }}#top"><img class="logo" src="{{ asset('img/fwz-logo.svg') }}" alt="FWZ Kärnten"></a>
      <div class="menu">
        <a href="{{ route('home') }}#fwz">Was ist FWZ</a>
        <a href="{{ route('home') }}#willkommen">Herzlich willkommen</a>
        <a href="{{ route('home') }}#registrieren">Registrieren</a>
        <a href="{{ route('home') }}#vereine">Vereine</a>
        <a href="{{ route('home') }}#aktionen">Aktuelles</a>
      </div>
    </div>
    <div class="nav-actions">
      <a class="btn primary" href="{{ route('home') }}#aktionen">Ich möchte helfen <span class="arrow">→</span></a>
    </div>
    {{-- Ergänzung: Menü-Umschalter, nur unterhalb des neuen Breakpoints sichtbar --}}
    <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="mobile-nav-panel" aria-label="Menü öffnen">
      <span class="nav-toggle__icon" aria-hidden="true">☰</span>
    </button>
  </nav>

  {{-- Ergänzung: eigenständiges Vollbild-Menü für kleinere Breiten. Enthält
       dieselben Links/Buttons wie oben, aber als eigener, deckender Layer –
       damit nichts mit dem Hero-Hintergrund/-Text durchscheint oder kollidiert. --}}
  <div class="mobile-nav-panel" id="mobile-nav-panel" hidden>
    <div class="mobile-nav-panel__inner">
      <a href="{{ route('home') }}#fwz">Was ist FWZ</a>
      <a href="{{ route('home') }}#willkommen">Herzlich willkommen</a>
      <a href="{{ route('home') }}#registrieren">Registrieren</a>
      <a href="{{ route('home') }}#vereine">Vereine</a>
      <a href="{{ route('home') }}#aktionen">Aktuelles</a>
      <div class="mobile-nav-panel__actions">
        <a class="btn primary" href="{{ route('home') }}#aktionen">Ich möchte helfen <span class="arrow">→</span></a>
      </div>
    </div>
  </div>
</header>

@yield('hero')

<main id="main-content">
  @yield('content')
</main>

{{-- Ab hier: 1:1 aus der Vorlage übernommene Footer-Struktur, unverändert
     (nur die vier "#"-Platzhalterlinks unter "Rechtliches" wurden auf die
     echten Routen der Rechtsseiten gesetzt, plus ein Cookie-Einstellungen-Link) --}}
<footer class="footer">
  <div class="container footer-grid">
    <div>
      <img class="footer-logo" src="{{ asset('img/footer-logo.svg') }}" alt="FWZ Kärnten">
      <p>Die zentrale Anlaufstelle für freiwilliges Engagement in Kärnten — klar, regional und verbindend.</p>
    </div>
    <div>
      <h4>Kontakt</h4>
      <p>Freiwilligenzentrum Kärnten<br>Bahnhofplatz 5/1<br>9020 Klagenfurt</p>
      <p style="margin-top:12px">+43 463 50 56 60<br>office@freiwilligenzentrum-kaernten.at</p>
    </div>
    <div>
      <h4>Schnellzugriff</h4>
      <ul>
        <li><a href="{{ route('home') }}#fwz">Über das FWZ</a></li>
        <li><a href="{{ route('home') }}#registrieren">Verein registrieren</a></li>
        <li><a href="{{ route('home') }}#vereine">Vereine finden</a></li>
        <li><a href="{{ route('home') }}#aktionen">Aktionen & Termine</a></li>
      </ul>
    </div>
    <div>
      <h4>Rechtliches & Social</h4>
      <ul>
        <li><a href="{{ route('impressum') }}">Impressum</a></li>
        <li><a href="{{ route('datenschutz') }}">Datenschutz</a></li>
        <li><a href="{{ route('barrierefreiheit') }}">Barrierefreiheit</a></li>
        <li><a href="#" data-action="open-settings">Cookie-Einstellungen</a></li>
      </ul>
      <div class="socials">
        <span class="social">f</span>
        <span class="social">◎</span>
        <span class="social">▶</span>
      </div>
    </div>
  </div>
  <div class="container copyright">© {{ date('Y') }} Freiwilligenzentrum Kärnten</div>
</footer>

<script src="{{ asset('js/main.js') }}" defer></script>
<script src="{{ asset('js/cookie-consent.js') }}" defer></script>
@stack('scripts')
</body>
</html>
