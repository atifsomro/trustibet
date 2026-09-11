@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="w-full">
        <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
            <p class="text-sm text-white/50">
                Showing
                <span class="font-semibold text-white/80">{{ $paginator->firstItem() ?? 0 }}</span>
                to
                <span class="font-semibold text-white/80">{{ $paginator->lastItem() ?? 0 }}</span>
                of
                <span class="font-semibold text-white/80">{{ $paginator->total() }}</span>
                results
            </p>

            <div class="flex flex-wrap items-center justify-center gap-1.5">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-brand-border bg-brand-dark text-white/30">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </span>
                @else
                    <a
                        href="{{ $paginator->previousPageUrl() }}"
                        rel="prev"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-brand-border bg-brand-dark text-white/70 transition hover:border-green-500/40 hover:text-green-400"
                        aria-label="Previous"
                    >
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex h-10 min-w-10 items-center justify-center px-2 text-sm text-white/40">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span
                                    aria-current="page"
                                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-green-500/40 bg-green-500/15 px-3 text-sm font-semibold text-green-400"
                                >
                                    {{ $page }}
                                </span>
                            @else
                                <a
                                    href="{{ $url }}"
                                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-brand-border bg-brand-dark px-3 text-sm font-medium text-white/70 transition hover:border-orange-500/40 hover:text-orange-400"
                                >
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a
                        href="{{ $paginator->nextPageUrl() }}"
                        rel="next"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-brand-border bg-brand-dark text-white/70 transition hover:border-green-500/40 hover:text-green-400"
                        aria-label="Next"
                    >
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-brand-border bg-brand-dark text-white/30">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
