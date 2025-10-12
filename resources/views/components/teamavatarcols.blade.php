@props([
    'teamName' => null,
    'members' => [] 
])

<div class="flex flex-col items-end space-y-2">
  <h2 class="text-xs font-semibold text-gray-900 dark:text-white">{{ $teamName }}</h2>

  <div class="avatar-row flex -space-x-3 relative">
    @foreach ($members as $member)
      <img 
        class="avatar w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 object-cover" 
        src="{{ asset('images/people/' . $member . '.svg') }}" 
        alt="Team Member"
      >
    @endforeach

    <span class="extra-count relative z-20 inline-flex items-center justify-center h-8 min-w-[2rem] px-2 text-xs font-medium leading-none rounded-full bg-gray-100 border-2 border-white dark:bg-gray-700 dark:border-gray-800">
      +0
    </span>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Select all team avatar groups
    document.querySelectorAll(".avatar-row").forEach((row) => {
        const avatars = row.querySelectorAll(".avatar");
        const extra = row.querySelector(".extra-count");

        function updateVisibleAvatars() {
            avatars.forEach(a => a.classList.remove("hidden"));
            extra.classList.add("hidden");
            extra.textContent = "";

            const rowWidth = row.offsetWidth;
            let totalWidth = 0;
            let hiddenCount = 0;

            const visibleLimit = 4; 

            avatars.forEach((avatar, i) => {
                if (i < visibleLimit) {
                    avatar.classList.remove("hidden");
                } else {
                    avatar.classList.add("hidden");
                    hiddenCount++;
                }
            });

            if (hiddenCount > 0) {
                extra.textContent = `+${hiddenCount}`;
                extra.classList.remove("hidden");
            }
        }

        updateVisibleAvatars();
        window.addEventListener("resize", updateVisibleAvatars);
    });
});
</script>
@endpush
