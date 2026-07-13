@extends('layouts.app')

@section('title', 'Freiwilligenzentrum Kärnten – Gemeinsam für das Ehrenamt')
@section('meta_description', 'Das Freiwilligenzentrum Kärnten (FWZ) ist die zentrale Anlaufstelle des Landes Kärnten für ehrenamtliches Engagement – für Freiwillige und Vereine.')

@section('hero')
<section class="hero" id="top">
  <div class="container hero-inner">
    <div class="hero-copy">
      <h1 class="h1">Ich bin…<br><span>ehrenamtlich.</span></h1>
      <p>Hinter jedem Einsatz, jeder Übung, jeder helfenden Hand steht ein Mensch aus Kärnten. Werde Teil von etwas, das zählt — und schreib deine eigene Geschichte.</p>
      <div class="hero-cta">
        <a class="btn primary" href="#aktionen">Ich möchte helfen <span class="arrow">→</span></a>
        <a class="btn light" href="{{ route('registrierung.schritt1') }}">Verein anmelden <span class="arrow">→</span></a>
      </div>
    </div>
  </div>
</section>

<div class="container hero-stats">
  <div class="stats box">
    <div class="stat"><div class="stat-icon">◎</div><div><strong>{{ $stats['vereine'] }}</strong><span>Vereine</span></div></div>
    <div class="stat"><div class="stat-icon">◉</div><div><strong>{{ $stats['freiwillige'] }}</strong><span>aktive Freiwillige</span></div></div>
    <div class="stat"><div class="stat-icon">◌</div><div><strong>{{ $stats['stunden'] }}</strong><span>Stunden / Jahr</span></div></div>
    <div class="stat"><div class="stat-icon">✦</div><div><strong>{{ $stats['engagementFelder'] }}</strong><span>Engagement-Felder</span></div></div>
  </div>
</div>
@endsection

@section('content')

  <section class="section" id="fwz">
    <div class="container">
      <div class="box intro-box">
        <div class="intro-top">
          <span class="eyebrow">Das Freiwilligenzentrum</span>
          <h2 class="h2">Das Freiwilligenzentrum Kärnten.</h2>
          <p class="lead">Das FWZ ist die zentrale, offizielle Anlaufstelle des Landes Kärnten für ehrenamtliches Engagement. Wir vernetzen Vereine, Organisationen und Menschen, die anpacken wollen — übersichtlich, regional und kostenfrei.</p>
        </div>
        <div class="features">
          <div class="feature">
            <img class="icon-img" src="{{ asset('img/icon-freiwillige.svg') }}" alt="Icon Freiwillige">
            <h3 class="h3">Für Freiwillige</h3>
            <p>Finde Vereine, Projekte und Veranstaltungen in deiner Region — und entdecke dort deinen Platz, wo du wirken willst.</p>
          </div>
          <div class="feature">
            <img class="icon-img" src="{{ asset('img/icon-vereine.svg') }}" alt="Icon Vereine">
            <h3 class="h3">Für Vereine</h3>
            <p>Macht eure Arbeit sichtbar, gewinnt neue Mitstreiter:innen und werdet Teil eines kärntenweiten Netzwerks.</p>
          </div>
          <div class="feature">
            <img class="icon-img" src="{{ asset('img/icon-benefits.svg') }}" alt="Icon Benefits">
            <h3 class="h3">Benefits</h3>
            <p>Mit dem persönlichen Vereinscode öffnen sich Vergünstigungen bei ausgewählten Kärntner Partnerbetrieben.</p>
          </div>
          <div class="feature">
            <img class="icon-img" src="{{ asset('img/icon-sicherheit.svg') }}" alt="Icon Sicherheit">
            <h3 class="h3">Sicherheitsnetz</h3>
            <p>Zusätzlicher Versicherungsschutz für freiwillig Engagierte während der Freiwilligenarbeit in Kärnten.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="willkommen">
    <div class="container">
      <div class="box welcome-box">
        <div class="welcome-media">
          <img src="{{ asset('img/welcome-photo.png') }}" alt="Walter Gitschthaler und Ing. Daniel Fellner gemeinsam">
        </div>
        <div class="welcome-text">
          <span class="eyebrow">Herzlich willkommen</span>
          <h2 class="h2">Gemeinsam für das Ehrenamt in Kärnten.</h2>
          <p>Liebe Kärntnerinnen und Kärntner,</p>
          <p>mit großem Stolz und tiefer Überzeugung dürfen wir Ihnen das erste Freiwilligenzentrum Kärntens vorstellen — eine zentrale Anlaufstelle für all jene, die sich ehrenamtlich engagieren oder freiwilliges Engagement ermöglichen möchten.</p>
          <p>Die Gründung dieses Zentrums ist ein bedeutender Meilenstein unserer Ehrenamts-Offensive. Ohne die unermüdliche Arbeit unserer Ehrenamtlichen wäre unser gesellschaftliches Zusammenleben in dieser Form nicht aufrechtzuerhalten.</p>
          <p>Das Ehrenamt ist mehr als nur eine Ergänzung zu staatlichen Strukturen — es ist die Seele unseres Miteinanders. Ob bei Feuerwehr, Rettung, Bergrettung, in der Pflege, in Bildungsinitiativen oder zahllosen anderen Initiativen: Freiwillige leisten Tag für Tag Unbezahlbares.</p>
          <p>Wir laden Sie herzlich ein, Teil dieser Bewegung zu sein — durch Ihr Engagement, Ihre Ideen oder Ihre Anerkennung. Gemeinsam gestalten wir eine noch solidarischere und lebenswertere Zukunft für Kärnten.</p>
          <div class="welcome-signoff">Walter Gitschthaler, MSD · Schirmherr des FWZ Kärnten<br>Ing. Daniel Fellner · Landeshauptmann von Kärnten</div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="registrieren">
    <div class="container">
      <div class="box centered-box">
        <div class="section-title center">
          <span class="eyebrow">In 3 Schritten zum Mitglied</span>
          <h2 class="h2">So registrierst du deinen Verein.</h2>
        </div>
        <div class="steps-grid">
          <div class="step">
            <div class="step-num">1</div>
            <h3 class="h3">Formular ausfüllen</h3>
            <p>Vereinsdaten, Tätigkeitsfeld und Ansprechperson eintragen — dauert nur wenige Minuten.</p>
          </div>
          <div class="step">
            <div class="step-num">2</div>
            <h3 class="h3">Prüfung durch das FWZ-Team</h3>
            <p>Wir prüfen die Angaben und schalten euren Verein im Verzeichnis frei.</p>
          </div>
          <div class="step">
            <div class="step-num">3</div>
            <h3 class="h3">Vereinscode erhalten</h3>
            <p>Per E-Mail bekommt ihr den persönlichen Code für eure Mitglieder — inklusive Zugang zu allen Benefits.</p>
          </div>
        </div>
        <div class="button-center">
          <a class="btn dark" href="{{ route('registrierung.schritt1') }}">Jetzt Verein registrieren <span class="arrow">→</span></a>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="vereine">
    <div class="container">
      <div class="box directory-box">
        <div class="section-title center">
          <span class="eyebrow">Vereinsverzeichnis</span>
          <h2 class="h2">Finde Vereine, die schon dabei sind.</h2>
          <p>Durchsuche Mitglieds-Organisationen des Freiwilligenzentrums Kärnten — nach Name, Ort oder Tätigkeitsfeld.</p>
        </div>
        {{-- TODO: an echte Such-Route anbinden (GET q / ort auswerten) --}}
        <form class="searchbar" action="{{ route('home') }}" method="get">
          <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Verein, Organisation oder Stichwort" aria-label="Suche nach Verein"/>
          <input class="input" type="text" name="ort" value="{{ request('ort') }}" placeholder="Ort, Bezirk oder Region" aria-label="Ort oder Region"/>
          <button class="btn primary" type="submit">Suchen</button>
        </form>
        <div class="chips">
          @foreach($kategorien as $kategorie)
            <span class="chip">{{ $kategorie }}</span>
          @endforeach
        </div>
        <div class="org-grid">
          @forelse($vereine as $verein)
            <div class="org-card">
              <div class="org-logo">{{ $verein['kuerzel'] }}</div>
              <div class="name">{{ $verein['name'] }}</div>
              <div class="place">{{ $verein['ort'] }}</div>
            </div>
          @empty
            <p class="muted">Aktuell sind noch keine Vereine im Verzeichnis freigeschaltet.</p>
          @endforelse
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="aktionen">
    <div class="container">
      <div class="box banner-box">
        <div class="banner-head">
          <div>
            <span class="eyebrow">News &amp; Aktuelles</span>
            <h2 class="h2">Aktionen, die jetzt deine Hilfe brauchen.</h2>
          </div>
          <a class="btn dark" href="{{ route('in-arbeit') }}">Alle Aktionen <span class="arrow">→</span></a>
        </div>
        <div class="news-grid">
          @forelse($aktionen as $aktion)
            <article class="news-card">
              <img src="{{ $aktion['bild'] }}" alt="{{ $aktion['bild_alt'] }}">
              <div class="news-body">
                <div class="news-meta">{{ $aktion['typ'] }}</div>
                <h3 class="h3">{{ $aktion['titel'] }}</h3>
                <div class="news-data">
                  <div>{{ $aktion['veranstalter'] }}</div>
                  <div>{{ $aktion['ort'] }}</div>
                  <div>{{ $aktion['zeit'] }}</div>
                </div>
              </div>
            </article>
          @empty
            <p class="muted">Aktuell sind keine Aktionen eingetragen.</p>
          @endforelse
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="box benefit-box">
        <div class="section-title center">
          <span class="eyebrow">Exklusive Vorteile</span>
          <h2 class="h2">Benefits für Mitglieder.</h2>
          <p class="benefit-intro lead">Als Mitglied eines registrierten Vereins erhältst du Zugang zu Vergünstigungen bei ausgewählten Kärntner Partnerbetrieben.</p>
        </div>
        <div class="benefit-grid">
          @forelse($benefits as $benefit)
            <div class="benefit-card">
              <div>
                <div class="benefit-logo">{{ $benefit['partner'] }}</div>
                <p>{{ $benefit['beschreibung'] }}</p>
              </div>
              <span class="benefit-code">{{ $benefit['code'] }}</span>
            </div>
          @empty
            <p class="muted">Aktuell sind keine Benefits eingetragen.</p>
          @endforelse
        </div>
        <div class="button-center">
          <a class="btn dark" href="{{ route('in-arbeit') }}">Alle Benefits <span class="arrow">→</span></a>
        </div>
      </div>
    </div>
  </section>

  <section class="section testimonials">
    <div class="container">
      <div class="testi-head">
        <span class="eyebrow">Stimmen aus dem Ehrenamt</span>
        <h2 class="h2">Das sagen unsere Mitglieder</h2>
      </div>
      <div class="testi-grid">
        @foreach($testimonials as $t)
          <article class="testi-card">
            <div class="avatar">🙂</div>
            <div>
              <div class="quote">„{{ $t['zitat'] }}"</div>
              <div class="person">{{ $t['person'] }}</div>
              <div class="role">{{ $t['rolle'] }}</div>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>

@endsection
