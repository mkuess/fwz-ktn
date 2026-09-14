@extends('layouts.app')

@section('title', 'Daten ändern – Freiwilligenzentrum Kärnten')

@push('styles')
<style>
.profile-form{max-width:720px;margin:0 auto}
.profile-form-grid{display:grid;grid-template-columns:1fr;gap:1rem}
.profile-form .form-group{margin:0}
.profile-form .form-control[readonly]{background:#f3f4f6;color:#4b5563;cursor:not-allowed}
.profile-actions{display:flex;flex-wrap:wrap;gap:0.75rem;margin-top:1.5rem}
@media(min-width:640px){.profile-form-grid{grid-template-columns:1fr 1fr}.profile-form-grid .full-width{grid-column:1/-1}}
</style>
@endpush

@section('hero')
<div class="page-hero" style="background:#1a2e1a">
  <div class="container">
    <span class="eyebrow">MITGLIEDERBEREICH</span>
    <h1 class="h2">Daten ändern</h1>
    <p>Aktualisiere deinen Namen, deine Adresse oder dein Passwort.</p>
  </div>
</div>
@endsection

@section('content')
<section class="section">
  <div class="container">
    <form method="POST" action="{{ route('member.profile.update') }}" class="profile-form box" style="padding:1.5rem">
      @csrf
      @method('PATCH')

      <div class="profile-form-grid">
        <div class="form-group">
          <label class="form-label" for="first_name">Vorname <span class="req">*</span></label>
          <input class="form-control @error('first_name') is-error @enderror" id="first_name" name="first_name" type="text" value="{{ old('first_name', $member->first_name) }}" maxlength="255" required autocomplete="given-name">
          @error('first_name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="last_name">Nachname <span class="req">*</span></label>
          <input class="form-control @error('last_name') is-error @enderror" id="last_name" name="last_name" type="text" value="{{ old('last_name', $member->last_name) }}" maxlength="255" required autocomplete="family-name">
          @error('last_name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group full-width">
          <label class="form-label" for="organisation">Verein / Organisation</label>
          <input class="form-control" id="organisation" type="text" value="{{ $member->organisation?->name ?? 'Keinem Verein zugeordnet' }}" readonly>
          <div style="font-size:0.8rem;color:#6b7280;margin-top:0.35rem">Die Vereinszugehörigkeit kann nur durch das Freiwilligenzentrum geändert werden.</div>
        </div>

        <div class="form-group full-width">
          <label class="form-label" for="street">Straße und Hausnummer <span class="req">*</span></label>
          <input class="form-control @error('street') is-error @enderror" id="street" name="street" type="text" value="{{ old('street', $member->street) }}" maxlength="255" required autocomplete="street-address">
          @error('street')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="zip">PLZ <span class="req">*</span></label>
          <input class="form-control @error('zip') is-error @enderror" id="zip" name="zip" type="text" value="{{ old('zip', $member->zip) }}" maxlength="4" inputmode="numeric" required autocomplete="postal-code">
          @error('zip')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="city">Ort <span class="req">*</span></label>
          <input class="form-control @error('city') is-error @enderror" id="city" name="city" type="text" value="{{ old('city', $member->city) }}" maxlength="255" required autocomplete="address-level2">
          @error('city')<div class="form-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <hr style="border:0;border-top:1px solid #e5e7eb;margin:2rem 0">

      <h2 style="font-size:1.2rem;margin:0 0 0.4rem">Passwort ändern</h2>
      <p style="color:#6b7280;margin:0 0 1.25rem;font-size:0.9rem">Optional: Lass diese Felder leer, wenn du dein Passwort nicht ändern möchtest.</p>

      <div class="profile-form-grid">
        <div class="form-group full-width">
          <label class="form-label" for="current_password">Aktuelles Passwort</label>
          <input class="form-control @error('current_password') is-error @enderror" id="current_password" name="current_password" type="password" autocomplete="current-password">
          @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Neues Passwort</label>
          <input class="form-control @error('password') is-error @enderror" id="password" name="password" type="password" minlength="8" autocomplete="new-password">
          @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="password_confirmation">Neues Passwort bestätigen</label>
          <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password">
        </div>
      </div>

      <div class="profile-actions">
        <button type="submit" class="btn btn-primary">Änderungen speichern</button>
        <a href="{{ route('member.portal') }}" class="btn btn-outline">Abbrechen</a>
      </div>
    </form>
  </div>
</section>
@endsection