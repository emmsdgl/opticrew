@props([
    'totalEmployees' => 0,
    'presentEmployees' => 0,
    'absentEmployees' => 0,
    'attendanceRate' => 0,
])

<div class="mx-auto p-6 flex flex-col items-center space-y-10">
    <!-- Gauge Container -->
    <div id="gauge" class="relative w-56 h-28 flex items-center justify-center">
        <svg viewBox="0 0 200 100" class="w-full h-full">
            <g id="segments"></g>
        </svg>
        <div class="absolute bottom-0 flex flex-col items-center translate-y-6">
            <span id="percentage" class="text-3xl font-semibold text-gray-900 dark:text-gray-100">0%</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">Attendance Rate</span>
        </div>
    </div>

    <!-- Progress Details -->
    <div class="w-full space-y-6">
        <div>
            <div class="flex mb-3 justify-between text-sm text-gray-600 dark:text-gray-300">
                <span>Present</span>
                {{-- DYNAMIC DATA --}}
                <span>{{ $presentEmployees }}/{{ $totalEmployees }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                {{-- DYNAMIC DATA --}}
                <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $attendanceRate }}%;"></div>
            </div>
        </div>

        <div>
            <div class="flex mb-3 justify-between text-sm text-gray-600 dark:text-gray-300">
                <span>Expected Workforce</span>
                {{-- DYNAMIC DATA --}}
                <span>{{ $totalEmployees }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-900 h-2.5 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        <div>
            <div class="flex mb-3 justify-between text-sm text-gray-600 dark:text-gray-300">
                <span>Absent</span>
                {{-- DYNAMIC DATA --}}
                <span>{{ $absentEmployees }}/{{ $totalEmployees }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                {{-- DYNAMIC DATA: Calculate absent percentage --}}
                <div class="bg-blue-400 h-2.5 rounded-full" style="width: {{ $totalEmployees > 0 ? ($absentEmployees / $totalEmployees) * 100 : 0 }}%;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const gauge = document.getElementById("segments");
        const percentageText = document.getElementById("percentage");

        const totalSegments = 14;     // number of wedge blocks
        
        // DYNAMIC DATA: Pass the attendance rate from PHP to JavaScript
        const attendanceRate = @json($attendanceRate ?? 0);

        // Shape parameters
        const outerRadius = 90;   // outer circle radius
        const innerRadius = 70;   // inner circle radius
        const outerWidth = 20;    // width of segment at outer edge (top)
        const innerWidth = 8;     // width of segment at inner edge (bottom)
        const angleStep = Math.PI / (totalSegments - 0.3); // angle gap per segment
        const cornerRadius = 6;
        const outerAngleOffset = 0.05;
        const innerAngleOffset = 0.12;

        // Build each segment
        for (let i = 0; i < totalSegments; i++) {
            const angle = Math.PI - i * angleStep;
            const centerX = 100;
            const centerY = 100;

            const angleOffset = 0.08; // controls curvature of each segment

            // Outer (top) arc points
            const x1 = centerX + outerRadius * Math.cos(angle - angleOffset);
            const y1 = centerY - outerRadius * Math.sin(angle - angleOffset);
            const x2 = centerX + outerRadius * Math.cos(angle + angleOffset);
            const y2 = centerY - outerRadius * Math.sin(angle + angleOffset);

            // Inner (bottom) arc points
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
            path.setAttribute("rx", "25");
            path.setAttribute("ry", "20");
            gauge.appendChild(path);

        }

        // Animate segments fill
        const rate = parseFloat(attendanceRate) || 0;
        const fillSegments = Math.round((rate / 100) * totalSegments);
        let current = 0;

        const interval = setInterval(() => {
            if (current < fillSegments) {
                const path = gauge.children[current];
                const hue = 217; // blue tone
                const lightness = 75 - (current * 30) / totalSegments; // gradient effect
                path.setAttribute("fill", `hsl(${hue}, 95%, ${lightness}%)`);
                current++;
            } else {
                clearInterval(interval);
            }

            const displayedPercent = Math.min(
                (current / totalSegments) * 100,
                rate
            );

            // Ensure final display is the precise rate
            if (current >= fillSegments) {
                percentageText.textContent = `${rate.toFixed(1)}%`;
            } else {
                 percentageText.textContent = `${displayedPercent.toFixed(1)}%`;
            }
        }, 100);
    });
</script>