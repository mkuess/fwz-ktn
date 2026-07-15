<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Freiwilligenzentrum Kärnten')</title>
<meta name="description" content="@yield('meta_description', 'Das Freiwilligenzentrum Kärnten (FWZ) ist die zentrale Anlaufstelle des Landes Kärnten für ehrenamtliches Engagement.')">
<link rel="icon" href="{{ asset('img/fwz-logo.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
@stack('styles')
</head>
<body>

<a class="skip-link" href="#main-content">Zum Hauptinhalt springen</a>

<header class="header">
  <nav class="nav">
    <div class="nav-left">
      <a href="{{ route('home') }}#top"><img class="logo" src="{{ asset('img/fwz-logo-new.svg') }}" alt="FWZ Kärnten" width="234" height="40"></a>
      <div class="menu" id="mobile-menu">
        <a href="{{ route('home') }}#fwz">Was ist FWZ</a>
        <a href="{{ route('registrierung.schritt1') }}">Registrieren</a>
        <a href="{{ route('home') }}#vereine">Vereine</a>
        <a href="{{ route('articles.index') }}">Aktuelles</a>
      </div>
    </div>
    <div class="nav-actions">
      <a class="btn primary" href="{{ route('member.register') }}">Benefits als Mitglied sichern <span class="arrow">→</span></a>
    </div>
    <button class="nav-toggle" aria-expanded="false" aria-controls="mobile-nav-panel" aria-label="Menü öffnen"><span aria-hidden="true">☰</span></button>
  </nav>
</header>

<div class="mobile-nav-panel" id="mobile-nav-panel" hidden>
  <nav class="mobile-nav-panel__nav" aria-label="Mobilmenü">
    <a href="{{ route('home') }}#fwz">Was ist FWZ</a>
    <a href="{{ route('registrierung.schritt1') }}">Registrieren</a>
    <a href="{{ route('home') }}#vereine">Vereine</a>
    <a href="{{ route('articles.index') }}">Aktuelles</a>
  </nav>
  <div class="mobile-nav-panel__actions">
    <a class="btn primary" href="{{ route('member.register') }}">Benefits als Mitglied sichern <span class="arrow">→</span></a>
    <a class="btn light" href="{{ route('registrierung.schritt1') }}">Verein anmelden</a>
  </div>
</div>

@yield('hero')

<main id="main-content">
  @yield('content')
</main>

<footer class="footer">
  <div class="container footer-grid">
    <div>
      <img class="footer-logo" src="{{ asset('img/fwz-logo-new.svg') }}" alt="FWZ Kärnten">
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
        <li><a href="{{ route('registrierung.schritt1') }}">Verein registrieren</a></li>
        <li><a href="{{ route('home') }}#vereine">Vereine finden</a></li>
        <li><a href="{{ route('articles.index') }}">Aktionen &amp; Termine</a></li>
      </ul>
    </div>
    <div>
      <h4>Rechtliches &amp; Social</h4>
      <ul>
        <li><a href="{{ route('impressum') }}">Impressum</a></li>
        <li><a href="{{ route('datenschutz') }}">Datenschutz</a></li>
        <li><a href="{{ route('barrierefreiheit') }}">Barrierefreiheit</a></li>
        <li><a href="#" onclick="resetCookieConsent(); return false;" style="font-size:0.75rem;color:#9ca3af">Cookie-Einstellungen</a></li>
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

@include('partials.cookie-banner')
<script src="{{ asset('js/main.js') }}" defer></script>
<script src="{{ asset('js/vereine-suche.js') }}" defer></script>
@stack('scripts')
<script>
function resetCookieConsent() {
    localStorage.removeItem('fwz_cookie_consent');
    window.location.reload();
}
</script>
</body>
</html>
