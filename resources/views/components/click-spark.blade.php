@props([
    'sparkColor' => null,
    'sparkSize' => 10,
    'sparkRadius' => 15,
    'sparkCount' => 8,
    'duration' => 400,
    'easing' => 'ease-out',
    'extraScale' => 1.0,
])

<canvas id="click-spark-canvas" style="position:fixed;top:0;left:0;width:100vw;height:100vh;pointer-events:none;z-index:99999;"></canvas>

<script>
(function() {
    if (window.__clickSparkInit) return;
    window.__clickSparkInit = true;

    const canvas = document.getElementById('click-spark-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const config = {
        sparkSize: {{ $sparkSize }},
        sparkRadius: {{ $sparkRadius }},
        sparkCount: {{ $sparkCount }},
        duration: {{ $duration }},
        easing: '{{ $easing }}',
        extraScale: {{ $extraScale }},
    };

    function getSparkColor() {
        @if($sparkColor)
            return '{{ $sparkColor }}';
        @else
            return document.documentElement.classList.contains('dark') ? '#ffffff' : '#1e293b';
        @endif
    }

    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(resize, 100);
    });
    resize();

    function easeFn(t) {
        switch (config.easing) {
            case 'linear': return t;
            case 'ease-in': return t * t;
            case 'ease-in-out': return t < 0.5 ? 2*t*t : -1 + (4 - 2*t)*t;
            default: return t * (2 - t);
        }
    }

    const sparks = [];

    function draw(timestamp) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        for (let i = sparks.length - 1; i >= 0; i--) {
            const spark = sparks[i];
            const elapsed = timestamp - spark.startTime;
            if (elapsed >= config.duration) {
                sparks.splice(i, 1);
                continue;
            }

            const progress = elapsed / config.duration;
            const eased = easeFn(progress);
            const color = spark.color;

            const distance = eased * config.sparkRadius * config.extraScale;
            const lineLength = config.sparkSize * (1 - eased);

            const x1 = spark.x + distance * Math.cos(spark.angle);
            const y1 = spark.y + distance * Math.sin(spark.angle);
            const x2 = spark.x + (distance + lineLength) * Math.cos(spark.angle);
            const y2 = spark.y + (distance + lineLength) * Math.sin(spark.angle);

            ctx.strokeStyle = color;
            ctx.lineWidth = 2;
            ctx.globalAlpha = 1 - eased;
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();
        }

        ctx.globalAlpha = 1;
        requestAnimationFrame(draw);
    }

    requestAnimationFrame(draw);

    document.addEventListener('click', function(e) {
        const now = performance.now();
        const color = getSparkColor();
        for (let i = 0; i < config.sparkCount; i++) {
            sparks.push({
                x: e.clientX,
                y: e.clientY,
                angle: (2 * Math.PI * i) / config.sparkCount,
                startTime: now,
                color: color,
            });
        }
    });
})();
</script>
