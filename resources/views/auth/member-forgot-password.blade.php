@extends('layouts.app')

@section('title', 'Passwort vergessen – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">MITGLIEDERBEREICH</span>
    <h1 class="h2">Passwort zurücksetzen</h1>
    <p>Gib deine E-Mail-Adresse ein und wir senden dir einen Link.</p>
  </div>
</div>
@endsection

@section('content')
<section class="section" style="min-height:50vh">
  <div class="container" style="max-width:480px;margin:0 auto">

    <div class="box" style="padding:2rem 2.5rem">

      @if(session('status'))
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:0.5rem;padding:0.875rem 1rem;margin-bottom:1.5rem;font-size:0.875rem;color:#166534">
          {{ session('status') }}
        </div>
      @endif

      @if(session('reset_link'))
        <div style="background:#fef9ec;border:1px solid var(--yellow);border-radius:0.5rem;padding:0.875rem 1rem;margin-bottom:1.5rem;font-size:0.8rem;color:#374151;word-break:break-all">
          <strong>🔧 Entwicklermodus:</strong> Aktivierungslink:<br>
          <a href="{{ session('reset_link') }}" style="color:var(--yellow)">{{ session('reset_link') }}</a>
        </div>
      @endif

      <form method="POST" action="{{ route('member.forgot.post') }}">
        @csrf

        <div style="margin-bottom:1.25rem">
          <label for="email" style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.375rem;color:#374151">E-Mail-Adresse</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" required
            style="width:100%;padding:0.625rem 0.875rem;border:1.5px solid {{ $errors->has('email') ? '#ef4444' : '#d1d5db' }};border-radius:0.5rem;font-size:1rem;box-sizing:border-box;outline:none">
          @error('email')
            <p style="color:#ef4444;font-size:0.8rem;margin:0.375rem 0 0">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit" class="btn-cta" style="width:100%;border:none;cursor:pointer;font-family:inherit">
          Link zusenden →
        </button>

      </form>

      <div style="margin-top:1.5rem;text-align:center">
        <a href="{{ route('member.login') }}" style="font-size:0.875rem;color:var(--yellow)">← Zurück zur Anmeldung</a>
      </div>

    </div>
  </div>
</section>
@endsection
