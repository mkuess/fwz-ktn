@extends('layouts.app')

@section('title', 'Aktuelles – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="page-hero">
  <div class="container">
    <span class="eyebrow">NEWS & AKTUELLES</span>
    <h1 class="h2">Aktuelles aus der Freiwilligenarbeit</h1>
  </div>
</div>
@endsection

@section('content')

  <section class="section">
    <div class="container">
      <div class="news-grid">
        @forelse($articles as $article)
          <article class="news-card">
            @if($article->cover_image_path)
              <img src="{{ Storage::url($article->cover_image_path) }}" alt="{{ $article->title }}">
            @endif
            <div class="news-body">
              @if($article->article_category)
                <div class="news-meta">{{ strtoupper($article->article_category) }}</div>
              @endif
              <h3 class="h3">{{ $article->title }}</h3>
              <div class="news-data">
                @if($article->organisation_name)
                  <div>{{ $article->organisation_name }}</div>
                @endif
                @if($article->location)
                  <div>{{ $article->location }}</div>
                @endif
                @if($article->event_time)
                  <div>{{ $article->event_time }}</div>
                @endif
              </div>
              @if($article->excerpt)
                <p>{{ Str::limit($article->excerpt, 120) }}</p>
              @endif
              <a href="{{ route('articles.show', $article->slug) }}" style="font-size:0.875rem;font-weight:600;color:inherit">Artikel lesen →</a>
            </div>
          </article>
        @empty
          <p class="muted">Keine Beiträge vorhanden.</p>
        @endforelse
      </div>

      @if($articles->hasPages())
        <div style="margin-top:2rem;display:flex;justify-content:center">
          {{ $articles->links() }}
        </div>
      @endif
    </div>
  </section>

@endsection
