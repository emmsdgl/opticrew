@props([
    'logo' => '/public/images/finnoys-text-logo.svg',
    'logoAlt' => 'Logo',
    'companyName' => 'Company',
    'navItems' => [],
    'showAuth' => false,
    'loginRoute' => '#',
    'transparent' => false
])

<header class="inset-x-0 top-0 z-50 {{ $transparent ? 'absolute' : '' }}">
    <nav aria-label="Global" class="flex items-center justify-between p-6 lg:px-8">
        <div class="flex lg:flex-1">
            <a href="/" class="-m-1.5 p-1.5">
                <span class="sr-only">{{ $companyName }}</span>
                <img src="{{ $logo }}" alt="{{ $logoAlt }}" class="h-20 w-auto">
            </a>
        </div>
        
        <!-- Mobile menu button -->
        <div class="flex lg:hidden">
            <button type="button" command="show-modal" commandfor="mobile-menu"
                class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-200">
                <span class="sr-only">Open main menu</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon"
                    aria-hidden="true" class="size-6">
                    <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
        </div>
        
        <!-- Desktop Navigation -->
        <div class="hidden lg:flex lg:gap-x-12">
            @foreach($navItems as $item)
                <a href="{{ $item['url'] ?? '#' }}" 
                   class="text-sm/6 text-blue-950 hover:text-blue-600 hover:font-bold {{ ($item['active'] ?? false) ? 'font-bold text-blue-600' : '' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
        
        <div class="hidden lg:flex lg:flex-1 lg:justify-end">
            @if($showAuth)
                <a href="{{ $loginRoute }}" class="text-sm/6 text-blue-950 hover:text-blue-600 hover:font-bold">
                    Log in <span aria-hidden="true">&rarr;</span>
                </a>
            @endif
        </div>
    </nav>
    
    <!-- Mobile menu -->
    <el-dialog>
        <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
            <div tabindex="0" class="fixed inset-0 focus:outline-none">
                <el-dialog-panel
                    class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-blue-100 bg-blend-color-multiply p-6 sm:max-w-sm sm:ring-1 sm:ring-gray-100/10">
                    <div class="flex items-center justify-between">
                        <a href="/" class="-m-1.5 p-1.5">
                            <span class="sr-only">{{ $companyName }}</span>
                            <img src="{{ $logo }}" alt="{{ $logoAlt }}" class="h-20 w-auto">
                        </a>
                        <button type="button" command="close" commandfor="mobile-menu"
                            class="-m-2.5 rounded-md p-2.5 text-gray-200">
                            <span class="sr-only">Close menu</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                data-slot="icon" aria-hidden="true" class="size-6">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-white/10">
                            <div class="space-y-2 py-6">
                                @foreach($navItems as $item)
                                    <a href="{{ $item['url'] ?? '#' }}"
                                        class="-mx-3 block rounded-lg px-3 py-2 text-base/7 text-blue-950 hover:bg-blue-600/10 {{ ($item['active'] ?? false) ? 'bg-blue-600/10 font-bold' : '' }}">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                            @if($showAuth)
                                <div class="py-6">
                                    <a href="{{ $loginRoute }}"
                                        class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 text-blue-950 hover:bg-blue-600/10">
                                        Log in
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>
</header>