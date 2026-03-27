<x-layouts.general-dashboard :title="$title">

    @slot('sidebar')
        <x-applicant-components.applicant-sidebar>
            @isset($sidebarBottom)
                <x-slot:bottom>{{ $sidebarBottom }}</x-slot:bottom>
            @endisset
        </x-applicant-components.applicant-sidebar>
    @endslot

    @php $hasRightPanel = isset($rightBar) || isset($rightFilters); @endphp
    <div class="flex-1 min-h-0 overflow-hidden bg-[#EFF5FF] dark:bg-gray-900 relative">

        {{-- ── Middle Content ───────────────────────────────────────────────── --}}
        <section class="h-full min-w-0 p-4 md:p-6 flex flex-col gap-4 overflow-y-auto overflow-x-hidden
                        {{ $hasRightPanel ? 'lg:pr-[19rem]' : '' }}">
            {{ $slot }}
        </section>

        {{-- ── Right Bar (profile card + filters) ─────────────────────────── --}}
        @if($hasRightPanel)
        <aside class="hidden lg:flex lg:flex-col
                      absolute top-0 right-0 bottom-0 w-72
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
