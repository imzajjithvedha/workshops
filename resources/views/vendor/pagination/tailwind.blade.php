<nav class="flex items-center justify-between flex-col sm:flex-row gap-3 sm:gap-0" role="navigation" aria-label="{{ __('Pagination Navigation') }}">

    <p class="text-sm text-(--color-primary)">
        Showing
        <span class="font-medium">{{ $paginator->firstItem() ?? 0 }}</span>
        to
        <span class="font-medium">{{ $paginator->lastItem() ?? 0 }}</span>
        of
        <span class="font-medium">{{ $paginator->total() }}</span>
        results
    </p>

    @if ($paginator->hasPages())
        <div class="inline-flex items-center gap-1">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 border border-(--color-border) text-(--color-primary) cursor-not-allowed" aria-disabled="true">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 border border-(--color-border) text-(--color-primary) hover:bg-(--color-bg-button-hover) transition duration-150" aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm border border-(--color-border) text-(--color-primary)">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium border border-(--color-primary) bg-(--color-primary) text-white cursor-default" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 text-sm border border-(--color-border) text-(--color-primary) hover:bg-(--color-bg-button-hover) transition duration-150" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 border border-(--color-border) text-(--color-primary) hover:bg-(--color-bg-button-hover) transition duration-150" aria-label="{{ __('pagination.next') }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 border border-(--color-border) text-(--color-primary) cursor-not-allowed" aria-disabled="true">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </span>
            @endif

        </div>
    @endif

</nav>
