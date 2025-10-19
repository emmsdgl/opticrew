@props([
    'totalEmployees' => 0,
    'presentEmployees' => 0,
    'absentEmployees' => 0,
    'attendanceRate' => 0,
])

<div class="mx-auto p-6 flex flex-col items-center space-y-10">
    <!-- Gauge Container -->
    <div id="gauge-{{ uniqid() }}" class="gauge-container relative w-56 h-28 flex items-center justify-center" 
         data-rate="{{ $attendanceRate }}">
        <svg viewBox="0 0 200 100" class="w-full h-full">
            <g class="segments"></g>
        </svg>
        <div class="absolute bottom-0 flex flex-col items-center translate-y-6">
            <span class="percentage-text text-3xl font-semibold text-gray-900 dark:text-gray-100">0%</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">Attendance Rate</span>
        </div>
    </div>

    <!-- Progress Details -->
    <div class="w-full space-y-6">
        <div>
            <div class="flex mb-3 justify-between text-sm text-gray-600 dark:text-gray-300">
                <span>Present</span>
                <span>{{ $presentEmployees }}/{{ $totalEmployees }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-500" 
                     style="width: {{ $attendanceRate }}%;"></div>
            </div>
        </div>

        <div>
            <div class="flex mb-3 justify-between text-sm text-gray-600 dark:text-gray-300">
                <span>Expected Workforce</span>
                <span>{{ $totalEmployees }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-900 h-2.5 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        <div>
            <div class="flex mb-3 justify-between text-sm text-gray-600 dark:text-gray-300">
                <span>Absent</span>
                <span>{{ $absentEmployees }}/{{ $totalEmployees }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-400 h-2.5 rounded-full transition-all duration-500" 
                     style="width: {{ $totalEmployees > 0 ? ($absentEmployees / $totalEmployees) * 100 : 0 }}%;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function initGauge(container) {
        const gauge = container.querySelector('.segments');
        const percentageText = container.querySelector('.percentage-text');
        const attendanceRate = parseFloat(container.dataset.rate) || 0;

        // Clear existing segments
        gauge.innerHTML = '';

        const totalSegments = 14;
        const outerRadius = 90;
        const innerRadius = 70;
        const angleStep = Math.PI / (totalSegments - 0.3);

        // Build each segment
        for (let i = 0; i < totalSegments; i++) {
            const angle = Math.PI - i * angleStep;
            const centerX = 100;
            const centerY = 100;
            const angleOffset = 0.08;

            // Outer arc points
            const x1 = centerX + outerRadius * Math.cos(angle - angleOffset);
            const y1 = centerY - outerRadius * Math.sin(angle - angleOffset);
            const x2 = centerX + outerRadius * Math.cos(angle + angleOffset);
            const y2 = centerY - outerRadius * Math.sin(angle + angleOffset);

            // Inner arc points
            const x3 = centerX + innerRadius * Math.cos(angle + angleOffset);
            const y3 = centerY - innerRadius * Math.sin(angle + angleOffset);
            const x4 = centerX + innerRadius * Math.cos(angle - angleOffset);
            const y4 = centerY - innerRadius * Math.sin(angle - angleOffset);

            const pathData = `
                M ${x1} ${y1}
                L ${x2} ${y2}
                L ${x3} ${y3}
                L ${x4} ${y4}
                Z
            `;

            const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
            path.setAttribute("d", pathData);
            path.setAttribute("fill", "#E5E7EB");
            path.setAttribute("stroke-linejoin", "round");
            path.setAttribute("stroke-linecap", "round");
            gauge.appendChild(path);
        }

        // Animate segments fill
        const fillSegments = Math.round((attendanceRate / 100) * totalSegments);
        let current = 0;

        const interval = setInterval(() => {
            if (current < fillSegments) {
                const path = gauge.children[current];
                const hue = 217;
                const lightness = 75 - (current * 30) / totalSegments;
                path.setAttribute("fill", `hsl(${hue}, 95%, ${lightness}%)`);
                current++;
            } else {
                clearInterval(interval);
            }

            const displayedPercent = Math.min(
                (current / totalSegments) * 100,
                attendanceRate
            );

            if (current >= fillSegments) {
                percentageText.textContent = `${attendanceRate.toFixed(1)}%`;
            } else {
                percentageText.textContent = `${displayedPercent.toFixed(1)}%`;
            }
        }, 100);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.gauge-container').forEach(initGauge);
    });

    // Re-initialize when Livewire updates (THIS IS THE KEY!)
    document.addEventListener('livewire:load', function() {
        Livewire.hook('message.processed', (message, component) => {
            // Re-init all gauges after Livewire updates
            document.querySelectorAll('.gauge-container').forEach(initGauge);
        });
    });

    // For Livewire v3 (if you're using it)
    document.addEventListener('livewire:initialized', () => {
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            succeed(({ snapshot, effect }) => {
                // Re-init gauges after commit
                setTimeout(() => {
                    document.querySelectorAll('.gauge-container').forEach(initGauge);
                }, 50);
            });
        });
    });
</script>