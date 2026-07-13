@extends('layouts.app')

@section('title', 'Verein registrieren – Schritt 3 – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="register-hero-strip"></div>
@endsection

@section('content')
<div class="container register-page">
  <div class="register-grid">
    @include('registrierung._sidebar')

    <div class="register-form-card">
      @include('registrierung._stepper', ['step' => 3])

      <span class="form-eyebrow">Schritt 3</span>
      <h2 class="h2" style="font-size:1.9rem;margin-bottom:6px;">Standort &amp; Ansprechperson</h2>
      <p class="muted" style="margin-bottom:28px;">Wo seid ihr zu Hause und wer ist die richtige Kontaktperson für Freiwillige?</p>

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

      <form method="POST" action="{{ route('registrierung.schritt3.speichern') }}" novalidate>
        @csrf

        <div class="form-field @error('street') has-error @enderror">
          <label for="street">Straße &amp; Hausnummer <span class="required">*</span></label>
          <input type="text" id="street" name="street" value="{{ old('street', $daten['street'] ?? '') }}" placeholder="z. B. Mießtaler Straße 1" required>
          @error('street')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-row">
          <div class="form-field @error('zip') has-error @enderror">
            <label for="zip">PLZ <span class="required">*</span></label>
            <input type="text" id="zip" name="zip" value="{{ old('zip', $daten['zip'] ?? '') }}" placeholder="9020" required>
            @error('zip')<span class="field-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-field @error('city') has-error @enderror">
            <label for="city">Ort <span class="required">*</span></label>
            <input type="text" id="city" name="city" value="{{ old('city', $daten['city'] ?? '') }}" placeholder="Klagenfurt am Wörthersee" required>
            @error('city')<span class="field-error">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-field @error('phone') has-error @enderror">
            <label for="phone">Telefonnummer</label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone', $daten['phone'] ?? '') }}" placeholder="+43 …">
            @error('phone')<span class="field-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-field @error('website') has-error @enderror">
            <label for="website">Webseite</label>
            <input type="url" id="website" name="website" value="{{ old('website', $daten['website'] ?? '') }}" placeholder="https://verein.at">
            @error('website')<span class="field-error">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-field @error('representative') has-error @enderror">
            <label for="representative">Vertretungsberechtigte Person <span class="required">*</span></label>
            <input type="text" id="representative" name="representative" value="{{ old('representative', $daten['representative'] ?? '') }}" placeholder="Vor- und Nachname" required>
            @error('representative')<span class="field-error">{{ $message }}</span>@enderror
          </div>
          <div class="form-field @error('contact_person') has-error @enderror" id="contact-person-field">
            <label for="contact_person">Ansprechpartner:in <span class="required">*</span></label>
            <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $daten['contact_person'] ?? '') }}" placeholder="Vor- und Nachname">
            @error('contact_person')<span class="field-error">{{ $message }}</span>@enderror
          </div>
        </div>

        <label class="checkbox-field" for="same_contact">
          <input type="checkbox" id="same_contact" name="same_contact" value="1" {{ old('same_contact') ? 'checked' : '' }}>
          <span>Vertretungsberechtigte Person ist gleichzeitig Ansprechpartner:in</span>
        </label>

        <div class="form-actions">
          <a class="btn light" style="color:var(--ink);border-color:var(--line);" href="{{ route('registrierung.schritt2') }}">← Zurück</a>
          <span class="step-indicator">Schritt 3 / 3</span>
          <button class="btn dark" type="submit">Registrierung abschließen <span class="arrow">→</span></button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Ergänzung: Wenn "gleichzeitig Ansprechpartner:in" angehakt ist, wird das
     Ansprechpartner:in-Feld ausgeblendet und serverseitig automatisch mit dem
     Namen der vertretungsberechtigten Person befüllt (siehe Controller). --}}
@push('scripts')
<script>
(function () {
  var same = document.getElementById('same_contact');
  var field = document.getElementById('contact-person-field');
  var input = document.getElementById('contact_person');
  var rep = document.getElementById('representative');
  if (!same || !field || !input) return;
  function sync() {
    if (same.checked) {
      field.style.display = 'none';
      input.required = false;
      input.value = rep.value;
    } else {
      field.style.display = '';
      input.required = true;
    }
  }
  same.addEventListener('change', sync);
  sync();
})();
</script>
@endpush
@endsection
