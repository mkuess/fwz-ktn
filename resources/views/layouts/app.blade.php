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
@stack('styles')
</head>
<body>

<a class="skip-link" href="#main-content">Zum Hauptinhalt springen</a>

<header class="header">
  <nav class="nav">
    <div class="nav-left">
      <a href="{{ route('home') }}#top"><img class="logo" src="{{ asset('img/fwz-logo-hor.svg') }}" alt="FWZ Kärnten" width="228" height="40"></a>
      <div class="menu" id="mobile-menu">
        <a href="{{ route('home') }}#fwz">Was ist FWZ</a>
        <a href="{{ route('registrierung.schritt1') }}">Registrieren</a>
        <a href="{{ route('home') }}#vereine">Vereine</a>
        <a href="{{ route('articles.index') }}">Aktuelles</a>
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
    <a href="{{ route('registrierung.schritt1') }}">Registrieren</a>
    <a href="{{ route('home') }}#vereine">Vereine</a>
    <a href="{{ route('articles.index') }}">Aktuelles</a>
  </nav>
  <div class="mobile-nav-panel__actions">
    <a class="btn primary" href="{{ route('home') }}#aktionen">Ich möchte helfen <span class="arrow">→</span></a>
    <a class="btn light" href="{{ route('registrierung.schritt1') }}">Verein anmelden</a>
  </div>
</div>

@yield('hero')

<main id="main-content">
  @yield('content')
</main>

<footer style="background:#1a2e1a;color:#fff;padding:3rem 0 0">
  <div style="max-width:1200px;margin:0 auto;padding:0 1.5rem">

    <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:2rem;border-bottom:1px solid rgba(255,255,255,0.15);margin-bottom:2rem">
      <a href="/">
        <img src="{{ asset('img/footer-logo.svg') }}" alt="Freiwilligenzentrum Kärnten" style="height:2.5rem">
      </a>
      <a href="https://www.instagram.com/freiwilligenzentrum.kaernten" target="_blank" rel="noopener" aria-label="Instagram"
         style="color:#fff;opacity:0.8;transition:opacity 0.2s"
         onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
        </svg>
      </a>
    </div>

    <div class="footer-cols">

      <div>
        <h3 style="font-size:0.875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#c9a227;margin:0 0 1rem">Über FWZ</h3>
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.625rem">
          <li><a href="/#was-ist-fwz" class="footer-link">Was ist FWZ?</a></li>
          <li><a href="{{ route('articles.index') }}" class="footer-link">Aktuelles</a></li>
          <li><a href="{{ route('organisations.index') }}" class="footer-link">Vereine</a></li>
          <li><a href="{{ route('benefits.index') }}" class="footer-link">Benefits</a></li>
        </ul>
      </div>

      <div>
        <h3 style="font-size:0.875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#c9a227;margin:0 0 1rem">Mitmachen</h3>
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.625rem">
          <li><a href="{{ route('member.register') }}" class="footer-link">Mitglied werden</a></li>
          <li><a href="{{ route('registrierung.schritt1') }}" class="footer-link">Verein anmelden</a></li>
        </ul>
      </div>

      <div>
        <h3 style="font-size:0.875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#c9a227;margin:0 0 1rem">Kontakt</h3>
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.625rem;font-size:0.9rem;color:rgba(255,255,255,0.75)">
          <li>Freiwilligenzentrum Kärnten</li>
          <li>Rosenegger Straße 20</li>
          <li>9020 Klagenfurt am Wörthersee</li>
          <li style="margin-top:0.5rem"><a href="mailto:info@fwz-ktn.at" class="footer-link">info@fwz-ktn.at</a></li>
        </ul>
      </div>

    </div>

    <div style="border-top:1px solid rgba(255,255,255,0.15);padding:1.5rem 0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;font-size:0.8rem;color:rgba(255,255,255,0.5)">
      <span>© {{ date('Y') }} Freiwilligenzentrum Kärnten</span>
      <div style="display:flex;gap:1.5rem;flex-wrap:wrap">
        <a href="/impressum" class="footer-legal-link">Impressum</a>
        <a href="/datenschutz" class="footer-legal-link">Datenschutz</a>
        <a href="/barrierefreiheit" class="footer-legal-link">Barrierefreiheit</a>
        <a href="#" onclick="localStorage.removeItem('fwz_cookie_consent'); location.reload(); return false;" class="footer-legal-link">Cookie-Einstellungen</a>
      </div>
    </div>

  </div>
</footer>

@include('partials.cookie-banner')
<script src="{{ asset('js/main.js') }}" defer></script>
<script src="{{ asset('js/vereine-suche.js') }}" defer></script>
@stack('scripts')
</body>
</html>
