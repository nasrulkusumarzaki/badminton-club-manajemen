@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination">

        {{-- Tombol Previous --}}
        @if ($paginator->onFirstPage())
            <span class="page-btn disabled">&laquo; Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-btn" rel="prev">&laquo; Sebelumnya</a>
        @endif

        {{-- Nomor halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-btn disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Tombol Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-btn" rel="next">Selanjutnya &raquo;</a>
        @else
            <span class="page-btn disabled">Selanjutnya &raquo;</span>
        @endif

    </nav>

    <p class="pagination-info">
        Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
    </p>
@endif