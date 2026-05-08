@if ($paginator->hasPages())
    <nav class="pagination-container">
        <div class="pagination-info">
            Показано с <strong>{{ $paginator->firstItem() }}</strong> по <strong>{{ $paginator->lastItem() }}</strong> из <strong>{{ $paginator->total() }}</strong> записей
        </div>

        <div class="pagination-nav">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-arrow disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-arrow">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="pagination-pages">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="pagination-ellipsis">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="pagination-page active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination-page">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-arrow">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-arrow disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
