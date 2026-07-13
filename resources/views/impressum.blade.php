@extends('layouts.app')

@section('title', 'Impressum – Freiwilligenzentrum Kärnten')
@section('meta_description', 'Impressum des Freiwilligenzentrums Kärnten gemäß § 5 ECG und § 25 MedienG.')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">Rechtliches</span>
    <h2 class="h2">Impressum</h2>
    <p>Angaben gemäß § 5 ECG und § 25 MedienG</p>
  </div>
</div>
@endsection

@section('content')
<div class="legal-content">
  <div class="container">
    <div class="box">
      <div class="note-box">
        <strong>⚠️ Platzhalter</strong>
        Vor Go-Live mit echten Rechts- und Kontaktdaten befüllen. Alle <span class="placeholder-field">[…]</span>-Felder ersetzen.
      </div>

      <h2>Medieninhaber und Herausgeber</h2>
      <p>
        <span class="placeholder-field">[Name des Rechtsträgers / Vereins]</span><br>
        ZVR-Zahl: <span class="placeholder-field">[ZVR-Zahl]</span><br>
        <span class="placeholder-field">[Straße und Hausnummer]</span><br>
        <span class="placeholder-field">[PLZ] [Ort]</span><br>
        Österreich
      </p>

      <h2>Kontakt</h2>
      <p>
        Telefon: <span class="placeholder-field">[+43 …]</span><br>
        E-Mail: <span class="placeholder-field">[office@freiwilligenzentrum-kaernten.at]</span>
      </p>

      <h2>Zweck der Website</h2>
      <p>Das Freiwilligenzentrum Kärnten ist die zentrale Anlaufstelle des Landes Kärnten für ehrenamtliches Engagement. Diese Website dient der Information von Freiwilligen und Organisationen sowie der Vernetzung im Bereich des Ehrenamts.</p>

      <h2>Grundlegende Richtung</h2>
      <p>Informationsangebot zu freiwilligem Engagement, Vereinsverzeichnis und Benefits für Mitglieder des Freiwilligenzentrums Kärnten.</p>

      <h2>Haftungsausschluss</h2>
      <p>Trotz sorgfältiger inhaltlicher Kontrolle übernehmen wir keine Haftung für die Inhalte externer Links. Für den Inhalt der verlinkten Seiten sind ausschließlich deren Betreiber verantwortlich.</p>

      <p style="margin-top:2rem"><a href="{{ route('home') }}">&larr; Zurück zur Startseite</a></p>
    </div>
  </div>
</div>
@endsection
