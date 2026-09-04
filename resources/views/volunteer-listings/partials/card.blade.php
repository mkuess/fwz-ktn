@php
  $flyerExtension = strtolower(pathinfo($listing->flyer_path ?? '', PATHINFO_EXTENSION));
  $imagePath = in_array($flyerExtension, ['jpg', 'jpeg', 'png', 'webp'], true)
      ? $listing->flyer_path
      : $listing->organisation?->logo_path;
  $location = trim(($listing->zip ?? '').' '.($listing->city ?? ''));
@endphp

<a href="{{ route('volunteer-listings.show', $listing) }}" style="text-decoration:none;color:inherit;display:block">
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
    </div>
  </article>
</a>