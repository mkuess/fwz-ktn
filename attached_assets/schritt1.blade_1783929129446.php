@extends('layouts.app')

@section('title', 'Verein registrieren – Schritt 1 – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="register-hero-strip"></div>
@endsection

@section('content')
<div class="container register-page">
  <div class="register-grid">
    @include('registrierung._sidebar')

    <div class="register-form-card">
      @include('registrierung._stepper', ['step' => 1])

      <span class="form-eyebrow">Schritt 1</span>
      <h2 class="h2" style="font-size:1.9rem;margin-bottom:6px;">Eure Organisation</h2>
      <p class="muted" style="margin-bottom:28px;">Erzählt uns, wer ihr seid — das wird später öffentlich auf eurem Profil sichtbar sein.</p>

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

      <form method="POST" action="{{ route('registrierung.schritt1.speichern') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <fieldset class="form-field" style="border:0;padding:0;margin:0 0 22px;">
          <legend style="font-weight:800;margin-bottom:10px;">Art der Organisation <span class="required">*</span></legend>
          <div class="type-choice">
            <label>
              <input type="radio" name="type" value="verein" {{ old('type', $daten['type'] ?? 'verein') === 'verein' ? 'checked' : '' }} required>
              Verein <span class="hint">Eingetragener Verein (ZVR)</span>
            </label>
            <label>
              <input type="radio" name="type" value="organisation" {{ old('type', $daten['type'] ?? '') === 'organisation' ? 'checked' : '' }}>
              Organisation <span class="hint">Institution / Initiative</span>
            </label>
          </div>
        </fieldset>

        <div class="form-row">
          <div class="form-field @error('zvr_number') has-error @enderror">
            <label for="zvr_number">Vereins-Nummer (ZVR)</label>
            <input type="text" id="zvr_number" name="zvr_number" value="{{ old('zvr_number', $daten['zvr_number'] ?? '') }}" placeholder="z. B. 1234567890">
            @error('zvr_number')<span class="field-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-field @error('name') has-error @enderror">
            <label for="name">Name der Organisation <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $daten['name'] ?? '') }}" placeholder="z. B. Bergrettung Klagenfurt" required>
            @error('name')<span class="field-error">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-field">
          <label for="logo">Bild / Logo <span class="hint" style="display:inline;">(JPG oder PNG, max. 5 MB)</span></label>
          <div class="file-drop">
            <span class="muted">Ziehe eine Datei hierher oder klicke zum Auswählen</span>
            <input type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png">
          </div>
          @error('logo')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-field @error('email') has-error @enderror">
          <label for="email">E-Mail-Adresse <span class="required">*</span></label>
          <input type="email" id="email" name="email" value="{{ old('email', $daten['email'] ?? '') }}" placeholder="kontakt@verein.at" required>
          @error('email')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
          <span></span>
          <span class="step-indicator">Schritt 1 / 3</span>
          <button class="btn dark" type="submit">Weiter <span class="arrow">→</span></button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
