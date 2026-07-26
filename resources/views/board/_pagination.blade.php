@props(['paginator', 'baseUrl', 'borders' => 3])

@if ($paginator->lastPage() > 1)
<div class="pagination">
    @for ($page = 1; $page <= $paginator->lastPage(); $page++)
        @php
            $isBorderPage = $page <= $borders || $page > $paginator->lastPage() - $borders;
            $isNearCurrentPage = $page > $paginator->currentPage() - $borders && $page < $paginator->currentPage() + $borders;
            $shouldRenderEllipsis = ($page > $borders && $page === $paginator->currentPage() - $borders)
                || ($page === $paginator->currentPage() + $borders && $page < $paginator->lastPage() - $borders);
            $url = $page === 1 ? $baseUrl : $baseUrl.'/'.$page;
        @endphp

        @if ($isBorderPage || $isNearCurrentPage)
            @if ($page === $paginator->currentPage())
            <em>{{ $page }}</em>
            @else
            <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @elseif ($shouldRenderEllipsis)
            &hellip;
        @endif
    @endfor
</div>
@endif
