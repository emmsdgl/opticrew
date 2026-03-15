@props([
    'paginator',
    'onEachSide' => 1,
])

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="mx-auto flex w-full justify-center">
        <ul class="flex flex-row items-center gap-1">

            {{-- Previous --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-400 dark:text-gray-600 cursor-not-allowed rounded-lg">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        <span>Previous</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        <span>Previous</span>
                    </a>
                @endif
            </li>

            {{-- Page Numbers --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $window = $onEachSide;

                $pages = collect();

                // Always show first page
                $pages->push(1);

                // Calculate range around current page
                $rangeStart = max(2, $currentPage - $window);
                $rangeEnd = min($lastPage - 1, $currentPage + $window);

                // Add ellipsis before range if needed
                if ($rangeStart > 2) {
                    $pages->push('...');
                }

                // Add range pages
                for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
                    $pages->push($i);
                }

                // Add ellipsis after range if needed
                if ($rangeEnd < $lastPage - 1) {
                    $pages->push('...');
                }

                // Always show last page (if more than 1 page)
                if ($lastPage > 1) {
                    $pages->push($lastPage);
                }
            @endphp

            @foreach ($pages as $page)
                <li>
                    @if ($page === '...')
                        <span class="flex h-9 w-9 items-center justify-center text-gray-400 dark:text-gray-500" aria-hidden="true">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                            <span class="sr-only">More pages</span>
                        </span>
                    @elseif ($page === $currentPage)
                        <span aria-current="page"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-100 shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                </li>
            @endforeach

            {{-- Next --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <span>Next</span>
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-400 dark:text-gray-600 cursor-not-allowed rounded-lg">
                        <span>Next</span>
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </span>
                @endif
            </li>

        </ul>
    </nav>
@endif
