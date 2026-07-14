@extends('layouts.app')

@section('title', $organisation->name . ' – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container" style="max-width:1200px;margin:0 auto;padding:3rem 1.5rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap">
    @if($organisation->logo_path)
      <img src="{{ Storage::url($organisation->logo_path) }}"
           alt="{{ $organisation->name }}"
           style="width:80px;height:80px;object-fit:contain;background:#fff;border-radius:0.75rem;padding:0.5rem;flex-shrink:0">
    @endif
    <div>
      @if($organisation->categories->count())
        <div style="margin-bottom:0.5rem">
          @foreach($organisation->categories as $cat)
            <span style="background:#c9a227;color:#fff;padding:0.15rem 0.6rem;border-radius:1rem;font-size:0.7rem;font-weight:600;margin-right:0.4rem">
              {{ $cat->name }}
            </span>
          @endforeach
        </div>
      @endif
      <h1 class="h2" style="margin:0 0 0.25rem">{{ $organisation->name }}</h1>
      @if(trim(($organisation->zip ?? '') . ' ' . ($organisation->city ?? '')))
        <p style="opacity:0.65;font-size:0.875rem;margin:0">
          📍 {{ trim(($organisation->zip ?? '') . ' ' . ($organisation->city ?? '')) }}
        </p>
      @endif
    </div>
  </div>
</div>
@endsection

@section('content')

<section class="section">
  <div style="max-width:1200px;margin:3rem auto 0;padding:0 1.5rem">

    <div class="org-profile-grid">

      {{-- Left column --}}
      <div>
        @if($organisation->description)
          <div class="box" style="padding:2rem;margin-bottom:2rem">
            <h2 style="margin:0 0 1rem;font-size:1.25rem">Über uns</h2>
            <p style="color:#374151;line-height:1.75;margin:0">{{ $organisation->description }}</p>
          </div>
        @endif

        @if($organisation->volunteerListings->count() > 0)
          <div class="box" style="padding:2rem">
            <h2 style="margin:0 0 1rem;font-size:1.25rem">Aktuelle Gesuche</h2>
            @foreach($organisation->volunteerListings as $listing)
              <div style="padding:1rem 0;border-bottom:1px solid #e5e7eb">
                <h3 style="margin:0 0 0.5rem;font-size:1rem">{{ $listing->title }}</h3>
                <p style="margin:0 0 0.25rem;color:#6b7280;font-size:0.875rem;line-height:1.6">{{ Str::limit($listing->description, 150) }}</p>
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
        <div class="box org-contact-card" style="padding:2rem">
          <h3 style="margin:0 0 1.25rem;font-size:0.8rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.07em">Kontakt &amp; Infos</h3>

          @if($organisation->contact_person)
            <div style="margin-bottom:0.875rem">
              <div style="font-size:0.7rem;color:#9ca3af;margin-bottom:0.2rem">Ansprechpartner:in</div>
              <div style="font-weight:600">{{ $organisation->contact_person }}</div>
            </div>
          @endif

          @if($organisation->street || $organisation->city)
            <div style="margin-bottom:0.875rem;display:flex;align-items:flex-start;gap:0.625rem">
              <span style="flex-shrink:0">📍</span>
              <div>
                @if($organisation->street)<div>{{ $organisation->street }}</div>@endif
                @if($organisation->zip || $organisation->city)<div>{{ $organisation->zip }} {{ $organisation->city }}</div>@endif
              </div>
            </div>
          @endif

          @if($organisation->email)
            <div style="margin-bottom:0.875rem;display:flex;align-items:center;gap:0.625rem">
              <span style="flex-shrink:0">✉️</span>
              <a href="mailto:{{ $organisation->email }}" style="color:#1a2e1a;word-break:break-all">{{ $organisation->email }}</a>
            </div>
          @endif

          @if($organisation->phone)
            <div style="margin-bottom:0.875rem;display:flex;align-items:center;gap:0.625rem">
              <span style="flex-shrink:0">📞</span>
              <a href="tel:{{ $organisation->phone }}" style="color:#1a2e1a">{{ $organisation->phone }}</a>
            </div>
          @endif

          @if($organisation->website)
            <div style="margin-bottom:0.875rem;display:flex;align-items:center;gap:0.625rem">
              <span style="flex-shrink:0">🌐</span>
              <a href="{{ $organisation->website }}" target="_blank" rel="noopener" style="color:#c9a227;word-break:break-all">
                {{ $organisation->website }}
              </a>
            </div>
          @endif

          @if($organisation->zvr_number)
            <div style="margin-bottom:0.875rem;font-size:0.75rem;color:#9ca3af">
              ZVR: {{ $organisation->zvr_number }}
            </div>
          @endif

          <a href="{{ route('member.register') }}?organisation={{ $organisation->id }}"
             style="display:block;text-align:center;background:#c9a227;color:#1a2e1a;padding:0.875rem 1.5rem;border-radius:2rem;text-decoration:none;font-weight:700;font-size:1rem;margin-top:1.5rem;transition:background 0.2s"
             onmouseover="this.style.background='#b8911f'"
             onmouseout="this.style.background='#c9a227'">
            Mitglied werden →
          </a>
        </div>
      </div>

    </div>

    <div style="margin-top:3rem;margin-bottom:3rem">
      <a href="{{ route('organisations.index') }}" style="color:#6b7280">← Zurück zur Vereinsübersicht</a>
    </div>
  </div>
</section>

@endsection
