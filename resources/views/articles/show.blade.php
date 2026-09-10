@extends('layouts.app')

@section('title', $article->title . ' – Freiwilligenzentrum Kärnten')

@section('hero')
<div class="page-hero" @if($article->cover_image_path) style="background-image:linear-gradient(rgba(10,28,10,0.75),rgba(10,28,10,0.75)),url('{{ Storage::url($article->cover_image_path) }}');background-size:cover;background-position:center" @endif>
  <div class="container">
    @if($article->article_category)
      <span class="eyebrow">{{ strtoupper($article->article_category) }}</span>
    @else
      <span class="eyebrow">BEITRAG</span>
    @endif
    <h1 class="h2">{{ $article->title }}</h1>
    @if($article->published_at)
      <p style="opacity:0.7;font-size:0.875rem">{{ $article->published_at->format('d.m.Y') }}</p>
    @endif
  </div>
</div>
@endsection

@section('content')

  <section class="section">
    <div class="container">
      <div class="box" style="max-width:800px;margin:0 auto;padding:2.5rem">
        <article>
          @if($article->excerpt)
            <p style="font-size:1.125rem;color:#6b7280;margin-bottom:2rem;border-left:4px solid var(--yellow);padding-left:1rem">{{ $article->excerpt }}</p>
          @endif

          <div class="news-data" style="margin-bottom:1.5rem;display:flex;gap:1rem;flex-wrap:wrap">
            @if($article->organisation_name)
              <span>🏢 {{ $article->organisation_name }}</span>
            @endif
            @if($article->location)
              <span>📍 {{ $article->location }}</span>
            @endif
            @if($article->event_time)
              <span>🕐 {{ $article->event_time }}</span>
            @endif
          </div>

          @if($article->cover_image_path)
            <img
              src="{{ Storage::url($article->cover_image_path) }}"
              alt="{{ $article->title }}"
              style="width:100%;max-height:500px;object-fit:cover;border-radius:0.75rem;margin-bottom:2rem;display:block">
          @endif

          <div class="article-body" style="line-height:1.75;color:#374151">
            {!! $article->body !!}
          </div>

          @php
            $videoAttachments = $article->attachments->filter(fn ($attachment) => $attachment->isVideo());
            $downloadAttachments = $article->attachments->reject(fn ($attachment) => $attachment->isVideo());
          @endphp

          @if($videoAttachments->isNotEmpty())
            <div style="margin-top:2rem;display:flex;flex-direction:column;gap:1.5rem">
              @foreach($videoAttachments as $video)
                <figure style="margin:0">
                  <video
                    controls
                    playsinline
                    preload="metadata"
                    style="display:block;width:100%;height:auto;max-height:70vh;background:#000;border-radius:0.75rem"
                    aria-label="{{ $video->original_name }}">
                    <source src="{{ Storage::url($video->file_path) }}" type="{{ $video->mime_type ?: 'video/mp4' }}">
                    Dein Browser kann dieses Video nicht wiedergeben.
                    <a href="{{ Storage::url($video->file_path) }}">Video herunterladen</a>
                  </video>
                  @if($video->original_name)
                    <figcaption style="margin-top:0.5rem;color:#6b7280;font-size:0.875rem">
                      {{ $video->original_name }}
                    </figcaption>
                  @endif
                </figure>
              @endforeach
            </div>
          @endif

          @if($downloadAttachments->isNotEmpty())
            <div style="margin-top:2rem;padding:1.5rem;background:#f9fafb;border-radius:0.5rem">
              <h3 style="margin:0 0 1rem">Anhänge</h3>
              <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.5rem">
                @foreach($downloadAttachments as $attachment)
                  <li>
                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" download="{{ $attachment->original_name }}" style="color:var(--yellow)">
                      📎 {{ $attachment->original_name }}
                    </a>
                  </li>
                @endforeach
              </ul>
            </div>
          @endif

          <a href="{{ route('articles.index') }}" style="display:inline-block;margin-top:2rem;font-weight:600;color:inherit">← Zurück zur Übersicht</a>
        </article>
      </div>
    </div>
  </section>

@endsection
