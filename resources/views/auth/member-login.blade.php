@extends('layouts.app')

@section('title', 'Anmelden – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">MITGLIEDERBEREICH</span>
    <h1 class="h2">Willkommen zurück</h1>
    <p>Melde dich mit deiner E-Mail-Adresse und deinem Passwort an.</p>
  </div>
</div>
@endsection

@section('content')
<section class="section" style="min-height:50vh">
  <div class="container" style="max-width:480px;margin:0 auto">

    <div class="box" style="padding:2rem 2.5rem">

      <div style="text-align:center;margin-bottom:1.75rem">
        <img src="{{ asset('img/fwz-logo-login.svg') }}" alt="FWZ Kärnten" style="height:48px;width:auto">
      </div>

      @if(session('status'))
        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:0.5rem;padding:0.875rem 1rem;margin-bottom:1.5rem;font-size:0.875rem;color:#166534">
          {{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ route('member.login.post') }}" novalidate>
        @csrf

        {{-- Email --}}
        <div style="margin-bottom:1.25rem">
          <label for="email" style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.375rem;color:#374151">E-Mail-Adresse</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
            style="width:100%;padding:0.625rem 0.875rem;border:1.5px solid {{ $errors->has('email') ? '#ef4444' : '#d1d5db' }};border-radius:0.5rem;font-size:1rem;box-sizing:border-box;outline:none">
          @error('email')
            <p style="color:#ef4444;font-size:0.8rem;margin:0.375rem 0 0">{{ $message }}</p>
          @enderror
        </div>

        {{-- Password --}}
        <div style="margin-bottom:1.25rem">
          <label for="password" style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.375rem;color:#374151">Passwort</label>
          <input type="password" id="password" name="password" required autocomplete="current-password"
            style="width:100%;padding:0.625rem 0.875rem;border:1.5px solid #d1d5db;border-radius:0.5rem;font-size:1rem;box-sizing:border-box;outline:none">
        </div>

        {{-- Remember --}}
        <div style="margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem">
          <input type="checkbox" id="remember" name="remember" style="width:1rem;height:1rem;accent-color:var(--yellow)">
          <label for="remember" style="font-size:0.875rem;color:#6b7280;cursor:pointer">Angemeldet bleiben</label>
        </div>

        <button type="submit" class="btn-cta" style="width:100%;border:none;cursor:pointer;font-family:inherit">
          Anmelden →
        </button>

      </form>

      <div style="margin-top:1.5rem;text-align:center;display:flex;flex-direction:column;gap:0.5rem">
        <a href="{{ route('member.forgot') }}" style="font-size:0.875rem;color:var(--yellow)">Passwort vergessen?</a>
        @if(\App\Models\Setting::enabled('member_registration_enabled'))
          <span style="font-size:0.875rem;color:#9ca3af">Noch kein Mitglied?
            <a href="{{ route('member.register') }}" style="color:var(--yellow);font-weight:600">Jetzt registrieren →</a>
          </span>
        @endif
      </div>

    </div>
  </div>
</section>
@endsection
