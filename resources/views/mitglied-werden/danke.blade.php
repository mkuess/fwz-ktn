@extends('layouts.app')

@section('title', 'Anmeldung erfolgreich | FWZ Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">Vielen Dank</span>
    <h1 class="h2">Anmeldung erfolgreich!</h1>
  </div>
</div>
@endsection

@section('content')
<section class="reg-section">
  <div class="container">
    <div class="box" style="max-width:600px;margin:0 auto;padding:2.5rem;text-align:center">
      <div style="font-size:3rem;margin-bottom:1rem">✅</div>
      <h2 class="h3" style="margin-bottom:1rem">Vielen Dank für deine Anmeldung!</h2>
      <p style="margin-bottom:1.5rem;color:#4b5563;line-height:1.7">
        Wir haben deine Anfrage erhalten und werden sie so bald wie möglich prüfen.
        Du erhältst eine Bestätigung per E-Mail, sobald dein Konto freigeschaltet wurde.
      </p>
      <a class="btn primary" href="{{ route('home') }}">Zurück zur Startseite <span class="arrow">→</span></a>
    </div>
  </div>
</section>
@endsection
