@extends('layouts.app')

@section('title', 'Verein registrieren – Schritt 2 – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="register-hero-strip"></div>
@endsection

@section('content')
<div class="container register-page">
  <div class="register-grid">
    @include('registrierung._sidebar')

    <div class="register-form-card">
      @include('registrierung._stepper', ['step' => 2])

      <span class="form-eyebrow">Schritt 2</span>
      <h2 class="h2" style="font-size:1.9rem;margin-bottom:6px;">Benutzerkonto erstellen</h2>
      <p class="muted" style="margin-bottom:28px;">Damit ihr euer Vereinsprofil später bearbeiten und Gesuche verwalten könnt.</p>

      @if ($errors->any())
        <div class="note-box" role="alert" style="border-color:#c0392b;background:#fdecea;">
          <strong style="color:#c0392b;">Bitte prüft eure Eingaben:</strong>
          <ul style="margin-top:6px;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('registrierung.schritt2.speichern') }}" novalidate>
        @csrf

        <div class="form-row">
          <div class="form-field @error('password') has-error @enderror">
            <label for="password">Passwort <span class="required">*</span></label>
            <input type="password" id="password" name="password" placeholder="Mindestens 8 Zeichen" required minlength="8" autocomplete="new-password">
            @error('password')<span class="field-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-field">
            <label for="password_confirmation">Passwort wiederholen <span class="required">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Passwort wiederholen" required autocomplete="new-password">
          </div>
        </div>

        <div class="form-field @error('description') has-error @enderror">
          <label for="description">Kurzbeschreibung eurer Organisation <span class="required">*</span></label>
          <textarea id="description" name="description" placeholder="Was macht ihr? Was ist eure Mission? Welche Freiwilligen sucht ihr?" required>{{ old('description', $daten['description'] ?? '') }}</textarea>
          @error('description')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        {{-- Bewusst NICHT vorangehakt: eine gültige DSGVO-Einwilligung erfordert eine
             aktive, unmissverständliche Handlung der Person (Art. 4 Nr. 11 DSGVO). --}}
        <label class="checkbox-field" for="newsletter_optin">
          <input type="checkbox" id="newsletter_optin" name="newsletter_optin" value="1" {{ old('newsletter_optin', $daten['newsletter_optin'] ?? false) ? 'checked' : '' }}>
          <span>Ich bin mit der Zusendung von <strong>News und Informationen</strong> zum Freiwilligenwesen in Kärnten einverstanden. Abmeldung jederzeit möglich.</span>
        </label>

        <div class="form-actions">
          <a class="btn light" style="color:var(--ink);border-color:var(--line);" href="{{ route('registrierung.schritt1') }}">← Zurück</a>
          <span class="step-indicator">Schritt 2 / 3</span>
          <button class="btn dark" type="submit">Weiter <span class="arrow">→</span></button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
