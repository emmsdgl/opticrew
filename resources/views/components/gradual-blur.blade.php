@props([
    'position' => 'bottom',
    'intensity' => 1,
    'height' => '140px',
    'layers' => 8,
    'fixed' => false,
])

@php
    $positionClasses = match($position) {
        'top'    => 'top-0 left-0 right-0',
        'bottom' => 'bottom-0 left-0 right-0',
        default  => 'bottom-0 left-0 right-0',
    };
    $fixedClass = $fixed ? 'gradual-blur-fixed' : 'absolute';
    $zIndex = $fixed ? 'z-[60]' : 'z-30';
@endphp

<div class="gradual-blur {{ $fixedClass }} {{ $positionClasses }} {{ $zIndex }} pointer-events-none"
    style="height: {{ $height }};"
    aria-hidden="true">
    <div class="gradual-blur-inner" style="width: 100%; height: 100%; position: relative;">
        @for ($i = 0; $i < $layers; $i++)
            @php
                $progress = $i / max($layers - 1, 1);
                $blur = round($progress * 10 * $intensity, 1);
                $opacity = round($progress * 0.6, 2);

                if ($position === 'top') {
                    // Top: full blur at top (0%), clear at bottom (100%)
                    $topPct = round((1 - $progress - 1/$layers) * 100);
                    $heightPct = round(100 / $layers) + 1;
                    $maskDir = 'to bottom';
                } else {
                    // Bottom: clear at top (0%), full blur at bottom (100%)
                    $topPct = round($progress * 100);
                    $heightPct = round(100 / $layers) + 1;
                    $maskDir = 'to top';
                }
            @endphp
            <div style="
                position: absolute;
                left: 0; right: 0;
                top: {{ $topPct }}%;
                height: {{ $heightPct }}%;
                backdrop-filter: blur({{ $blur }}px);
                -webkit-backdrop-filter: blur({{ $blur }}px);
                mask-image: linear-gradient({{ $maskDir }}, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 100%);
                -webkit-mask-image: linear-gradient({{ $maskDir }}, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 100%);
            "></div>
        @endfor
    </div>
</div>
