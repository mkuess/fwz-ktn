@extends('layouts.app')

@section('title', 'Code eingeben – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">MITGLIEDERBEREICH</span>
    <h1 class="h2">Code eingeben</h1>
    <p>Wir haben dir einen sechsstelligen Code per E-Mail gesendet.</p>
  </div>
</div>
@endsection

@section('content')
<section class="section" style="min-height:50vh">
  <div class="container" style="max-width:480px;margin:0 auto">
    <div class="box" style="padding:2rem 2.5rem">
      @if(session('status'))
        <div role="status" style="background:#f0fdf4;border:1px solid #86efac;border-radius:0.5rem;padding:0.875rem 1rem;margin-bottom:1.5rem;font-size:0.875rem;color:#166534">
          {{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ route('member.reset.code.post') }}">
        @csrf
        <div style="margin-bottom:1.25rem">
          <label for="code" style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.375rem;color:#374151">Sechsstelliger Code</label>
          <input type="text" id="code" name="code" value="{{ old('code') }}" required autofocus
            inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6"
            aria-describedby="code-hint{{ $errors->has('code') ? ' code-error' : '' }}"
            style="width:100%;padding:0.75rem 0.875rem;border:1.5px solid {{ $errors->has('code') ? '#ef4444' : '#d1d5db' }};border-radius:0.5rem;font-family:monospace;font-size:1.5rem;letter-spacing:0.2em;text-align:center;box-sizing:border-box">
          <p id="code-hint" style="font-size:0.8rem;color:#6b7280;margin:0.5rem 0 0">Der Code ist 10 Minuten gültig.</p>
          @error('code')
            <p id="code-error" role="alert" style="color:#b91c1c;font-size:0.8rem;margin:0.375rem 0 0">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit" class="btn-cta" style="width:100%;border:none;cursor:pointer;font-family:inherit">
          Code prüfen →
        </button>
      </form>

      <div style="margin-top:1.5rem;text-align:center">
        <a href="{{ route('member.forgot') }}" style="font-size:0.875rem;color:var(--yellow)">Neuen Code anfordern</a>
      </div>
    </div>
  </div>
</section>
@endsection