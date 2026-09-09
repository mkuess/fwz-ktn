@extends('layouts.app')

@section('title', 'Verein registrieren – Schritt 1 | FWZ Kärnten')
@section('meta_description', 'Registriere deinen Verein oder deine Organisation beim Freiwilligenzentrum Kärnten.')

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
        <h2 class="h3" style="margin-bottom:24px">Schritt 1: Angaben zur Organisation</h2>

        @if($errors->any())
          <div class="reg-errors" role="alert">
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form
          id="organisation-registration-step-one"
          action="{{ route('registrierung.schritt1.post') }}"
          method="POST"
          enctype="multipart/form-data"
          x-data="{ organisationType: @js(old('type', $old['type'] ?? 'verein')) }"
          novalidate>
          @csrf

          <div class="form-group">
            <label class="form-label" for="name">Name der Organisation / des Vereins <span class="req">*</span></label>
            <input class="form-control @error('name') is-error @enderror" type="text" id="name" name="name"
              value="{{ old('name', $old['name'] ?? '') }}" required autocomplete="organization">
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-group">
            <fieldset>
              <legend class="form-label">Typ <span class="req">*</span></legend>
              <div class="form-radios">
                <label class="form-radio">
                  <input type="radio" name="type" value="verein" x-model="organisationType"
                    {{ old('type', $old['type'] ?? 'verein') === 'verein' ? 'checked' : '' }}>
                  <span>Verein</span>
                </label>
                <label class="form-radio">
                  <input type="radio" name="type" value="organisation" x-model="organisationType"
                    {{ old('type', $old['type'] ?? '') === 'organisation' ? 'checked' : '' }}>
                  <span>Organisation / Initiative</span>
                </label>
              </div>
            </fieldset>
            @error('type')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-group" x-show="organisationType === 'verein'" x-cloak>
            <label class="form-label" for="zvr_number">ZVR-Nummer <span class="req">*</span></label>
            <input class="form-control @error('zvr_number') is-error @enderror" type="text" id="zvr_number" name="zvr_number"
              value="{{ old('zvr_number', $old['zvr_number'] ?? '') }}" placeholder="z. B. 123456789"
              :required="organisationType === 'verein'"
              :disabled="organisationType !== 'verein'">
            @error('zvr_number')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="description">Kurzbeschreibung <span class="req">*</span> <span class="form-hint">(max. 2000 Zeichen)</span></label>
            <textarea class="form-control @error('description') is-error @enderror" id="description" name="description"
              rows="4" placeholder="Wofür steht eure Organisation? Was macht ihr?" required>{{ old('description', $old['description'] ?? '') }}</textarea>
            @error('description')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="logo">Logo <span class="form-hint">(optional, JPG/PNG/SVG/WebP; große Fotos werden automatisch verkleinert)</span></label>
            <input class="form-control form-file @error('logo') is-error @enderror" type="file" id="logo" name="logo"
              accept=".jpg,.jpeg,.png,.webp,.svg,image/jpeg,image/png,image/webp,image/svg+xml"
              aria-describedby="logo-upload-status">
            <p id="logo-upload-status" class="form-hint" aria-live="polite" tabindex="-1"></p>
            @error('logo')<p class="form-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-actions">
            <button class="btn primary" type="submit">Weiter: Benutzerkonto <span class="arrow">→</span></button>
          </div>
        </form>
      </div>

      @include('registrierung._sidebar')
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/logo-upload.js') }}?v={{ filemtime(public_path('js/logo-upload.js')) }}" defer></script>
@endpush
