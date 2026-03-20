@props([
    'size' => 80,
    'text' => 'Loading',
    'icon' => null,
    'logoSrc' => null,
    'color' => 'rgba(100,100,100,0.6)',
])

@php
    $uniqueId = 'ripple-' . uniqid();
    // Parse base RGB from color prop for ring tinting
    $isBlue = str_contains($color, '59,130,246') || str_contains($color, 'blue');
    $r = $isBlue ? 59 : 100;
    $g = $isBlue ? 130 : 100;
    $b = $isBlue ? 246 : 100;
@endphp

<div class="flex flex-col items-center justify-center gap-3" id="{{ $uniqueId }}">
    <div class="relative" style="width: {{ $size }}px; height: {{ $size }}px;">
        {{-- Ripple rings --}}
        @for($i = 0; $i < 5; $i++)
            @php
                $inset = 40 - ($i * 10);
                $opacity = 1 - ($i * 0.2);
                $delay = $i * 0.2;
            @endphp
            <div class="absolute rounded-full border-t backdrop-blur-[5px]"
                 style="
                    inset: {{ $inset }}%;
                    z-index: {{ 99 - $i }};
                    border-color: rgba({{ $r }},{{ $g }},{{ $b }},{{ $opacity }});
                    background: linear-gradient(0deg, rgba({{ $r }},{{ $g }},{{ $b }},0.15), rgba({{ $r }},{{ $g }},{{ $b }},0.25));
                    animation: ripplePulse 2s {{ $delay }}s ease-in-out infinite;
                 ">
            </div>
        @endfor

        {{-- Center icon (only if logoSrc or icon is provided) --}}
        @if($logoSrc || $icon)
        <div class="absolute inset-0 grid place-content-center" style="z-index: 100; padding: 20%;">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="Loading" class="w-full h-full object-contain ripple-icon-pulse">
            @elseif($icon)
                <i class="{{ $icon }} w-full h-full text-gray-400 ripple-icon-pulse" style="font-size: {{ $size * 0.25 }}px;"></i>
            @endif
        </div>
        @endif
    </div>

    @if($text)
        <span class="text-xs font-medium text-blue-400 dark:text-blue-300 tracking-wide animate-pulse">{{ $text }}</span>
    @endif
</div>

<style>
@keyframes ripplePulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: rgba({{ $r }},{{ $g }},{{ $b }},0.15) 0px 10px 10px 0px;
    }
    50% {
        transform: scale(1.3);
        box-shadow: rgba({{ $r }},{{ $g }},{{ $b }},0.25) 0px 30px 20px 0px;
    }
}

.ripple-icon-pulse {
    animation: iconColorPulse 2s 0.1s ease-in-out infinite;
}

@keyframes iconColorPulse {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 1; }
}
</style>
