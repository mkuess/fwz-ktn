@extends('layouts.app')

@section('title', 'Verein registrieren – Schritt 2 | FWZ Kärnten')
@section('meta_description', 'Benutzerkonto für euren Verein beim Freiwilligenzentrum Kärnten anlegen.')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">Verein registrieren</span>
    <h1 class="h2">Organisation anmelden.</h1>
    <p>In 3 einfachen Schritten ins FWZ-Netzwerk aufgenommen werden.</p>
  </div>
</div>
@endsection

@section('content')
<section class="reg-section">
  <div class="container">
    @include('registrierung._stepper')

    <div class="reg-layout">
      <div class="reg-main box">
        <h2 class="h3" style="margin-bottom:24px">Schritt 2: Benutzerkonto</h2>

        @if($errors->any())
          <div class="reg-errors" role="alert">
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('registrierung.schritt2.post') }}" method="POST" novalidate>
          @csrf

          <div class="form-group">
            <label class="form-label" for="email">E-Mail-Adresse <span class="req">*</span></label>
            <input class="form-control @error('email') is-error @enderror" type="email" id="email" name="email"
              value="{{ old('email', $old['email'] ?? '') }}" required autocomplete="email">
            <p class="form-hint-text">Diese E-Mail-Adresse dient als Benutzername für euer Vereinskonto.</p>
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="password">Passwort <span class="req">*</span></label>
            <input class="form-control @error('password') is-error @enderror" type="password" id="password"
              name="password" required autocomplete="new-password" minlength="8">
            <p class="form-hint-text">Mindestens 8 Zeichen.</p>
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="password_confirmation">Passwort bestätigen <span class="req">*</span></label>
            <input class="form-control" type="password" id="password_confirmation"
              name="password_confirmation" required autocomplete="new-password">
          </div>

          <div class="form-group">
            <label class="form-check">
              <input type="checkbox" name="newsletter" value="1">
              <span>Ich möchte den FWZ-Newsletter erhalten.</span>
            </label>
          </div>

          <div class="form-actions">
            <a class="btn light-outline" href="{{ route('registrierung.schritt1') }}">← Zurück</a>
            <button class="btn primary" type="submit">Weiter: Standort & Kontakt <span class="arrow">→</span></button>
          </div>
        </form>
      </div>

      @include('registrierung._sidebar')
    </div>
  </div>
</section>
@endsection
