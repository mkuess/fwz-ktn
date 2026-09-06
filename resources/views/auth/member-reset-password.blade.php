@extends('layouts.app')

@section('title', 'Neues Passwort – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">MITGLIEDERBEREICH</span>
    <h1 class="h2">Neues Passwort wählen</h1>
    <p>Lege jetzt dein neues Passwort fest.</p>
  </div>
</div>
@endsection

@section('content')
<section class="section" style="min-height:50vh">
  <div class="container" style="max-width:480px;margin:0 auto">
    <div class="box" style="padding:2rem 2.5rem">
      <form method="POST" action="{{ route('member.reset.password.post') }}">
        @csrf
        <input type="email" name="reset_email" value="{{ session('member_password_reset.email') }}"
          autocomplete="username" tabindex="-1" aria-hidden="true"
          style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0">

        <div style="margin-bottom:1.25rem">
          <label for="password" style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.375rem;color:#374151">Neues Passwort</label>
          <input type="password" id="password" name="password" required autofocus autocomplete="new-password" minlength="8"
            aria-describedby="password-hint{{ $errors->has('password') ? ' password-error' : '' }}"
            style="width:100%;padding:0.625rem 0.875rem;border:1.5px solid {{ $errors->has('password') ? '#ef4444' : '#d1d5db' }};border-radius:0.5rem;font-size:1rem;box-sizing:border-box">
          <p id="password-hint" style="font-size:0.8rem;color:#6b7280;margin:0.5rem 0 0">Mindestens 8 Zeichen.</p>
          @error('password')
            <p id="password-error" role="alert" style="color:#b91c1c;font-size:0.8rem;margin:0.375rem 0 0">{{ $message }}</p>
          @enderror
        </div>

        <div style="margin-bottom:1.5rem">
          <label for="password_confirmation" style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.375rem;color:#374151">Passwort bestätigen</label>
          <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" minlength="8"
            style="width:100%;padding:0.625rem 0.875rem;border:1.5px solid #d1d5db;border-radius:0.5rem;font-size:1rem;box-sizing:border-box">
        </div>

        <button type="submit" class="btn-cta" style="width:100%;border:none;cursor:pointer;font-family:inherit">
          Passwort ändern →
        </button>
      </form>
    </div>
  </div>
</section>
@endsection