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
      <div class="org-directory-header">
        <div>
          <span class="eyebrow">Vereinsverzeichnis</span>
          <h2 class="h2">Vereine &amp; Organisationen</h2>
        </div>
        <a class="btn dark" href="{{ route('organisations.map') }}">Karte öffnen <span class="arrow">→</span></a>
      </div>

      <div class="vereine-search-wrap">
        <div class="combobox-wrap">
          <input
            id="vereine-suche"
            class="input"
            type="search"
            value="{{ request('q') }}"
            placeholder="Verein, Organisation, Ort oder Stichwort"
            aria-label="Suche nach Verein oder Organisation"
            autocomplete="off"
          />
        </div>
        <p class="vereine-result-count" aria-live="polite" aria-atomic="true">
          {{ $organisations->total() === 1 ? '1 Verein gefunden' : $organisations->total().' Vereine gefunden' }}
        </p>
      </div>

      @php($activeCategory = request('kategorie', ''))
      <div class="chips" role="group" aria-label="Vereine nach Tätigkeitsfeld filtern">
        <button
          type="button"
          class="chip{{ $activeCategory === '' || $activeCategory === 'alle' ? ' active' : '' }}"
          data-kategorie=""
          aria-pressed="{{ $activeCategory === '' || $activeCategory === 'alle' ? 'true' : 'false' }}"
        >Alle</button>
        @foreach($categories as $category)
          <button
            type="button"
            class="chip{{ $activeCategory === $category->slug ? ' active' : '' }}"
            data-kategorie="{{ $category->slug }}"
            aria-pressed="{{ $activeCategory === $category->slug ? 'true' : 'false' }}"
          >{{ $category->name }}</button>
        @endforeach
      </div>

      <div
        class="org-grid"
        id="vereine-grid"
        data-infinite-scroll="true"
        data-search-limit="12"
        data-initial-page="{{ $organisations->currentPage() }}"
        data-has-more="{{ $organisations->hasMorePages() ? 'true' : 'false' }}"
        data-active-category="{{ $activeCategory }}"
      >
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
                <div class="org-card-categories">
                  @foreach($org->categories->take(2) as $cat)
                    <span>{{ $cat->name }}</span>
                  @endforeach
                </div>
              @endif
            </div>
          </a>
        @empty
          <p class="muted">Aktuell sind noch keine Vereine im Verzeichnis freigeschaltet.</p>
        @endforelse
      </div>

      <div
        id="vereine-load-more"
        class="vereine-load-more"
        role="status"
        aria-live="polite"
        @if(! $organisations->hasMorePages()) hidden @endif
      >Weitere Vereine werden geladen …</div>
    </div>
  </section>

@endsection
