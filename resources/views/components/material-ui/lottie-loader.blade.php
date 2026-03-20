@props([
    'src' => null,
    'size' => 120,
    'text' => 'Loading',
    'textClass' => 'text-blue-400 dark:text-blue-300',
    'color' => '#3b82f6',
])

@php
    $jsonUrl = $src ?: asset('images/icons/loader/loading-character.json');
    $containerId = 'lottie-' . uniqid();
@endphp

<div class="flex flex-col items-center justify-center gap-1">
    <div id="{{ $containerId }}" style="width: {{ $size }}px; height: {{ $size }}px;"></div>

    @if($text)
        <span class="text-xs font-medium {{ $textClass }} tracking-wide animate-pulse">{{ $text }}</span>
    @endif
</div>

<script>
(function() {
    function initLottie_{{ str_replace('-', '_', $containerId) }}() {
        if (typeof lottie === 'undefined') return false;
        lottie.loadAnimation({
            container: document.getElementById('{{ $containerId }}'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '{{ $jsonUrl }}'
        });
        return true;
    }
    // Try immediately, or wait for script to load
    if (!initLottie_{{ str_replace('-', '_', $containerId) }}()) {
        document.addEventListener('lottie-ready', function() {
            initLottie_{{ str_replace('-', '_', $containerId) }}();
        });
    }
})();
</script>

@once
<script>
(function() {
    if (typeof lottie !== 'undefined') {
        document.dispatchEvent(new Event('lottie-ready'));
        return;
    }
    var s = document.createElement('script');
    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js';
    s.onload = function() {
        document.dispatchEvent(new Event('lottie-ready'));
    };
    document.head.appendChild(s);
})();
</script>
@endonce
