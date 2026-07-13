@extends('layouts.app')

@section('title', 'Verein registrieren – Schritt 3 | FWZ Kärnten')
@section('meta_description', 'Standort und Kontaktdaten eurer Organisation beim FWZ Kärnten eintragen.')

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
        <h2 class="h3" style="margin-bottom:24px">Schritt 3: Standort & Kontakt</h2>

        @if($errors->any())
          <div class="reg-errors" role="alert">
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('registrierung.schritt3.post') }}" method="POST" novalidate>
          @csrf

          <div class="form-row">
            <div class="form-group" style="flex:2">
              <label class="form-label" for="street">Straße & Hausnummer</label>
              <input class="form-control" type="text" id="street" name="street"
                value="{{ old('street', $old['street'] ?? '') }}" autocomplete="street-address">
            </div>
            <div class="form-group" style="flex:0 0 120px">
              <label class="form-label" for="zip">PLZ</label>
              <input class="form-control" type="text" id="zip" name="zip"
                value="{{ old('zip', $old['zip'] ?? '') }}" maxlength="10" autocomplete="postal-code">
            </div>
            <div class="form-group" style="flex:1">
              <label class="form-label" for="city">Ort</label>
              <input class="form-control" type="text" id="city" name="city"
                value="{{ old('city', $old['city'] ?? '') }}" autocomplete="address-level2">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="phone">Telefonnummer</label>
              <input class="form-control" type="tel" id="phone" name="phone"
                value="{{ old('phone', $old['phone'] ?? '') }}" autocomplete="tel">
            </div>
            <div class="form-group">
              <label class="form-label" for="website">Website</label>
              <input class="form-control @error('website') is-error @enderror" type="url" id="website" name="website"
                value="{{ old('website', $old['website'] ?? '') }}" placeholder="https://...">
              @error('website')<p class="form-error">{{ $message }}</p>@enderror
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="representative">Verantwortliche Person / Obmann·Obfrau</label>
            <input class="form-control" type="text" id="representative" name="representative"
              value="{{ old('representative', $old['representative'] ?? '') }}">
          </div>

          <div class="form-group">
            <label class="form-label" for="contact_person">Ansprechperson für das FWZ-Team</label>
            <label class="form-check" style="margin-bottom:10px">
              <input type="checkbox" id="same-contact" name="same_contact" value="1">
              <span>Identisch mit verantwortlicher Person</span>
            </label>
            <input class="form-control" type="text" id="contact_person" name="contact_person"
              value="{{ old('contact_person', $old['contact_person'] ?? '') }}">
          </div>

          <div class="form-actions">
            <a class="btn light-outline" href="{{ route('registrierung.schritt2') }}">← Zurück</a>
            <button class="btn primary" type="submit">Registrierung abschließen <span class="arrow">→</span></button>
          </div>
        </form>
      </div>

      @include('registrierung._sidebar')
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
  var checkbox = document.getElementById('same-contact');
  var representative = document.getElementById('representative');
  var contactPerson = document.getElementById('contact_person');

  function syncContact() {
    if (checkbox.checked) {
      contactPerson.value = representative.value;
      contactPerson.disabled = true;
      contactPerson.style.opacity = '.6';
    } else {
      contactPerson.disabled = false;
      contactPerson.style.opacity = '';
    }
  }

  checkbox.addEventListener('change', syncContact);
  representative.addEventListener('input', function () {
    if (checkbox.checked) contactPerson.value = representative.value;
  });

  // Re-enable before submit so the value is sent
  contactPerson.closest('form').addEventListener('submit', function () {
    contactPerson.disabled = false;
  });
})();
</script>
@endpush
