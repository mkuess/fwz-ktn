@extends('layouts.app')

@section('title', 'Gesuche – Freiwilligenzentrum Kärnten')
@section('meta_description', 'Aktuelle Gesuche und Möglichkeiten für freiwilliges Engagement in Kärnten.')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">GESUCHE</span>
    <h1 class="h2">Aktuelle Möglichkeiten für dein Engagement</h1>
  </div>
</div>
@endsection

@section('content')
  <section class="section">
    <div class="container">
      <div class="news-grid">
        @forelse($volunteerListings as $volunteerListing)
          @include('volunteer-listings.partials.card', ['listing' => $volunteerListing])
        @empty
          <p class="muted">Aktuell sind keine Gesuche eingetragen.</p>
        @endforelse
      </div>

      @if($volunteerListings->hasPages())
        <div style="margin-top:2rem;display:flex;justify-content:center">
          {{ $volunteerListings->links() }}
        </div>
      @endif
    </div>
  </section>
@endsection