@extends('layouts.app')

@section('title', 'Mitglied werden | FWZ Kärnten')
@section('meta_description', 'Werde Mitglied bei einem Verein oder einer Organisation im Freiwilligenzentrum Kärnten.')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">Ehrenamt</span>
    <h1 class="h2">Mitglied werden.</h1>
    <p>Melde dich bei einer Organisation deiner Wahl an und werde Teil der Freiwilligengemeinschaft in Kärnten.</p>
  </div>
</div>
@endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
  .ts-control { border-radius: 0.375rem !important; border-color: #d1d5db !important; min-height: 42px; }
  .ts-control:focus-within { border-color: #c9a227 !important; box-shadow: 0 0 0 2px rgba(201,162,39,0.2) !important; outline: none; }
  .ts-dropdown .active { background-color: #c9a227 !important; color: #fff !important; }
  .ts-dropdown-content .option:hover { background-color: #f5e9c0; }
  .ts-wrapper.is-error .ts-control { border-color: #ef4444 !important; }
  .ts-wrapper { position: relative; }
  .ts-dropdown { z-index: 9999 !important; position: absolute !important; top: 100% !important; left: 0 !important; right: 0 !important; }
  form, .form-container, .form-group, .reg-main, .reg-section, .container { overflow: visible !important; }
</style>

<section class="reg-section">
  <div class="container">
    <div class="reg-layout">
      <div class="reg-main box">
        <h2 class="h3" style="margin-bottom:24px">Deine Anmeldung</h2>

        @if($errors->any())
          <div class="reg-errors" role="alert">
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('member.register.store') }}" method="POST" novalidate>
          @csrf

          <div class="form-group">
            <label class="form-label" for="organisation_id">Organisation <span class="req">*</span></label>
            <select class="form-control @error('organisation_id') is-error @enderror" id="organisation_id" name="organisation_id" required>
              <option value="" disabled selected>Organisation auswählen...</option>
              @foreach($organisations as $id => $name)
                <option value="{{ $id }}" {{ old('organisation_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
            @error('organisation_id')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label class="form-label" for="first_name">Vorname <span class="req">*</span></label>
              <input class="form-control @error('first_name') is-error @enderror" type="text" id="first_name" name="first_name"
                value="{{ old('first_name') }}" required autocomplete="given-name">
              @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
              <label class="form-label" for="last_name">Nachname <span class="req">*</span></label>
              <input class="form-control @error('last_name') is-error @enderror" type="text" id="last_name" name="last_name"
                value="{{ old('last_name') }}" required autocomplete="family-name">
              @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="email">E-Mail-Adresse <span class="req">*</span></label>
            <input class="form-control @error('email') is-error @enderror" type="email" id="email" name="email"
              value="{{ old('email') }}" required autocomplete="email">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="street">Straße &amp; Hausnummer <span class="form-hint">(optional)</span></label>
            <input class="form-control @error('street') is-error @enderror" type="text" id="street" name="street"
              value="{{ old('street') }}" autocomplete="street-address">
            @error('street')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-row" style="display:grid;grid-template-columns:140px 1fr;gap:1rem">
            <div class="form-group">
              <label class="form-label" for="zip">PLZ <span class="form-hint">(optional)</span></label>
              <input class="form-control @error('zip') is-error @enderror" type="text" id="zip" name="zip"
                value="{{ old('zip') }}" maxlength="10" autocomplete="postal-code">
              @error('zip')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
              <label class="form-label" for="city">Ort <span class="form-hint">(optional)</span></label>
              <input class="form-control @error('city') is-error @enderror" type="text" id="city" name="city"
                value="{{ old('city') }}" autocomplete="address-level2">
              @error('city')<p class="form-error">{{ $message }}</p>@enderror
            </div>
          </div>

          <div class="form-group">
            <label class="form-check">
              <input type="checkbox" name="newsletter_optin" value="1" {{ old('newsletter_optin') ? 'checked' : '' }}>
              <span>Ich möchte den Newsletter des FWZ Kärnten erhalten</span>
            </label>
            @error('newsletter_optin')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-group">
            <div class="form-check">
              <input class="form-check-input @error('confirm_membership') is-invalid @enderror"
                     type="checkbox"
                     name="confirm_membership"
                     id="confirm_membership"
                     value="1"
                     {{ old('confirm_membership') ? 'checked' : '' }}
                     required>
              <label class="form-check-label" for="confirm_membership">
                Ich bestätige, dass ich Mitglied der ausgewählten Organisation bin und dass alle von mir gemachten Angaben der Wahrheit entsprechen. <span class="req">*</span>
              </label>
              @error('confirm_membership')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="form-group">
            <div class="form-check">
              <input class="form-check-input @error('confirm_privacy') is-invalid @enderror"
                     type="checkbox"
                     name="confirm_privacy"
                     id="confirm_privacy"
                     value="1"
                     {{ old('confirm_privacy') ? 'checked' : '' }}
                     required>
              <label class="form-check-label" for="confirm_privacy">
                Ich stimme zu, dass das Freiwilligenzentrum Kärnten meine personenbezogenen Daten (Name, E-Mail-Adresse, Adresse) zum Zweck der Mitgliedsverwaltung und Ausstellung einer Mitgliedskarte gemäß der <a href="/datenschutz" target="_blank">Datenschutzerklärung</a> verarbeitet und speichert. Die Einwilligung kann jederzeit durch eine E-Mail an <a href="mailto:office@fwz-ktn.at">office@fwz-ktn.at</a> widerrufen werden. <span class="req">*</span>
              </label>
              @error('confirm_privacy')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <p style="font-size:0.8rem;color:#6b7280;margin-top:1rem">
            * Pflichtfeld. Ihre Daten werden ausschließlich für die Verwaltung Ihrer Mitgliedschaft beim Freiwilligenzentrum Kärnten verwendet und nicht an Dritte weitergegeben. Weitere Informationen entnehmen Sie unserer <a href="/datenschutz">Datenschutzerklärung</a>.
          </p>

          <div class="form-actions">
            <button class="btn primary" type="submit">Jetzt anmelden <span class="arrow">→</span></button>
          </div>
        </form>
      </div>

      <aside class="reg-sidebar">
        <div class="box" style="padding:1.5rem">
          <h3 class="h4" style="margin-bottom:1rem">Was passiert danach?</h3>
          <ol style="padding-left:1.25rem;line-height:1.8">
            <li>Deine Anfrage wird geprüft</li>
            <li>Die Organisation bestätigt deine Mitgliedschaft</li>
            <li>Du erhältst eine Bestätigung per E-Mail</li>
            <li>Du kannst dich mit deiner E-Mail-Adresse anmelden</li>
          </ol>
        </div>
        <div class="box" style="padding:1.5rem;margin-top:1rem">
          <h3 class="h4" style="margin-bottom:0.5rem">Du möchtest einen Verein anmelden?</h3>
          <p style="font-size:0.9rem;margin-bottom:1rem">Als Organisation kannst du dich ebenfalls beim FWZ Kärnten registrieren.</p>
          <a class="btn dark" style="width:100%;text-align:center" href="{{ route('registrierung.schritt1') }}">Verein anmelden <span class="arrow">→</span></a>
        </div>
      </aside>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
  new TomSelect('#organisation_id', {
    placeholder: 'Organisation suchen...',
    searchField: ['text'],
    maxOptions: 50,
    create: false,
    allowEmptyOption: false,
    closeAfterSelect: true,
    render: {
      no_results: function() {
        return '<div class="no-results" style="padding:8px 12px">Keine Organisation gefunden</div>';
      }
    }
  });
</script>
@endpush
