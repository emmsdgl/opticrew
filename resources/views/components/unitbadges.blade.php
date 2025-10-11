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
document.addEventListener("DOMContentLoaded", function () {
    // Select all unitbadges containers
    const containers = document.querySelectorAll(".unitbadges");

    containers.forEach(container => {
        const badges = container.querySelectorAll(".badge");
        const extra = container.querySelector(".extraCount");

        function updateVisibleBadges() {
            // Reset
            badges.forEach(b => b.classList.remove("hidden"));
            extra.classList.add("hidden");
            extra.textContent = "";

            let containerWidth = container.offsetWidth;
            let totalWidth = 0;
            let hiddenCount = 0;

            badges.forEach(badge => {
                totalWidth += badge.offsetWidth + 8; // include margin/gap
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

        // Run on load and resize
        updateVisibleBadges();
        window.addEventListener("resize", updateVisibleBadges);
    });
});
</script>
@endpush
