@extends('layouts.app')

@section('title', 'Barrierefreiheit – Freiwilligenzentrum Kärnten')
@section('description', 'Erklärung zur Barrierefreiheit des Freiwilligenzentrums Kärnten gemäß WZG / EU-Richtlinie 2016/2102.')

@section('content')
<div class="legal-page">
  <div class="container">
    <div class="box">
      <p class="placeholder-hint">⚠️ Platzhalter — Konformitätsstatus und geprüfte Details vor Go-Live eintragen.</p>
      <h1>Erklärung zur Barrierefreiheit</h1>
      <p>Das Freiwilligenzentrum Kärnten ist bemüht, seine Website gemäß dem Bundes-Behindertengleichstellungsgesetz (BGStG) und der EU-Richtlinie 2016/2102 barrierefrei zugänglich zu machen.</p>

      <h2>Stand der Vereinbarkeit</h2>
      <p>Diese Website ist <strong>[vollständig / teilweise / nicht]</strong> vereinbar mit den WCAG 2.1 Richtlinien, Konformitätsstufe AA. [Bekannte Ausnahmen hier beschreiben.]</p>

      <h2>Was wir umgesetzt haben</h2>
      <ul>
        <li>Semantisches HTML5 mit korrekter Überschriftenhierarchie</li>
        <li>Skip-Link „Zum Hauptinhalt springen" auf jeder Seite</li>
        <li>Sichtbare Fokus-Zustände für Tastaturnavigation</li>
        <li>Alternativtexte für alle informationellen Bilder</li>
        <li>Formulare mit zugeordneten Labels (auch visuell versteckt)</li>
        <li>Mindestgröße 44×44 px für Bedienelemente</li>
        <li><code>prefers-reduced-motion</code> wird respektiert</li>
        <li>Cookie-Banner und -Dialog per Tastatur bedienbar mit Fokus-Trap</li>
      </ul>

      <h2>Feedback und Kontakt</h2>
      <p>Haben Sie Barrieren festgestellt oder Verbesserungsvorschläge? Bitte melden Sie sich bei: <a href="mailto:office@freiwilligenzentrum-kaernten.at">office@freiwilligenzentrum-kaernten.at</a></p>

      <h2>Schlichtungsverfahren</h2>
      <p>Wenn Sie nach einer Kontaktaufnahme mit uns keine zufriedenstellende Antwort erhalten haben, können Sie das Schlichtungsverfahren beim Sozialministeriumservice in Anspruch nehmen: <a href="https://www.sozialministeriumservice.at" rel="noopener noreferrer" target="_blank">www.sozialministeriumservice.at</a></p>

      <p style="margin-top:2rem"><a href="{{ url('/') }}">&larr; Zurück zur Startseite</a></p>
    </div>
  </div>
</div>
@endsection
