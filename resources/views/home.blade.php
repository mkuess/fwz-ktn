@extends('layouts.app')

@section('title', 'Freiwilligenzentrum Kärnten – Gemeinsam für das Ehrenamt')

@section('content')

{{-- Hero is outside <main> semantically on the original; we render it here inside main --}}
<section class="hero" id="top">
  <div class="container hero-inner">
    <div class="hero-copy">
      <h1 class="h1">Ich bin…<br><span>ehrenamtlich.</span></h1>
      <p>Hinter jedem Einsatz, jeder Übung, jeder helfenden Hand steht ein Mensch aus Kärnten. Werde Teil von etwas, das zählt — und schreib deine eigene Geschichte.</p>
      <div class="hero-cta">
        <a class="btn primary" href="#aktionen">Ich möchte helfen <span class="arrow" aria-hidden="true">→</span></a>
        <a class="btn light" href="#registrieren">Organisation anmelden <span class="arrow" aria-hidden="true">→</span></a>
      </div>
    </div>
  </div>
</section>

<div class="container hero-stats">
  <div class="stats box" role="list" aria-label="Kennzahlen des Freiwilligenzentrums Kärnten">
    <div class="stat" role="listitem"><span class="stat-icon" aria-hidden="true">◎</span><div><strong>{{ $stats['vereine'] }}</strong><span>Vereine</span></div></div>
    <div class="stat" role="listitem"><span class="stat-icon" aria-hidden="true">◉</span><div><strong>{{ $stats['freiwillige'] }}</strong><span>aktive Freiwillige</span></div></div>
    <div class="stat" role="listitem"><span class="stat-icon" aria-hidden="true">◌</span><div><strong>{{ $stats['stunden'] }}</strong><span>Stunden pro Jahr</span></div></div>
    <div class="stat" role="listitem"><span class="stat-icon" aria-hidden="true">✦</span><div><strong>{{ $stats['engagementFelder'] }}</strong><span>Engagement-Felder</span></div></div>
  </div>
</div>

{{-- Was ist FWZ --}}
<section class="section" id="fwz" aria-labelledby="fwz-title">
  <div class="container">
    <div class="box intro-box">
      <div class="intro-top">
        <span class="eyebrow">Das Freiwilligenzentrum</span>
        <h2 class="h2" id="fwz-title">Das Freiwilligenzentrum Kärnten.</h2>
        <p class="lead">Das FWZ ist die zentrale, offizielle Anlaufstelle des Landes Kärnten für ehrenamtliches Engagement. Wir vernetzen Vereine, Organisationen und Menschen, die anpacken wollen — übersichtlich, regional und kostenfrei.</p>
      </div>
      <div class="features">
        <div class="feature">
          <svg class="icon-img" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="#0b3165" stroke-width="1.6"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c0-4 3-6.5 6.5-6.5S15.5 16 15.5 20"/><circle cx="17" cy="9" r="2.6"/><path d="M14.8 20c.2-3 2-5 4.7-5 2.4 0 4.3 1.6 4.7 4"/></svg>
          <h3 class="h3">Für Freiwillige</h3>
          <p>Finde Vereine, Projekte und Veranstaltungen in deiner Region — und entdecke dort deinen Platz, wo du wirken willst.</p>
        </div>
        <div class="feature">
          <svg class="icon-img" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="#0b3165" stroke-width="1.6"><path d="M3 21V9l9-6 9 6v12"/><path d="M8 21v-7h8v7"/></svg>
          <h3 class="h3">Für Vereine</h3>
          <p>Macht eure Arbeit sichtbar, gewinnt neue Mitstreiter:innen und werdet Teil eines kärntenweiten Netzwerks.</p>
        </div>
        <div class="feature">
          <svg class="icon-img" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="#0b3165" stroke-width="1.6"><rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2.4"/><path d="M6 8V6M18 8V6M6 18v-2M18 18v-2"/></svg>
          <h3 class="h3">Benefits</h3>
          <p>Mit dem persönlichen Vereinscode öffnen sich Vergünstigungen bei ausgewählten Kärntner Partnerbetrieben.</p>
        </div>
        <div class="feature">
          <svg class="icon-img" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="#0b3165" stroke-width="1.6"><path d="M12 2l8 3.2v6c0 5.4-3.4 8.6-8 10.8-4.6-2.2-8-5.4-8-10.8v-6z"/><path d="M8.5 12l2.4 2.4L16 9.4"/></svg>
          <h3 class="h3">Sicherheitsnetz</h3>
          <p>Zusätzlicher Versicherungsschutz für freiwillig Engagierte während der Freiwilligenarbeit in Kärnten.</p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Herzlich willkommen --}}
<section class="section" id="willkommen" aria-labelledby="willkommen-title">
  <div class="container">
    <div class="box welcome-box">
      <div class="welcome-media">
        <img src="{{ asset('img/placeholder-welcome.svg') }}" alt="Platzhalterbild – wird durch ein Foto des Freiwilligenzentrums ersetzt" width="800" height="600" loading="lazy">
      </div>
      <div class="welcome-text">
        <span class="eyebrow">Herzlich willkommen</span>
        <h2 class="h2" id="willkommen-title">Gemeinsam für das Ehrenamt in Kärnten.</h2>
        <p>Liebe Kärntnerinnen und Kärntner,</p>
        <p>mit großem Stolz und tiefer Überzeugung dürfen wir Ihnen das erste Freiwilligenzentrum Kärntens vorstellen — eine zentrale Anlaufstelle für all jene, die sich ehrenamtlich engagieren oder freiwilliges Engagement ermöglichen möchten.</p>
        <p>Die Gründung dieses Zentrums ist ein bedeutender Meilenstein unserer Ehrenamts-Offensive. Ohne die unermüdliche Arbeit unserer Ehrenamtlichen wäre unser gesellschaftliches Zusammenleben in dieser Form nicht aufrechtzuerhalten.</p>
        <p>Das Ehrenamt ist mehr als nur eine Ergänzung zu staatlichen Strukturen — es ist die Seele unseres Miteinanders. Ob bei Feuerwehr, Rettung, Bergrettung, in der Pflege, in Bildungsinitiativen oder zahllosen anderen Initiativen: Freiwillige leisten Tag für Tag Unbezahlbares.</p>
        <p>Wir laden Sie herzlich ein, Teil dieser Bewegung zu sein — durch Ihr Engagement, Ihre Ideen oder Ihre Anerkennung. Gemeinsam gestalten wir eine noch solidarischere und lebenswertere Zukunft für Kärnten.</p>
        <p class="welcome-signoff">Walter Gitschthaler, MSD · Schirmherr des FWZ Kärnten<br>Ing. Daniel Fellner · Landeshauptmann von Kärnten</p>
      </div>
    </div>
  </div>
</section>

{{-- Registrieren --}}
<section class="section" id="registrieren" aria-labelledby="registrieren-title">
  <div class="container">
    <div class="box centered-box">
      <div class="section-title center">
        <span class="eyebrow">In 3 Schritten zum Mitglied</span>
        <h2 class="h2" id="registrieren-title">So registrierst du deinen Verein.</h2>
      </div>
      <ol class="steps-grid" style="list-style:none;padding:0;">
        <li class="step">
          <div class="step-num" aria-hidden="true">1</div>
          <h3 class="h3">Formular ausfüllen</h3>
          <p>Vereinsdaten, Tätigkeitsfeld und Ansprechperson eintragen — dauert nur wenige Minuten.</p>
        </li>
        <li class="step">
          <div class="step-num" aria-hidden="true">2</div>
          <h3 class="h3">Prüfung durch das FWZ-Team</h3>
          <p>Wir prüfen die Angaben und schalten euren Verein im Verzeichnis frei.</p>
        </li>
        <li class="step">
          <div class="step-num" aria-hidden="true">3</div>
          <h3 class="h3">Vereinscode erhalten</h3>
          <p>Per E-Mail bekommt ihr den persönlichen Code für eure Mitglieder — inklusive Zugang zu allen Benefits.</p>
        </li>
      </ol>
      <div class="button-center">
        <a class="btn dark" href="{{ route('in-arbeit') }}">Jetzt Verein registrieren <span class="arrow" aria-hidden="true">→</span></a>
      </div>
    </div>
  </div>
</section>

{{-- Vereinsverzeichnis --}}
<section class="section" id="vereine" aria-labelledby="vereine-title">
  <div class="container">
    <div class="box directory-box">
      <div class="section-title center">
        <span class="eyebrow">Vereinsverzeichnis</span>
        <h2 class="h2" id="vereine-title">Finde Vereine, die schon dabei sind.</h2>
        <p>Durchsuche Mitglieds-Organisationen des Freiwilligenzentrums Kärnten — nach Name, Ort oder Tätigkeitsfeld.</p>
      </div>
      <form class="searchbar" role="search" aria-label="Vereinssuche" action="{{ url('/') }}" method="get">
        <div class="sr-only" id="q-label">Verein, Organisation oder Stichwort</div>
        <input class="input" type="search" name="q" placeholder="Verein, Organisation oder Stichwort" aria-labelledby="q-label" value="{{ request('q') }}">
        <div class="sr-only" id="ort-label">Ort, Bezirk oder Region</div>
        <input class="input" type="text" name="ort" placeholder="Ort, Bezirk oder Region" aria-labelledby="ort-label" value="{{ request('ort') }}">
        <button class="btn primary" type="submit">Suchen</button>
      </form>
      <div class="chips" role="group" aria-label="Nach Kategorie filtern">
        @foreach($kategorien as $kat)
          <button type="button" class="chip" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">{{ $kat }}</button>
        @endforeach
      </div>
      <ul class="org-grid" style="list-style:none;padding:0;" aria-label="Ergebnisliste Vereine">
        @foreach($vereine as $verein)
          <li class="org-card">
            <div class="org-logo" aria-hidden="true">{{ $verein['kuerzel'] ?? substr($verein['name'], 0, 3) }}</div>
            <div class="name">{{ $verein['name'] }}</div>
            <div class="place">{{ $verein['ort'] }}</div>
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</section>

{{-- Aktionen --}}
<section class="section" id="aktionen" aria-labelledby="aktionen-title">
  <div class="container">
    <div class="box banner-box">
      <div class="banner-head">
        <div>
          <span class="eyebrow">News &amp; Aktuelles</span>
          <h2 class="h2" id="aktionen-title">Aktionen, die jetzt deine Hilfe brauchen.</h2>
        </div>
        <a class="btn dark" href="{{ route('in-arbeit') }}">Alle Aktionen <span class="arrow" aria-hidden="true">→</span></a>
      </div>
      <ul class="news-grid" style="list-style:none;padding:0;">
        @foreach($aktionen as $i => $aktion)
          <li>
            <article class="news-card">
              <img src="{{ $aktion['bild'] ?? asset('img/placeholder-news-'.($loop->iteration).'.svg') }}"
                   alt="{{ $aktion['bild_alt'] ?? 'Platzhalterbild – wird durch ein Foto der Veranstaltung ersetzt' }}"
                   width="800" height="600" loading="lazy">
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
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</section>

{{-- Benefits --}}
<section class="section" aria-labelledby="benefits-title">
  <div class="container">
    <div class="box benefit-box">
      <div class="section-title center">
        <span class="eyebrow">Exklusive Vorteile</span>
        <h2 class="h2" id="benefits-title">Benefits für Mitglieder.</h2>
        <p class="benefit-intro lead">Als Mitglied eines registrierten Vereins erhältst du Zugang zu Vergünstigungen bei ausgewählten Kärntner Partnerbetrieben.</p>
      </div>
      <ul class="benefit-grid" style="list-style:none;padding:0;">
        @foreach($benefits as $benefit)
          <li class="benefit-card">
            <div>
              <div class="benefit-logo">{{ $benefit['partner'] }}</div>
              <p>{{ $benefit['beschreibung'] }}</p>
            </div>
            <span class="benefit-code">{{ $benefit['code'] }}</span>
          </li>
        @endforeach
      </ul>
      <div class="button-center">
        <a class="btn dark" href="{{ route('in-arbeit') }}">Alle Benefits <span class="arrow" aria-hidden="true">→</span></a>
      </div>
    </div>
  </div>
</section>

{{-- Testimonials --}}
<section class="section testimonials" aria-labelledby="stimmen-title">
  <div class="container">
    <div class="testi-head">
      <span class="eyebrow">Stimmen aus dem Ehrenamt</span>
      <h2 class="h2" id="stimmen-title">Das sagen unsere Mitglieder</h2>
    </div>
    <ul class="testi-grid" style="list-style:none;padding:0;">
      @foreach($testimonials as $t)
        <li>
          <article class="testi-card">
            <span class="avatar" aria-hidden="true">🙂</span>
            <div>
              <p class="quote">„{{ $t['zitat'] }}"</p>
              <div class="person">{{ $t['person'] }}</div>
              <div class="role">{{ $t['rolle'] }}</div>
            </div>
          </article>
        </li>
      @endforeach
    </ul>
  </div>
</section>

@endsection
