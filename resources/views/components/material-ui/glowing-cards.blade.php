@props([
    'glowRadius' => 25,
    'glowOpacity' => 1,
    'animationDuration' => 400,
    'containerClass' => '',
])

@php
    $id = 'glow-' . uniqid();
@endphp

<div id="{{ $id }}" class="relative w-full {{ $containerClass }}">
    <div id="{{ $id }}-container" class="relative">
        {{-- Main cards layer --}}
        <div id="{{ $id }}-cards" class="flex items-center justify-center flex-wrap">
            {{ $slot }}
        </div>

        {{-- Glow overlay (cloned cards with glow colors, masked by cursor position) --}}
        <div id="{{ $id }}-overlay"
             class="absolute inset-0 pointer-events-none select-none opacity-0 transition-opacity"
             style="transition-duration: {{ $animationDuration }}ms;">
        </div>
    </div>
</div>

<script>
(function() {
    const id = '{{ $id }}';
    const glowRadius = {{ $glowRadius }};
    const glowOpacity = {{ $glowOpacity }};

    function init() {
        const container = document.getElementById(id + '-container');
        const overlay = document.getElementById(id + '-overlay');
        const cardsWrap = document.getElementById(id + '-cards');
        if (!container || !overlay || !cardsWrap) return;

        // Clone the cards into the overlay with glow styling
        function buildOverlay() {
            overlay.innerHTML = '';
            const clone = cardsWrap.cloneNode(true);
            clone.removeAttribute('id');

            // Apply glow border/bg to each card in the clone
            const cards = clone.querySelectorAll('[data-glow-color]');
            cards.forEach(card => {
                const color = card.getAttribute('data-glow-color') || '#3b82f6';
                card.style.backgroundColor = color + '15';
                card.style.borderColor = color;
                card.style.boxShadow = '0 0 0 1px inset ' + color;
            });

            overlay.appendChild(clone);
        }

        buildOverlay();

        // Rebuild overlay when cards expand/collapse to stay in sync
        const observer = new MutationObserver(() => {
            requestAnimationFrame(buildOverlay);
        });
        observer.observe(cardsWrap, { attributes: true, subtree: true, attributeFilter: ['class'] });

        // Mouse tracking for radial mask
        container.addEventListener('mousemove', (e) => {
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const mask = `radial-gradient(${glowRadius}rem ${glowRadius}rem at ${x}px ${y}px, #000 1%, transparent 50%)`;
            overlay.style.WebkitMask = mask;
            overlay.style.mask = mask;
            overlay.style.opacity = glowOpacity;
        });

        container.addEventListener('mouseleave', () => {
            overlay.style.opacity = '0';
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        requestAnimationFrame(init);
    }
})();
</script>
