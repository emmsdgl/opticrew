@props([
    'paginator',
    'onEachSide' => 1,
])

@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $window = $onEachSide;
        $from = ($currentPage - 1) * $paginator->perPage() + 1;
        $to = min($currentPage * $paginator->perPage(), $paginator->total());
        $total = $paginator->total();

        $pages = collect();
        $pages->push(1);
        $rangeStart = max(2, $currentPage - $window);
        $rangeEnd = min($lastPage - 1, $currentPage + $window);
        if ($rangeStart > 2) { $pages->push('...'); }
        for ($i = $rangeStart; $i <= $rangeEnd; $i++) { $pages->push($i); }
        if ($rangeEnd < $lastPage - 1) { $pages->push('...'); }
        if ($lastPage > 1) { $pages->push($lastPage); }
    @endphp

    <div class="flex items-center justify-between px-2">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Showing {{ $from }}-{{ $to }} of {{ $total }}
        </p>
        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg opacity-40 cursor-not-allowed text-gray-700 dark:text-gray-300">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($pages as $page)
                @if ($page === '...')
                    <span class="px-3 py-1.5 text-sm text-gray-400 dark:text-gray-500">...</span>
                @elseif ($page === $currentPage)
                    <span class="px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}"
                        class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            @else
                <span class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg opacity-40 cursor-not-allowed text-gray-700 dark:text-gray-300">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
    </div>
@endif
