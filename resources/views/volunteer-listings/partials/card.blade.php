@php
  $flyerExtension = strtolower(pathinfo($listing->flyer_path ?? '', PATHINFO_EXTENSION));
  $imagePath = in_array($flyerExtension, ['jpg', 'jpeg', 'png', 'webp'], true)
      ? $listing->flyer_path
      : $listing->organisation?->logo_path;
  $location = trim(($listing->zip ?? '').' '.($listing->city ?? ''));
@endphp

<article class="news-card">
  @if($imagePath)
    <img src="{{ '/storage/'.ltrim($imagePath, '/') }}" alt="{{ $listing->title }}">
  @endif
  <div class="news-body">
    <div class="news-meta">GESUCH</div>
    <h3 class="h3">{{ $listing->title }}</h3>
    <div class="news-data">
      @if($listing->organisation)
        <div>{{ $listing->organisation->name }}</div>
      @endif
      @if($location)
        <div>{{ $location }}</div>
      @endif
      @if($listing->valid_until)
        <div>Gültig bis {{ $listing->valid_until->format('d.m.Y') }}</div>
      @endif
    </div>
    <p>{{ \Illuminate\Support\Str::limit($listing->description, 150) }}</p>
    @if($listing->categories->isNotEmpty())
      <div class="news-data">
        <div>{{ $listing->categories->pluck('name')->join(' · ') }}</div>
      </div>
    @endif
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:auto;padding-top:1rem">
      @if($listing->website_link)
        <a class="btn dark" href="{{ $listing->website_link }}" target="_blank" rel="noopener noreferrer">Zur Website <span class="arrow">→</span></a>
      @endif
      @if($listing->flyer_path)
        <a class="btn light" href="{{ '/storage/'.ltrim($listing->flyer_path, '/') }}" target="_blank" rel="noopener noreferrer">Flyer öffnen</a>
      @endif
    </div>
  </div>
</article>