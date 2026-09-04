@extends('layouts.app')

@php
  $flyerExtension = strtolower(pathinfo($volunteerListing->flyer_path ?? '', PATHINFO_EXTENSION));
  $heroImagePath = in_array($flyerExtension, ['jpg', 'jpeg', 'png', 'webp'], true)
      ? $volunteerListing->flyer_path
      : $volunteerListing->organisation?->logo_path;
  $location = trim(($volunteerListing->zip ?? '').' '.($volunteerListing->city ?? ''));
  $address = array_filter([
      $volunteerListing->street,
      $location,
  ]);
@endphp

@section('title', $volunteerListing->title . ' – Freiwilligenzentrum Kärnten')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($volunteerListing->description), 155))

@section('hero')
<div class="page-hero" @if($heroImagePath) style="background-image:linear-gradient(rgba(10,28,10,0.78),rgba(10,28,10,0.78)),url('{{ '/storage/'.ltrim($heroImagePath, '/') }}');background-size:cover;background-position:center" @endif>
  <div class="container">
    <span class="eyebrow">GESUCH</span>
    <h1 class="h2">{{ $volunteerListing->title }}</h1>
    @if($volunteerListing->organisation)
      <p style="opacity:.8">{{ $volunteerListing->organisation->name }}</p>
    @endif
  </div>
</div>
@endsection

@section('content')
  <section class="section">
    <div class="container">
      <div class="box volunteer-listing-detail">
        <article>
          <div class="news-data" style="margin-bottom:2rem;display:flex;gap:1.5rem;flex-wrap:wrap">
            @if($volunteerListing->organisation)
              <span><strong>Organisation:</strong> {{ $volunteerListing->organisation->name }}</span>
            @endif
            @if($address)
              <span><strong>Ort:</strong> {{ implode(', ', $address) }}</span>
            @endif
            @if($volunteerListing->valid_until)
              <span><strong>Gültig bis:</strong> {{ $volunteerListing->valid_until->format('d.m.Y') }}</span>
            @endif
          </div>

          <div class="article-body" style="line-height:1.75;color:#374151">
            {!! nl2br(e($volunteerListing->description)) !!}
          </div>

          @if($volunteerListing->flyer_path)
            <div style="margin-top:2rem">
              <h2 class="h3">Flyer</h2>
              @if(in_array($flyerExtension, ['jpg', 'jpeg', 'png', 'webp'], true))
                <img
                  data-testid="flyer-preview"
                  src="{{ '/storage/'.ltrim($volunteerListing->flyer_path, '/') }}"
                  alt="Flyer zu {{ $volunteerListing->title }}"
                  style="display:block;width:100%;height:auto;object-fit:contain;border-radius:.75rem">
              @elseif($flyerExtension === 'pdf')
                <object
                  data-testid="flyer-preview"
                  data="{{ '/storage/'.ltrim($volunteerListing->flyer_path, '/') }}"
                  type="application/pdf"
                  style="display:block;width:100%;height:900px;max-height:90vh;border:0;border-radius:.75rem;background:#f3f4f6">
                  <p>Der Flyer kann in diesem Browser nicht eingebettet werden.</p>
                </object>
              @endif
            </div>
          @endif

          @if($volunteerListing->categories->isNotEmpty() || $volunteerListing->activities->isNotEmpty())
            <div style="margin-top:2rem;padding:1.5rem;background:#f9fafb;border-radius:.75rem">
              @if($volunteerListing->categories->isNotEmpty())
                <p style="margin-top:0"><strong>Kategorien:</strong> {{ $volunteerListing->categories->pluck('name')->join(', ') }}</p>
              @endif
              @if($volunteerListing->activities->isNotEmpty())
                <p style="margin-bottom:0"><strong>Tätigkeiten:</strong> {{ $volunteerListing->activities->pluck('name')->join(', ') }}</p>
              @endif
            </div>
          @endif

          @if(! $volunteerListing->is_spontaneous && ($volunteerListing->weekdays || $volunteerListing->daytimes || $volunteerListing->hours_per_week))
            <div style="margin-top:2rem">
              <h2 class="h3">Zeitlicher Rahmen</h2>
              @if($volunteerListing->weekdays)
                <p><strong>Wochentage:</strong> {{ collect($volunteerListing->weekdays)->map(fn ($day) => ucfirst($day))->join(', ') }}</p>
              @endif
              @if($volunteerListing->daytimes)
                <p><strong>Tageszeiten:</strong> {{ collect($volunteerListing->daytimes)->map(fn ($daytime) => ucfirst($daytime))->join(', ') }}</p>
              @endif
              @if($volunteerListing->hours_per_week)
                <p><strong>Zeitaufwand:</strong> {{ $volunteerListing->hours_per_week }} Stunden pro Woche</p>
              @endif
            </div>
          @elseif($volunteerListing->is_spontaneous)
            <div style="margin-top:2rem">
              <h2 class="h3">Zeitlicher Rahmen</h2>
              <p>Dieses Engagement ist spontan und flexibel möglich.</p>
            </div>
          @endif

          <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:2rem">
            @if($volunteerListing->website_link)
              <a class="btn dark" href="{{ $volunteerListing->website_link }}" target="_blank" rel="noopener noreferrer">Zur Website <span class="arrow">→</span></a>
            @endif
            @if($volunteerListing->flyer_path)
              <a class="btn light" href="{{ '/storage/'.ltrim($volunteerListing->flyer_path, '/') }}" target="_blank" rel="noopener noreferrer">Flyer öffnen</a>
            @endif
          </div>

          <a href="{{ route('volunteer-listings.index') }}" style="display:inline-block;margin-top:2rem;font-weight:600;color:inherit">← Zurück zu allen Gesuchen</a>
        </article>
      </div>
    </div>
  </section>
@endsection