@extends('layouts.app')

@section('title', $organisation->name . ' – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container" style="display:flex;align-items:center;gap:2rem;flex-wrap:wrap">
    @if($organisation->logo_path)
      <img src="{{ Storage::url($organisation->logo_path) }}"
           alt="{{ $organisation->name }}"
           style="width:100px;height:100px;object-fit:contain;background:#fff;border-radius:0.75rem;padding:0.5rem;flex-shrink:0">
    @endif
    <div>
      @foreach($organisation->categories as $cat)
        <span style="background:#c9a227;color:#fff;padding:0.2rem 0.75rem;border-radius:1rem;font-size:0.75rem;font-weight:600;margin-right:0.5rem">
          {{ $cat->name }}
        </span>
      @endforeach
      <h1 class="h2" style="margin:0.5rem 0 0">{{ $organisation->name }}</h1>
      @if(trim(($organisation->zip ?? '') . ' ' . ($organisation->city ?? '')))
        <p style="opacity:0.7;font-size:0.875rem;margin:0.25rem 0 0">
          📍 {{ trim(($organisation->zip ?? '') . ' ' . ($organisation->city ?? '')) }}
        </p>
      @endif
    </div>
  </div>
</div>
@endsection

@section('content')

<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;align-items:start">

      {{-- Left column --}}
      <div>
        @if($organisation->description)
          <div class="box" style="margin-bottom:2rem">
            <h2 style="margin-top:0">Über uns</h2>
            <p style="color:#374151;line-height:1.7">{{ $organisation->description }}</p>
          </div>
        @endif

        @if($organisation->volunteerListings->count() > 0)
          <div class="box">
            <h2 style="margin-top:0">Aktuelle Gesuche</h2>
            @foreach($organisation->volunteerListings as $listing)
              <div style="padding:1rem 0;border-bottom:1px solid #e5e7eb">
                <h3 style="margin:0 0 0.5rem;font-size:1rem">{{ $listing->title }}</h3>
                <p style="margin:0 0 0.25rem;color:#6b7280;font-size:0.875rem">{{ Str::limit($listing->description, 150) }}</p>
                @if($listing->city)
                  <span style="font-size:0.75rem;color:#9ca3af">📍 {{ $listing->zip }} {{ $listing->city }}</span>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- Right column: contact card --}}
      <div>
        <div class="box" style="position:sticky;top:2rem">
          <h3 style="margin-top:0;font-size:0.875rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em">Kontakt &amp; Infos</h3>

          @if($organisation->contact_person)
            <div style="margin-bottom:1rem">
              <div style="font-size:0.75rem;color:#9ca3af">Ansprechpartner:in</div>
              <div style="font-weight:600">{{ $organisation->contact_person }}</div>
            </div>
          @endif

          @if($organisation->street || $organisation->city)
            <div style="margin-bottom:1rem;display:flex;gap:0.5rem">
              <span>📍</span>
              <div>
                @if($organisation->street)<div>{{ $organisation->street }}</div>@endif
                @if($organisation->zip || $organisation->city)<div>{{ $organisation->zip }} {{ $organisation->city }}</div>@endif
              </div>
            </div>
          @endif

          @if($organisation->email)
            <div style="margin-bottom:1rem;display:flex;gap:0.5rem;align-items:center">
              <span>✉️</span>
              <a href="mailto:{{ $organisation->email }}" style="color:#1a2e1a">{{ $organisation->email }}</a>
            </div>
          @endif

          @if($organisation->phone)
            <div style="margin-bottom:1rem;display:flex;gap:0.5rem;align-items:center">
              <span>📞</span>
              <a href="tel:{{ $organisation->phone }}" style="color:#1a2e1a">{{ $organisation->phone }}</a>
            </div>
          @endif

          @if($organisation->website)
            <div style="margin-bottom:1.5rem;display:flex;gap:0.5rem;align-items:center">
              <span>🌐</span>
              <a href="{{ $organisation->website }}" target="_blank" rel="noopener" style="color:#c9a227;word-break:break-all">
                {{ $organisation->website }}
              </a>
            </div>
          @endif

          @if($organisation->zvr_number)
            <div style="margin-bottom:1.5rem;font-size:0.75rem;color:#9ca3af">
              ZVR: {{ $organisation->zvr_number }}
            </div>
          @endif

          <a href="{{ route('member.register') }}?organisation={{ $organisation->id }}"
             class="btn primary"
             style="display:block;text-align:center;border-radius:2rem;text-decoration:none">
            Mitglied werden →
          </a>
        </div>
      </div>

    </div>

    <div style="margin-top:2rem">
      <a href="{{ route('home') }}#vereine" style="color:#6b7280">← Zurück zur Startseite</a>
    </div>
  </div>
</section>

@endsection
