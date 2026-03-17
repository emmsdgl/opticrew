<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
        <x-applicant-components.applicant-sidebar>
            @isset($sidebarBottom)
                <x-slot:bottom>{{ $sidebarBottom }}</x-slot:bottom>
            @endisset
        </x-applicant-components.applicant-sidebar>
    @endslot

    <div class="flex flex-1 min-h-0 bg-[#EFF5FF] dark:bg-gray-900">

        {{-- ── Middle Content ───────────────────────────────────────────────── --}}
        <section class="flex-1 min-w-0 p-4 md:p-6 flex flex-col gap-4 overflow-y-auto">
            {{ $slot }}
        </section>

        {{-- ── Right Bar (profile card + filters) ─────────────────────────── --}}
        @if(isset($rightBar) || isset($rightFilters))
        <aside class="hidden lg:flex flex-col w-72 flex-shrink-0
                      border-l border-gray-200/70 dark:border-gray-700/50
                      bg-white dark:bg-[#1E293B]
                      overflow-y-auto">
            @isset($rightBar)
                {{ $rightBar }}
            @endisset
            @isset($rightFilters)
                <div class="px-4 pt-2 pb-6">
                    {{ $rightFilters }}
                </div>
            @endisset
        </aside>
        @endif

    </div>
    @stack('scripts')

</x-layouts.general-dashboard>
