@extends('layouts.app')

@section('title', 'Mein Bereich – Freiwilligenzentrum Kärnten')

@push('styles')
<style>
.portal-grid{display:grid;grid-template-columns:1fr;gap:1.5rem;max-width:600px;margin:0 auto}
@media(min-width:768px){.portal-grid{grid-template-columns:1fr 1fr;max-width:900px;gap:2.5rem}}
</style>
@endpush

@section('hero')
<div class="page-hero" style="background:#1a2e1a">
  <div class="container">
    <span class="eyebrow">MITGLIEDERBEREICH</span>
    <h1 class="h2">Mein Bereich</h1>
    <p>Deine digitale Mitgliedskarte und alle Benefits auf einen Blick.</p>
  </div>
</div>
@endsection

@section('content')

<section class="section">
  <div class="container">

    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:0.75rem;padding:1rem 1.5rem;margin-bottom:2rem;display:flex;align-items:center;gap:0.75rem">
      <span style="font-size:1.25rem">🎉</span>
      <p style="margin:0;font-size:0.9rem;color:#166534">{{ session('success') }}</p>
    </div>
    @endif

    <div class="portal-grid">

      {{-- Left: Membership card --}}
      <div>
        <h2 style="margin:0 0 1.25rem;font-size:1.1rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em">Deine Mitgliedskarte</h2>
        @include('partials.mitgliedskarte', ['member' => $member])
        <p style="margin-top:0.75rem;font-size:0.8rem;color:#9ca3af">
          Nach der Freischaltung erhältst du deine persönliche Karte mit Mitgliedsnummer.
        </p>
      </div>

      {{-- Right: Member info --}}
      <div>
        <h2 style="margin:0 0 1.25rem;font-size:1.1rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em">Meine Daten</h2>
        <div class="box" style="padding:1.5rem">
          <div style="margin-bottom:1rem">
            <div style="font-size:0.75rem;color:#9ca3af;margin-bottom:0.2rem">Name</div>
            <div style="font-weight:600">{{ $member->first_name }} {{ $member->last_name }}</div>
          </div>
          @if(isset($member->organisation) && $member->organisation)
          <div style="margin-bottom:1rem">
            <div style="font-size:0.75rem;color:#9ca3af;margin-bottom:0.2rem">Organisation</div>
            <div style="font-weight:600">{{ is_object($member->organisation) ? $member->organisation->name : $member->organisation }}</div>
          </div>
          @endif
          <div>
            <div style="font-size:0.75rem;color:#9ca3af;margin-bottom:0.2rem">Mitgliedsnummer</div>
            <div style="font-weight:600;font-family:monospace">{{ $member->membership_number ?? '– wird zugeteilt –' }}</div>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

{{-- Benefits section --}}
@if($benefits->count())
<section class="section" style="background:#f9fafb;padding-top:3rem;padding-bottom:3rem">
  <div class="container">
    <h2 style="margin:0 0 0.5rem">Meine Benefits</h2>
    <p style="color:#6b7280;margin:0 0 2rem">Exklusive Vorteile für freiwillig Engagierte in Kärnten.</p>
    <div class="benefit-grid">
      @foreach($benefits as $benefit)
        <a href="{{ route('member.benefit', $benefit->id) }}" style="text-decoration:none;color:inherit;display:block">
          <div class="benefit-card" style="cursor:pointer;height:100%">
            @if($benefit->logo_path)
              <img src="{{ Storage::url($benefit->logo_path) }}" alt="{{ $benefit->name }}" style="max-height:48px;object-fit:contain;margin-bottom:0.5rem">
            @else
              <div class="benefit-logo">{{ $benefit->name }}</div>
            @endif
            <p>{{ $benefit->description }}</p>
            <span style="font-size:0.875rem;color:var(--yellow);font-weight:600">Details ansehen →</span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

@endsection
