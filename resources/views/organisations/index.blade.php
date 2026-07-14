@extends('layouts.app')

@section('title', 'Vereine & Organisationen – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">FREIWILLIGENARBEIT IN KÄRNTEN</span>
    <h1 class="h2">Vereine &amp; Organisationen</h1>
    <p>Entdecke registrierte Vereine und Organisationen und werde Teil der Freiwilligengemeinschaft.</p>
  </div>
</div>
@endsection

@section('content')

  <section class="section">
    <div class="container">
      <div class="org-grid">
        @forelse($organisations as $org)
          <a href="{{ route('organisations.show', $org->id) }}" style="text-decoration:none;color:inherit;display:block">
            <div class="org-card" style="cursor:pointer">
              <img
                class="org-logo"
                src="{{ $org->logo_path ? Storage::url($org->logo_path) : asset('img/placeholder-verein-logo.svg') }}"
                alt="{{ $org->name }}"
                loading="lazy"
                onerror="this.src='{{ asset('img/placeholder-verein-logo.svg') }}'"
              >
              <div class="name">{{ $org->name }}</div>
              @if(trim(($org->zip ?? '') . ' ' . ($org->city ?? '')))
                <div class="place">{{ trim(($org->zip ?? '') . ' ' . ($org->city ?? '')) }}</div>
              @endif
              @if($org->categories->count())
                <div style="margin-top:0.5rem;display:flex;flex-wrap:wrap;gap:0.25rem;justify-content:center">
                  @foreach($org->categories->take(2) as $cat)
                    <span style="background:#f3f4f6;color:#374151;padding:0.15rem 0.5rem;border-radius:1rem;font-size:0.65rem;font-weight:600">
                      {{ $cat->name }}
                    </span>
                  @endforeach
                </div>
              @endif
            </div>
          </a>
        @empty
          <p class="muted">Aktuell sind noch keine Vereine im Verzeichnis freigeschaltet.</p>
        @endforelse
      </div>

      @if($organisations->hasPages())
        <div style="margin-top:2rem;display:flex;justify-content:center">
          {{ $organisations->links() }}
        </div>
      @endif
    </div>
  </section>

@endsection
