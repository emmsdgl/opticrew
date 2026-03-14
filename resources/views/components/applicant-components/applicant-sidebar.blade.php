<div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-30 lg:hidden hidden transition-opacity duration-300"></div>

<button id="sidebar-toggle"
    class="hidden lg:block fixed top-6 z-50 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md p-2 shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-300"
    style="left: 296px;">
    <i class="fa-solid fa-bars text-lg"></i>
</button>

<aside id="sidebar"
    class="fixed left-0 top-0 h-screen w-72 z-40
           bg-[#FFFFFF] dark:bg-[#1E293B]
           border-r border-[#D1D1D1] dark:border-[#334155]
           flex flex-col justify-between overflow-y-auto
           transition-all duration-300
           -translate-x-full lg:translate-x-0">

    <div>
        <div id="sidebar-header" class="hidden"></div>

        {{-- ── Logo ── --}}
        <div id="sidebar-header" class="flex items-center h-28 px-6 transition-all duration-300">
            <div class="flex-1 flex items-center justify-center">
                <a href="{{ route('applicant.dashboard') }}" class="flex items-center justify-center">
                    <img src="{{ asset('images/finnoys-text-logo-light.svg') }}"
                        class="block dark:hidden h-24 w-auto sidebar-logo" alt="Finnoys Light Logo">
                    <img src="{{ asset('images/finnoys-text-logo.svg') }}"
                        class="hidden dark:block h-24 w-auto sidebar-logo" alt="Finnoys Dark Logo">
                </a>
            </div>

            {{-- Mobile Close --}}
            <button id="mobile-sidebar-close"
                class="lg:hidden text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md p-2 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── Navigation ── --}}
        @php
            $navItems = [
                ['label' => 'Dashboard', 'icon' => 'fa-solid fa-house', 'href' => route('applicant.dashboard')],
                ['label' => 'Withdrawn', 'icon' => 'fa-solid fa-rotate-left', 'href' => route('applicant.withdrawn')],
                ['label' => 'Terms and Conditions', 'icon' => 'fa-solid fa-file-contract', 'href' => route('termscondition')],
                ['label' => 'Privacy Policy', 'icon' => 'fa-solid fa-shield-halved', 'href' => route('privacypolicy')],
            ];
        @endphp

        <nav class="mt-6 space-y-1 px-5">
            @foreach($navItems as $nav)
                @php
                    $navPath = trim(parse_url($nav['href'], PHP_URL_PATH), '/');
                    $isActive = request()->url() === $nav['href'] || request()->is($navPath) || request()->is($navPath.'/*');
                @endphp
                <a href="{{ $nav['href'] }}"
                    class="flex items-center px-3 py-3.5 text-sm font-medium rounded-lg transition-colors duration-200
                        {{ $isActive
                            ? 'bg-[#2A6DFA] text-white'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-[#2A6DFA] hover:text-white' }}">
                    <i class="{{ $nav['icon'] }} w-5"></i>
                    <span class="ml-8 nav-label sidebar-label">{{ $nav['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    {{-- ── Bottom Slot (Summary, etc.) ── --}}
    @if(isset($bottom))
        <div class="mt-auto">
            {{ $bottom }}
        </div>
    @endif

</aside>
