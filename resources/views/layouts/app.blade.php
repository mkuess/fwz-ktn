<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Freiwilligenzentrum Kärnten – Gemeinsam für das Ehrenamt')</title>
<meta name="description" content="@yield('description', 'Das Freiwilligenzentrum Kärnten (FWZ) ist die zentrale Anlaufstelle des Landes Kärnten für ehrenamtliches Engagement – für Freiwillige und Vereine.')">
<meta name="color-scheme" content="light">
<link rel="icon" href="{{ asset('img/fwz-logo.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<a class="skip-link" href="#main-content">Zum Hauptinhalt springen</a>

<header class="header">
  <nav class="nav" aria-label="Hauptnavigation">
    <div class="nav-left">
      <a href="{{ url('/') }}" aria-label="Freiwilligenzentrum Kärnten – Startseite">
        <img class="logo" src="{{ asset('img/fwz-logo.svg') }}" alt="Freiwilligenzentrum Kärnten" width="168" height="78">
      </a>
      <ul class="menu" id="mobile-menu">
        <li><a href="{{ url('/') }}#fwz">Was ist FWZ</a></li>
        <li><a href="{{ url('/') }}#willkommen">Herzlich willkommen</a></li>
        <li><a href="{{ url('/') }}#registrieren">Registrieren</a></li>
        <li><a href="{{ url('/') }}#vereine">Vereine</a></li>
        <li><a href="{{ url('/') }}#aktionen">Aktuelles</a></li>
      </ul>
    </div>
    <div class="nav-actions">
      <a class="btn primary" href="{{ url('/') }}#aktionen">Ich möchte helfen <span class="arrow" aria-hidden="true">→</span></a>
      <a class="btn light" href="{{ url('/') }}#registrieren">Verein anmelden</a>
    </div>
    <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="mobile-menu">
      Menü <span aria-hidden="true">☰</span>
    </button>
  </nav>
</header>

<main id="main-content">
  @yield('content')
</main>

<footer class="footer">
  <div class="container footer-grid">
    <div>
      <img class="footer-logo" src="{{ asset('img/fwz-logo.svg') }}" alt="Freiwilligenzentrum Kärnten" width="130" height="60">
      <p>Die zentrale Anlaufstelle für freiwilliges Engagement in Kärnten — klar, regional und verbindend.</p>
    </div>
    <div>
      <h4>Kontakt</h4>
      <p>Freiwilligenzentrum Kärnten<br>Bahnhofplatz 5/1<br>9020 Klagenfurt</p>
      <p style="margin-top:12px">
        <a href="tel:+4346350566020">+43 463 50 56 60</a><br>
        <a href="mailto:office@freiwilligenzentrum-kaernten.at">office@freiwilligenzentrum-kaernten.at</a>
      </p>
    </div>
    <div>
      <h4>Schnellzugriff</h4>
      <ul>
        <li><a href="{{ url('/') }}#fwz">Über das FWZ</a></li>
        <li><a href="{{ url('/') }}#registrieren">Verein registrieren</a></li>
        <li><a href="{{ url('/') }}#vereine">Vereine finden</a></li>
        <li><a href="{{ url('/') }}#aktionen">Aktionen &amp; Termine</a></li>
      </ul>
    </div>
    <div>
      <h4>Rechtliches &amp; Social</h4>
      <ul class="legal-links" style="flex-direction:column;">
        <li><a href="{{ route('impressum') }}">Impressum</a></li>
        <li><a href="{{ route('datenschutz') }}">Datenschutz</a></li>
        <li><a href="{{ route('barrierefreiheit') }}">Barrierefreiheit</a></li>
        <li><a href="#" data-action="open-settings">Cookie-Einstellungen</a></li>
      </ul>
      <div class="socials">
        <a class="social" href="#" aria-label="Freiwilligenzentrum Kärnten auf Facebook">f</a>
        <a class="social" href="#" aria-label="Freiwilligenzentrum Kärnten auf Instagram">◎</a>
        <a class="social" href="#" aria-label="Freiwilligenzentrum Kärnten auf YouTube">▶</a>
      </div>
    </div>
  </div>
  <div class="container copyright">
    <span>© <span id="current-year">{{ date('Y') }}</span> Freiwilligenzentrum Kärnten</span>
    <span class="legal-links">
      <a href="{{ route('impressum') }}">Impressum</a>
      <a href="{{ route('datenschutz') }}">Datenschutz</a>
      <a href="{{ route('barrierefreiheit') }}">Barrierefreiheit</a>
      <a href="#" data-action="open-settings">Cookie-Einstellungen</a>
    </span>
  </div>
</footer>

<script src="{{ asset('js/main.js') }}" defer></script>
<script src="{{ asset('js/cookie-consent.js') }}" defer></script>
</body>
</html>
