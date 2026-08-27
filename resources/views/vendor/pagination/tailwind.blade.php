@if ($paginator->hasPages())
    <nav class="pagination-nav" role="navigation" aria-label="Seitennavigation">
        <p class="pagination-summary">
            Zeige
            <span>{{ $paginator->firstItem() ?? 0 }}</span>
            bis
            <span>{{ $paginator->lastItem() ?? 0 }}</span>
            von
            <span>{{ $paginator->total() }}</span>
            Ergebnisse
        </p>

        <ul class="pagination">
            @if ($paginator->onFirstPage())
                <li>
                    <span class="pagination-control disabled" aria-disabled="true">
                        <span aria-hidden="true">←</span>
                        Zurück
                    </span>
                </li>
            @else
                <li>
                    <a class="pagination-control" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Zur vorherigen Seite">
                        <span aria-hidden="true">←</span>
                        Zurück
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="pagination-ellipsis">
                        <span aria-hidden="true">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active">
                                <span aria-current="page" aria-label="Aktuelle Seite {{ $page }}">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" aria-label="Gehe zu Seite {{ $page }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li>
                    <a class="pagination-control" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Zur nächsten Seite">
                        Weiter
                        <span aria-hidden="true">→</span>
                    </a>
                </li>
            @else
                <li>
                    <span class="pagination-control disabled" aria-disabled="true">
                        Weiter
                        <span aria-hidden="true">→</span>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif