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

<a class="skip-link" href="#main-content">Zum Hauptinhalt springen</a>

<header class="header">
  <nav class="nav">
    <div class="nav-left">
      <a href="{{ route('home') }}#top"><img class="logo" src="{{ asset('img/fwz-logo-hor.svg') }}" alt="FWZ Kärnten" width="228" height="40"></a>
      <div class="menu" id="mobile-menu">
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
    <button class="nav-toggle" aria-expanded="false" aria-controls="mobile-nav-panel" aria-label="Menü öffnen"><span aria-hidden="true">☰</span></button>
  </nav>
</header>

<div class="mobile-nav-panel" id="mobile-nav-panel" hidden>
  <nav class="mobile-nav-panel__nav" aria-label="Mobilmenü">
    <a href="{{ route('home') }}#fwz">Was ist FWZ</a>
    <a href="{{ route('home') }}#willkommen">Herzlich willkommen</a>
    <a href="{{ route('home') }}#registrieren">Registrieren</a>
    <a href="{{ route('home') }}#vereine">Vereine</a>
    <a href="{{ route('home') }}#aktionen">Aktuelles</a>
  </nav>
  <div class="mobile-nav-panel__actions">
    <a class="btn primary" href="{{ route('home') }}#aktionen">Ich möchte helfen <span class="arrow">→</span></a>
    <a class="btn light" href="{{ route('home') }}#registrieren">Verein anmelden</a>
  </div>
</div>

@yield('hero')

<main id="main-content">
  @yield('content')
</main>

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
        <li><a href="{{ route('home') }}#aktionen">Aktionen &amp; Termine</a></li>
      </ul>
    </div>
    <div>
      <h4>Rechtliches &amp; Social</h4>
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
</body>
</html>
