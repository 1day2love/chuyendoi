<!--
@if ($paginator->hasPages())
    <ul>
        @if ($paginator->onFirstPage())
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    aria-label="@lang('pagination.previous')">&larr;</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled" aria-disabled="true"><a>{{ $element }}</a>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li aria-current="page"><a class="current">{{ $page }}</a></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    aria-label="@lang('pagination.next')">&rarr;</a>
            </li>
        @else
        @endif
    </ul>
@endif
-->
@if ($paginator->hasPages())
    <ul>
        {{-- Previous Page Link --}}
        @if (!$paginator->onFirstPage())
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    aria-label="@lang('pagination.previous')">&larr;</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
            $start = max(1, $currentPage - 2);
            $end = min($currentPage + 4, $lastPage);
            if ($start > $lastPage - 5) {
                $start = max(1, $lastPage - 5);
                $end = $lastPage;
            }
            elseif ($end < 6) {
                $start = 1;
                $end = min(6, $lastPage);
            }
        @endphp

        @for ($i = $start; $i <= $end; $i++)
            @if ($i == $paginator->currentPage())
                <li aria-current="page"><a class="current">{{ $i }}</a></li>
            @else
                <li><a href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
            @endif
        @endfor

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    aria-label="@lang('pagination.next')">&rarr;</a>
            </li>
        @endif
    </ul>
@endif