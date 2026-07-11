@extends('layouts.app')

@section('title', 'Impressum – Freiwilligenzentrum Kärnten')
@section('description', 'Impressum des Freiwilligenzentrums Kärnten gemäß § 5 ECG und § 25 MedienG.')

@section('content')
<div class="legal-page">
  <div class="container">
    <div class="box">
      <p class="placeholder-hint">⚠️ Platzhalter — vor Go-Live mit echten Rechts- und Kontaktdaten befüllen.</p>
      <h1>Impressum</h1>
      <p>Angaben gemäß § 5 ECG und § 25 MedienG</p>

      <h2>Medieninhaber und Herausgeber</h2>
      <p>
        [Name des Rechtsträgers / Vereins]<br>
        ZVR-Zahl: [ZVR-Zahl]<br>
        [Straße und Hausnummer]<br>
        [PLZ] [Ort]<br>
        Österreich
      </p>

      <h2>Kontakt</h2>
      <p>
        Telefon: [+43 …]<br>
        E-Mail: [office@freiwilligenzentrum-kaernten.at]
      </p>

      <h2>Zweck der Website</h2>
      <p>Das Freiwilligenzentrum Kärnten ist die zentrale Anlaufstelle des Landes Kärnten für ehrenamtliches Engagement. Diese Website dient der Information von Freiwilligen und Organisationen sowie der Vernetzung im Bereich des Ehrenamts.</p>

      <h2>Grundlegende Richtung</h2>
      <p>Informationsangebot zu freiwilligem Engagement, Vereinsverzeichnis und Benefits für Mitglieder des Freiwilligenzentrums Kärnten.</p>

      <h2>Haftungsausschluss</h2>
      <p>Trotz sorgfältiger inhaltlicher Kontrolle übernehmen wir keine Haftung für die Inhalte externer Links. Für den Inhalt der verlinkten Seiten sind ausschließlich deren Betreiber verantwortlich.</p>

      <p style="margin-top:2rem"><a href="{{ url('/') }}">&larr; Zurück zur Startseite</a></p>
    </div>
  </div>
</div>
@endsection
