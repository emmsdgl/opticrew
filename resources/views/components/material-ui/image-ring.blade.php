@props([
    'images' => [],
    'width' => 280,
    'height' => 380,
    'perspective' => 2000,
    'imageDistance' => 500,
    'initialRotation' => 0,
    'animationDuration' => 1500,
    'staggerDelay' => 100,
    'hoverOpacity' => 0.5,
    'draggable' => true,
    'autoRotate' => true,
    'autoRotateSpeed' => 0.15,
    'mobileBreakpoint' => 768,
    'mobileScaleFactor' => 0.75,
    'inertiaPower' => 0.8,
    'inertiaTimeConstant' => 300,
    'inertiaVelocityMultiplier' => 20,
    'containerClass' => '',
    'ringClass' => '',
    'imageClass' => '',
])

@php
    $id = 'ring-' . uniqid();
    // Stage needs to be wide enough to show the full ring diameter
    $stageWidth = ($imageDistance * 2) + $width;
@endphp

{{-- Outer: full-width, clips overflow on sides --}}
<div id="{{ $id }}" class="w-full select-none relative overflow-hidden {{ $containerClass }}" style="height: {{ $height + 80 }}px;">
    {{-- Stage: fixed size, perfectly centered, holds perspective --}}
    <div id="{{ $id }}-stage"
         style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); perspective: {{ $perspective }}px; width: {{ $stageWidth }}px; height: {{ $height }}px;">
        {{-- Ring: centered within stage, card-sized anchor for 3D transforms --}}
        <div id="{{ $id }}-ring" class="{{ $ringClass }}"
             style="transform-style: preserve-3d; cursor: {{ $draggable ? 'grab' : 'default' }}; width: {{ $width }}px; height: {{ $height }}px; position: absolute; left: 50%; top: 0; margin-left: -{{ $width / 2 }}px;">
        </div>
    </div>
</div>

<script>
(function() {
    const cfg = {
        id: '{{ $id }}',
        images: @json($images),
        width: {{ $width }},
        height: {{ $height }},
        imageDistance: {{ $imageDistance }},
        initialRotation: {{ $initialRotation }},
        animDuration: {{ $animationDuration }},
        staggerDelay: {{ $staggerDelay }},
        hoverOpacity: {{ $hoverOpacity }},
        draggable: {{ $draggable ? 'true' : 'false' }},
        autoRotate: {{ $autoRotate ? 'true' : 'false' }},
        autoSpeed: {{ $autoRotateSpeed }},
        mobileBP: {{ $mobileBreakpoint }},
        mobileScale: {{ $mobileScaleFactor }},
        inertiaPower: {{ $inertiaPower }},
        inertiaTC: {{ $inertiaTimeConstant }},
        inertiaVM: {{ $inertiaVelocityMultiplier }},
        imageClass: '{{ $imageClass }}',
    };

    function init() {
        const container = document.getElementById(cfg.id);
        const stage = document.getElementById(cfg.id + '-stage');
        const ring = document.getElementById(cfg.id + '-ring');
        if (!ring || cfg.images.length === 0) return;

        const n = cfg.images.length;
        const angle = 360 / n;
        let rotation = cfg.initialRotation;
        let scale = 1;
        let isDragging = false;
        let startX = 0, lastX = 0, lastTime = 0;
        let velocity = 0;
        let inertiaId = null;
        let autoId = null;

        // Responsive scaling
        function checkScale() {
            const prev = scale;
            scale = window.innerWidth <= cfg.mobileBP ? cfg.mobileScale : 1;
            if (prev !== scale) {
                stage.style.transform = `translate(-50%, -50%) scale(${scale})`;
                updateCards();
            }
        }
        window.addEventListener('resize', checkScale);
        checkScale();

        // Parallax background position
        function getBgPos(i) {
            const dist = cfg.imageDistance;
            const effectiveRot = rotation - i * angle;
            const parallax = (((effectiveRot % 360) + 360) % 360) / 360;
            return `${-(parallax * (dist / 1.5))}px 0px`;
        }

        // Create cards with staggered entrance
        const cards = [];
        cfg.images.forEach((src, i) => {
            const card = document.createElement('div');
            card.className = 'absolute overflow-hidden shadow-xl ' + cfg.imageClass;
            const dist = cfg.imageDistance;
            card.style.cssText = `
                width: ${cfg.width}px;
                height: ${cfg.height}px;
                transform-style: preserve-3d;
                backface-visibility: hidden;
                background-image: url(${src});
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
                border-radius: 1.25rem;
                transform: rotateY(${i * -angle}deg) translateZ(${-dist}px);
                transform-origin: 50% 50% ${dist}px;
                transition: opacity 0.3s ease;
                opacity: 0;
                translate: 0 200px;
            `;
            ring.appendChild(card);
            cards.push(card);

            // Staggered entrance animation
            setTimeout(() => {
                card.style.transition = `opacity ${cfg.animDuration}ms cubic-bezier(0.16, 1, 0.3, 1), translate ${cfg.animDuration}ms cubic-bezier(0.16, 1, 0.3, 1), background-position 0s`;
                card.style.opacity = '1';
                card.style.translate = '0 0';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.3s ease';
                }, cfg.animDuration + 50);
            }, i * cfg.staggerDelay + 50);
        });

        function updateCards() {
            ring.style.transform = `rotateY(${rotation}deg)`;
            cards.forEach((card, i) => {
                card.style.backgroundPosition = getBgPos(i);
            });
        }
        updateCards();

        // Auto-rotate
        if (cfg.autoRotate) {
            function autoSpin() {
                if (!isDragging && !inertiaId) {
                    rotation += cfg.autoSpeed;
                    updateCards();
                }
                autoId = requestAnimationFrame(autoSpin);
            }
            autoId = requestAnimationFrame(autoSpin);
        }

        // Drag
        if (cfg.draggable) {
            function onStart(e) {
                isDragging = true;
                const cx = e.touches ? e.touches[0].clientX : e.clientX;
                startX = cx; lastX = cx; lastTime = Date.now();
                velocity = 0;
                ring.style.cursor = 'grabbing';
                if (inertiaId) { cancelAnimationFrame(inertiaId); inertiaId = null; }
                document.addEventListener('mousemove', onMove);
                document.addEventListener('mouseup', onEnd);
                document.addEventListener('touchmove', onMove, { passive: true });
                document.addEventListener('touchend', onEnd);
            }

            function onMove(e) {
                if (!isDragging) return;
                const cx = e.touches ? e.touches[0].clientX : e.clientX;
                const now = Date.now();
                const dt = Math.max(now - lastTime, 1);
                const dx = cx - lastX;
                velocity = (dx / dt) * 15;
                rotation += dx * 0.35;
                updateCards();
                lastX = cx; lastTime = now;
            }

            function onEnd() {
                isDragging = false;
                ring.style.cursor = 'grab';
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onEnd);
                document.removeEventListener('touchmove', onMove);
                document.removeEventListener('touchend', onEnd);

                const initialVelocity = velocity * cfg.inertiaVM / 20;
                const power = cfg.inertiaPower;
                const timeConstant = cfg.inertiaTC;
                const startRotation = rotation;
                const startTime = performance.now();

                const projected = startRotation + initialVelocity * power;
                const snapTarget = Math.round(projected / angle) * angle;

                function step(now) {
                    const elapsed = now - startTime;
                    const progress = 1 - Math.exp(-elapsed / timeConstant);
                    rotation = startRotation + (snapTarget - startRotation) * progress;
                    updateCards();

                    if (progress < 0.998) {
                        inertiaId = requestAnimationFrame(step);
                    } else {
                        rotation = snapTarget;
                        updateCards();
                        inertiaId = null;
                    }
                }
                inertiaId = requestAnimationFrame(step);
            }

            container.addEventListener('mousedown', onStart);
            container.addEventListener('touchstart', onStart, { passive: true });
        }

        // Hover effect
        ring.addEventListener('mouseover', (e) => {
            const card = e.target.closest('[style*="background-image"]');
            if (!card || isDragging) return;
            cards.forEach(c => { c.style.opacity = c === card ? '1' : String(cfg.hoverOpacity); });
        });
        ring.addEventListener('mouseleave', () => {
            cards.forEach(c => { c.style.opacity = '1'; });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        requestAnimationFrame(init);
    }
})();
</script>
