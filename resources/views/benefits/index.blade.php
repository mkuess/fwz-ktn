@extends('layouts.app')

@section('title', 'Benefits für Mitglieder – Freiwilligenzentrum Kärnten')

@section('content')

  <section class="section hero-small">
    <div class="container">
      <div class="section-title center">
        <span class="eyebrow">EXKLUSIVE VORTEILE</span>
        <h1 class="h2">Benefits für Mitglieder.</h1>
        <p class="lead">Als Mitglied eines registrierten Vereins erhältst du Zugang zu Vergünstigungen bei ausgewählten Kärntner Partnerbetrieben.</p>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="box benefit-box">
        <div class="benefit-grid">
          @forelse($benefits as $benefit)
            <div class="benefit-card">
              @if($benefit->logo_path)
                <img src="{{ Storage::url($benefit->logo_path) }}" alt="{{ $benefit->name }}" style="max-height:48px;object-fit:contain;margin-bottom:0.5rem">
              @else
                <div class="benefit-logo">{{ $benefit->name }}</div>
              @endif
              <p>{{ $benefit->description }}</p>
              @if($benefit->website)
                <a href="{{ $benefit->website }}" target="_blank" rel="noopener" style="font-size:0.875rem;color:inherit">Zum Partner →</a>
              @endif
            </div>
          @empty
            <p class="muted">Derzeit sind keine Benefits verfügbar.</p>
          @endforelse
        </div>
      </div>
    </div>
  </section>

@endsection
