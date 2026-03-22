@if ($paginator->hasPages())
    <nav>
        <div class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="page-link" style="opacity:.4;cursor:not-allowed;">
                    <i class="ri-arrow-left-s-line"></i>
                </span>
            @else
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="page-link" style="cursor:default;">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            @else
                <span class="page-link" style="opacity:.4;cursor:not-allowed;">
                    <i class="ri-arrow-right-s-line"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
