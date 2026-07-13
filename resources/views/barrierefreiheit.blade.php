@extends('layouts.app')

@section('title', 'Barrierefreiheit – Freiwilligenzentrum Kärnten')
@section('meta_description', 'Erklärung zur Barrierefreiheit des Freiwilligenzentrums Kärnten gemäß WZG / EU-Richtlinie 2016/2102.')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">Rechtliches</span>
    <h2 class="h2">Barrierefreiheit</h2>
    <p>Erklärung zur Barrierefreiheit gemäß EU-Richtlinie 2016/2102 und BGStG.</p>
  </div>
</div>
@endsection

@section('content')
<div class="legal-content">
  <div class="container">
    <div class="box">
      <div class="note-box">
        <strong>⚠️ Platzhalter</strong>
        Konformitätsstatus und geprüfte Details vor Go-Live eintragen. <span class="placeholder-field">[…]</span>-Felder ersetzen.
      </div>

      <h2>Stand der Vereinbarkeit</h2>
      <p>Diese Website ist <span class="placeholder-field">[vollständig / teilweise / nicht]</span> vereinbar mit den WCAG 2.1 Richtlinien, Konformitätsstufe AA.</p>

      <h2>Was wir umgesetzt haben</h2>
      <ul>
        <li>Semantisches HTML5 mit korrekter Überschriftenhierarchie</li>
        <li>Skip-Link „Zum Hauptinhalt springen" auf jeder Seite</li>
        <li>Sichtbare Fokus-Zustände für Tastaturnavigation</li>
        <li>Alternativtexte für alle informationellen Bilder</li>
        <li>Formulare mit zugeordneten Labels</li>
        <li>Cookie-Banner per Tastatur bedienbar</li>
        <li><code>prefers-reduced-motion</code> wird respektiert</li>
      </ul>

      <h2>Bekannte Einschränkungen</h2>
      <p>Auf Bildschirmen unter 900 px Breite ist das Hauptmenü ausgeblendet (aus der Vorlage übernommen). Nutzer:innen können die Sektionen durch Scrollen erreichen. Ein Hamburger-Menü wird in einer späteren Version ergänzt.</p>

      <h2>Feedback und Kontakt</h2>
      <p>Haben Sie Barrieren festgestellt? Bitte melden Sie sich: <a href="mailto:office@freiwilligenzentrum-kaernten.at">office@freiwilligenzentrum-kaernten.at</a></p>

      <h2>Schlichtungsverfahren</h2>
      <p>Wenn Sie nach einer Kontaktaufnahme keine zufriedenstellende Antwort erhalten haben, können Sie das Schlichtungsverfahren beim Sozialministeriumservice in Anspruch nehmen: <a href="https://www.sozialministeriumservice.at" rel="noopener noreferrer" target="_blank">www.sozialministeriumservice.at</a></p>

      <p style="margin-top:2rem"><a href="{{ route('home') }}">&larr; Zurück zur Startseite</a></p>
    </div>
  </div>
</div>
@endsection
