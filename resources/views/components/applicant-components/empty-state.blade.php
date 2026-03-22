@props([
    'icon'        => 'fa-solid fa-folder-plus',
    'title'       => 'No items',
    'description' => 'Get started by creating a new item.',
    'buttonText'  => null,
    'buttonIcon'  => 'fa-solid fa-plus',
    'buttonClick' => null,
    'buttonHref'  => null,
])

<div class="flex flex-col items-center justify-center py-16 px-6 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 transition-colors">
    {{-- Icon container --}}
    <div class="flex items-center justify-center w-14 h-14 rounded-xl bg-gray-100 dark:bg-gray-700/60 mb-4">
        <i class="{{ $icon }} text-xl text-gray-400 dark:text-gray-500"></i>
    </div>

    {{-- Title --}}
    <p class="text-sm font-bold text-gray-700 dark:text-white mb-1">{{ $title }}</p>

    {{-- Description --}}
    <p class="text-xs text-gray-400 dark:text-gray-500 text-center max-w-xs">{{ $description }}</p>

    {{-- Action button --}}
    @if($buttonText)
        @if($buttonHref)
            <a href="{{ $buttonHref }}"
                class="mt-4 inline-flex items-center gap-1.5 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                <i class="{{ $buttonIcon }} text-[10px]"></i>
                {{ $buttonText }}
            </a>
        @elseif($buttonClick)
            <button type="button" @click="{{ $buttonClick }}"
                class="mt-4 inline-flex items-center gap-1.5 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                <i class="{{ $buttonIcon }} text-[10px]"></i>
                {{ $buttonText }}
            </button>
        @endif
    @endif
</div>
