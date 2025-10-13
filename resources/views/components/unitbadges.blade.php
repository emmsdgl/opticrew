@props([
    'rooms' => []
])

<div class="unitbadges flex flex-row flex-wrap w-full gap-2 overflow-hidden">
    @foreach ($rooms as $room)
        <span class="badge bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300 whitespace-nowrap">
            {{ $room }}
        </span>
    @endforeach
    <span class="extraCount hidden text-gray-500 text-xs font-medium px-2.5 py-0.5 rounded-sm bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
        +0
    </span>
</div>

@push('scripts')
<script>
function initUnitBadges() {
    document.querySelectorAll(".unitbadges").forEach(container => {
        if (container.dataset.initialized === "true") return; // avoid re-running on same instance
        container.dataset.initialized = "true";

        const badges = container.querySelectorAll(".badge");
        const extra = container.querySelector(".extraCount");
        if (!badges.length || !extra) return;

        function updateVisibleBadges() {
            // Skip hidden containers (e.g., inside x-show=false)
            if (container.offsetParent === null) return;

            badges.forEach(b => b.classList.remove("hidden"));
            extra.classList.add("hidden");
            extra.textContent = "";

            const containerWidth = container.offsetWidth;
            let totalWidth = 0;
            let hiddenCount = 0;

            badges.forEach(badge => {
                totalWidth += badge.offsetWidth + 8;
                if (totalWidth > containerWidth) {
                    badge.classList.add("hidden");
                    hiddenCount++;
                }
            });

            if (hiddenCount > 0) {
                extra.textContent = `+${hiddenCount}`;
                extra.classList.remove("hidden");
            }
        }

        // Run on next tick (ensures visibility after Alpine renders)
        queueMicrotask(updateVisibleBadges);

        // Recalculate on window resize
        window.addEventListener("resize", updateVisibleBadges);

        // Recalculate when container becomes visible (e.g. modal opens)
        const observer = new MutationObserver(() => {
            if (container.offsetParent !== null) updateVisibleBadges();
        });
        observer.observe(container, { attributes: true, attributeFilter: ['class', 'style'] });
    });
}

// Run on page load
document.addEventListener("DOMContentLoaded", initUnitBadges);

// Also re-init after Alpine DOM updates
document.addEventListener("alpine:init", () => {
    Alpine.effect(() => queueMicrotask(initUnitBadges));
});
</script>
@endpush
