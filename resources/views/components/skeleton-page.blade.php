{{--
    Skeleton Page Wrapper
    Usage: <x-skeleton-page :preset="'dashboard'"> ... actual content ... </x-skeleton-page>

    Wraps page content with a skeleton loader that shows on initial load,
    then fades out to reveal the real content.
--}}
@props([
    'preset' => 'default',  // dashboard, table, cards, calendar, chart, list, stats-table, two-panel
])

<div x-data="{ skeletonLoaded: false }"
     x-init="$nextTick(() => { setTimeout(() => skeletonLoaded = true, 300) })"
     class="w-full flex-1">

    {{-- Skeleton Overlay - shown immediately at full width --}}
    <div x-show="!skeletonLoaded" class="w-full">
        @switch($preset)
            @case('dashboard')
                <x-skeletons.dashboard />
                @break
            @case('table')
                <x-skeletons.table />
                @break
            @case('cards')
                <x-skeletons.card-grid />
                @break
            @case('calendar')
                <x-skeletons.calendar />
                @break
            @case('chart')
                <x-skeletons.chart />
                @break
            @case('list')
                <x-skeletons.list />
                @break
            @case('stats-table')
                <x-skeletons.stats-table />
                @break
            @case('two-panel')
                <x-skeletons.two-panel />
                @break
            @case('recruitment')
                <x-skeletons.recruitment />
                @break
            @case('history')
                <x-skeletons.history />
                @break
            @case('training')
                <x-skeletons.training />
                @break
            @case('employee-dashboard')
                <x-skeletons.employee-dashboard />
                @break
            @case('client-dashboard')
                <x-skeletons.client-dashboard />
                @break
            @case('performance')
                <x-skeletons.performance />
                @break
            @case('profile')
                <x-skeletons.profile />
                @break
            @default
                <x-skeletons.default-skeleton />
        @endswitch
    </div>

    {{-- Actual Content - hidden until skeleton finishes, no transitions that affect layout --}}
    <div x-show="skeletonLoaded" x-cloak
         style="display: none;"
         class="w-full">
        {{ $slot }}
    </div>
</div>
