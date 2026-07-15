@extends('layouts.app')

@section('title', 'Konto aktivieren – FWZ Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">WILLKOMMEN</span>
    <h1 class="h2">Passwort erstellen</h1>
    <p>Fast geschafft! Wähle jetzt dein persönliches Passwort.</p>
  </div>
</div>
@endsection

@section('content')
<section class="section" style="min-height:50vh">
  <div class="container" style="max-width:480px;margin:0 auto">

    <div class="box" style="padding:2rem 2.5rem">

      <h2 style="font-size:1.25rem;font-weight:700;color:#1a2e1a;margin:0 0 0.375rem">
        Willkommen, {{ $member->first_name }}!
      </h2>
      <p style="color:#6b7280;margin:0 0 1.75rem;font-size:0.9rem">
        Erstelle jetzt dein Passwort, um dich einzuloggen und deine Benefits zu sehen.
      </p>

      @if($member->membership_number)
      <div style="background:#f9fafb;border:1px solid var(--line);border-radius:0.5rem;padding:0.75rem 1rem;margin-bottom:1.75rem;font-size:0.875rem;color:#374151">
        🪪 Deine Mitgliedsnummer: <strong style="font-family:monospace;letter-spacing:0.05em">{{ $member->membership_number }}</strong>
      </div>
      @endif

      <form method="POST" action="{{ route('member.activate.post', $token) }}">
        @csrf

        <div style="margin-bottom:1.25rem">
          <label for="password" style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.375rem;color:#374151">Passwort <span style="color:#9ca3af;font-weight:400">(mind. 8 Zeichen)</span></label>
          <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password"
            style="width:100%;padding:0.625rem 0.875rem;border:1.5px solid {{ $errors->has('password') ? '#ef4444' : '#d1d5db' }};border-radius:0.5rem;font-size:1rem;box-sizing:border-box;outline:none">
          @error('password')
            <p style="color:#ef4444;font-size:0.8rem;margin:0.375rem 0 0">{{ $message }}</p>
          @enderror
        </div>

        <div style="margin-bottom:1.75rem">
          <label for="password_confirmation" style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.375rem;color:#374151">Passwort bestätigen</label>
          <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
            style="width:100%;padding:0.625rem 0.875rem;border:1.5px solid #d1d5db;border-radius:0.5rem;font-size:1rem;box-sizing:border-box;outline:none">
        </div>

        <button type="submit" class="btn-cta" style="width:100%;border:none;cursor:pointer;font-family:inherit">
          Passwort erstellen &amp; einloggen →
        </button>

      </form>

    </div>
  </div>
</section>
@endsection
