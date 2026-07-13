@extends('layouts.app')

@section('title', 'Registrierung eingereicht | FWZ Kärnten')
@section('meta_description', 'Ihre Registrierung beim Freiwilligenzentrum Kärnten wurde erfolgreich eingereicht.')

@section('hero')
<div class="page-hero page-hero--success">
  <div class="container">
    <div class="danke-check" aria-hidden="true">✓</div>
    <h1 class="h2">Vielen Dank für eure Registrierung!</h1>
    <p>Wir haben eure Daten erhalten und werden sie in Kürze prüfen.<br>
       Nach der Freischaltung erhaltet ihr eine Bestätigungs-E-Mail.</p>
  </div>
</div>
@endsection

@section('content')
<section class="reg-section">
  <div class="container">
    <div class="danke-box box">
      <h2 class="h3" style="margin-bottom:18px">Was passiert als nächstes?</h2>
      <div class="danke-steps">
        <div class="danke-step">
          <div class="danke-step__num">1</div>
          <div>
            <strong>Prüfung durch das FWZ-Team</strong>
            <p>Wir überprüfen eure Angaben — das dauert in der Regel 1–3 Werktage.</p>
          </div>
        </div>
        <div class="danke-step">
          <div class="danke-step__num">2</div>
          <div>
            <strong>Freischaltung & Bestätigung</strong>
            <p>Ihr erhaltet eine E-Mail, sobald euer Verein im Verzeichnis sichtbar ist.</p>
          </div>
        </div>
        <div class="danke-step">
          <div class="danke-step__num">3</div>
          <div>
            <strong>Vereinscode & Benefits</strong>
            <p>Mit dem persönlichen Code können eure Mitglieder sofort alle Benefits nutzen.</p>
          </div>
        </div>
      </div>
      <div style="margin-top:32px;display:flex;gap:14px;flex-wrap:wrap">
        <a class="btn dark" href="{{ route('home') }}">Zurück zur Startseite <span class="arrow">→</span></a>
        <a class="btn light-outline" href="mailto:office@freiwilligenzentrum-kaernten.at">Bei Fragen kontaktieren</a>
      </div>
    </div>
  </div>
</section>
@endsection
