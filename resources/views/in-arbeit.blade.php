@extends('layouts.app')

@section('title', 'In Arbeit – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">Demnächst</span>
    <h2 class="h2">In Arbeit 🔧</h2>
    <p>Dieser Bereich wird gerade aufgebaut — bald ist er verfügbar.</p>
  </div>
</div>
@endsection

@section('content')
<div class="legal-content">
  <div class="container">
    <div class="box" style="text-align:center;padding:3rem">
      <p style="margin-bottom:2rem;color:var(--text)">Wir arbeiten daran. Schau bald wieder vorbei!</p>
      <a class="btn dark" href="{{ route('home') }}">&larr; Zurück zur Startseite</a>
    </div>
  </div>
</div>
@endsection
