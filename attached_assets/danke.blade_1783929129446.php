@extends('layouts.app')

@section('title', 'Danke für eure Registrierung – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="register-hero-strip"></div>
@endsection

@section('content')
<div class="container register-page">
  <div class="box register-danke" style="max-width:640px;margin:0 auto;">
    <div class="check-big" aria-hidden="true">✓</div>
    <h1 class="h2" style="margin-bottom:14px;">Danke für eure Registrierung!</h1>
    <p class="lead" style="margin-bottom:10px;">
      Das FWZ-Team prüft eure Angaben in Kürze. Sobald euer Verein freigeschaltet ist, erhaltet ihr eine
      Bestätigung per E-Mail mit eurem persönlichen Vereinscode.
    </p>
    <p class="muted">Fragen in der Zwischenzeit? Schreibt uns an
      <a href="mailto:office@freiwilligenzentrum-kaernten.at">office@freiwilligenzentrum-kaernten.at</a>.
    </p>
    <p style="margin-top:28px;"><a class="btn dark" href="{{ route('home') }}">Zurück zur Startseite</a></p>
  </div>
</div>
@endsection
