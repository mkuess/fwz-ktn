@extends('layouts.app')

@section('title', 'Konto aktivieren – FWZ Kärnten')

@section('content')
<section class="section" style="min-height:60vh;display:flex;align-items:center">
  <div class="container" style="max-width:560px;margin:0 auto">

    <div class="box" style="padding:2.5rem;text-align:center">

      <div style="width:72px;height:72px;border-radius:50%;background:var(--yellow);display:grid;place-items:center;font-size:2rem;color:#1a2e1a;font-weight:900;margin:0 auto 1.5rem">
        ✓
      </div>

      <h1 style="font-size:1.75rem;font-weight:800;color:#1a2e1a;margin:0 0 0.5rem">
        Willkommen, {{ $member->first_name }}!
      </h1>
      <p style="color:#6b7280;margin:0 0 2rem;font-size:1rem">
        Dein Konto wird aktiviert. Bitte vergib ein Passwort, um dich einzuloggen.
      </p>

      {{-- Mitgliedsnummer --}}
      @if($member->membership_number)
      <div style="background:#f9fafb;border:1px solid var(--line);border-radius:0.75rem;padding:1rem;margin-bottom:2rem;font-size:0.875rem;color:#374151">
        🪪 Deine Mitgliedsnummer: <strong style="font-family:monospace;letter-spacing:0.05em">{{ $member->membership_number }}</strong>
      </div>
      @endif

      {{-- Placeholder password form --}}
      <div style="background:#fef9ec;border:1px solid var(--yellow);border-radius:0.75rem;padding:1.25rem;margin-bottom:1.5rem;text-align:left;font-size:0.875rem;color:#374151">
        <strong>🔧 In Kürze verfügbar:</strong><br>
        Die Passwort-Erstellung wird in einem nächsten Update freigeschaltet.
        Bitte wende dich bis dahin an 
        <a href="mailto:office@freiwilligenzentrum-kaernten.at" style="color:var(--yellow)">office@freiwilligenzentrum-kaernten.at</a>.
      </div>

      <a href="/" class="btn-cta" style="display:inline-block;width:auto;padding:0.75rem 2rem">
        Zur Startseite →
      </a>

    </div>

  </div>
</section>
@endsection
