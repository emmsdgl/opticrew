@props([
    'text'         => '',
    'delay'        => 250,
    'animateBy'    => 'words',
    'direction'    => 'top',
    'stepDuration' => 0.45,
])

@php
    // Decode HTML entities (e.g. &#039; from Blade-escaped attribute values) before splitting
    $decodedText = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $elements  = $animateBy === 'words'
        ? explode(' ', trim($decodedText))
        : preg_split('//u', trim($decodedText), -1, PREG_SPLIT_NO_EMPTY);
    $totalDuration = $stepDuration * 2;
    $yFrom     = $direction === 'top' ? -50 : 50;
@endphp

<span {{ $attributes->merge(['class' => 'blur-text-root flex flex-wrap gap-x-[0.25em]']) }}
      data-bt-direction="{{ $direction }}">
    @foreach($elements as $index => $word)
        <span
            class="inline-block will-change-[transform,filter,opacity]"
            data-bt-delay="{{ $index * $delay }}"
            data-bt-duration="{{ $totalDuration }}"
            style="filter:blur(10px);opacity:0;transform:translateY({{ $yFrom }}px);"
        >{{ $word }}</span>
    @endforeach
</span>

@once
<script>
(function () {
    function initBlurText(root) {
        if (root.dataset.btInit) return;
        root.dataset.btInit = '1';

        var direction = root.dataset.btDirection || 'top';
        var yFrom     = direction === 'top' ? -50 : 50;
        var yMid      = direction === 'top' ?   5 : -5;
        var spans     = root.querySelectorAll('[data-bt-delay]');

        function animateAll() {
            spans.forEach(function (span) {
                var delay    = parseFloat(span.dataset.btDelay    || 0);
                var duration = parseFloat(span.dataset.btDuration || 0.7) * 1000;
                span.animate([
                    { filter: 'blur(10px)', opacity: 0,   transform: 'translateY(' + yFrom + 'px)' },
                    { filter: 'blur(5px)',  opacity: 0.5, transform: 'translateY(' + yMid  + 'px)' },
                    { filter: 'blur(0px)',  opacity: 1,   transform: 'translateY(0px)'              }
                ], { duration: duration, delay: delay, fill: 'forwards', easing: 'ease' });
            });
        }

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateAll();
                    io.unobserve(root);
                }
            });
        }, { threshold: 0.1 });

        io.observe(root);
    }

    function initAll() {
        document.querySelectorAll('.blur-text-root:not([data-bt-init])').forEach(initBlurText);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    window.__blurTextInitAll = initAll;
})();
</script>
@endonce
