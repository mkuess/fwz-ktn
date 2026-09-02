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

<div x-data="{ menuOpen: false }">

<header class="header">
  <nav class="nav">
    <div class="nav-left">
      <a href="{{ route('home') }}#top"><img class="logo" src="{{ asset('img/fwz-logo-new.svg') }}" alt="FWZ Kärnten" width="234" height="40"></a>
      <div class="menu" id="mobile-menu">
        <a href="{{ route('home') }}#fwz">Über uns</a>
        @if(\App\Models\Setting::enabled('organisation_registration_enabled'))
          <a href="{{ route('registrierung.schritt1') }}">Registrieren</a>
        @endif
        <a href="{{ route('organisations.map') }}">Vereine/Organisationen</a>
        <a href="#kontakt">Kontakt</a>
        <a href="{{ route('articles.index') }}">Aktuelles</a>
      </div>
    </div>
    <div class="nav-actions">
      @if(auth('member')->check())
        <a class="btn primary" href="{{ route('member.portal') }}">Mein Bereich <span class="arrow">→</span></a>
        <form method="POST" action="{{ route('member.logout') }}" style="display:inline">
          @csrf
          <button type="submit" class="btn light" style="cursor:pointer;font-family:inherit;border:none">Abmelden</button>
        </form>
      @else
        @if(\App\Models\Setting::enabled('login_button_enabled'))
          <a class="btn primary" href="{{ route('member.login') }}">Anmelden <span class="arrow">→</span></a>
        @endif
      @endif
    </div>
    <button
      class="nav-toggle"
      @click="menuOpen = !menuOpen"
      :aria-expanded="menuOpen.toString()"
      aria-controls="mobile-nav-panel"
      aria-label="Menü">
      <svg x-show="!menuOpen" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="3" y1="6" x2="21" y2="6"/>
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
      <svg x-show="menuOpen" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:none">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </nav>
</header>

<div class="mobile-nav-panel" id="mobile-nav-panel" x-show="menuOpen" x-cloak>

  {{-- Close button (inside panel, top-right) --}}
  <button
    @click="menuOpen = false"
    aria-label="Menü schließen"
    style="position:absolute;top:1.25rem;right:1.25rem;background:none;border:none;cursor:pointer;color:#fff;padding:0.5rem;line-height:1">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <line x1="18" y1="6" x2="6" y2="18"/>
      <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
  </button>

  <nav class="mobile-nav-panel__nav" aria-label="Mobilmenü">
    <a href="{{ route('home') }}#fwz" @click="menuOpen = false">Über uns</a>
    @if(\App\Models\Setting::enabled('organisation_registration_enabled'))
      <a href="{{ route('registrierung.schritt1') }}" @click="menuOpen = false">Registrieren</a>
    @endif
    <a href="{{ route('organisations.map') }}" @click="menuOpen = false">Vereine/Organisationen</a>
    <a
      href="#kontakt"
      @click.prevent="menuOpen = false; window.history.pushState(null, '', '#kontakt'); $nextTick(() => document.getElementById('kontakt').scrollIntoView({ behavior: 'smooth', block: 'start' }))">
      Kontakt
    </a>
    <a href="{{ route('articles.index') }}" @click="menuOpen = false">Aktuelles</a>
    @if(auth('member')->check())
      <a href="{{ route('member.portal') }}" @click="menuOpen = false">Mein Bereich</a>
      <form method="POST" action="{{ route('member.logout') }}" style="display:block">
        @csrf
        <button type="submit" class="mobile-nav-panel__nav-btn">Abmelden</button>
      </form>
    @else
      @if(\App\Models\Setting::enabled('login_button_enabled'))
        <a href="{{ route('member.login') }}" @click="menuOpen = false">Anmelden</a>
      @endif
    @endif
  </nav>

  <div class="mobile-nav-panel__actions">
    @if(\App\Models\Setting::enabled('organisation_registration_enabled'))
      <a class="btn light" href="{{ route('registrierung.schritt1') }}">Verein anmelden</a>
    @endif
  </div>

</div>

</div>{{-- end x-data --}}

@yield('hero')

<main id="main-content">
  @yield('content')
</main>

<footer class="footer" id="kontakt">
  <div class="container footer-grid">
    <div>
      <img class="footer-logo" src="{{ asset('img/fwz-logo-new.svg') }}" alt="FWZ Kärnten">
      <p>Die zentrale Anlaufstelle für freiwilliges Engagement in Kärnten — klar, regional und verbindend.</p>
    </div>
    <div>
      <h4>Kontakt</h4>
         <p><strong>Freiwilligenzentrum Kärnten</strong><br>Rosenegger Straße 20<br>A-9021 Klagenfurt am Wörthersee<br>E-Mail: info@fwz-ktn.at</p>
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
