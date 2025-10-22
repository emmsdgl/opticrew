@props([
])

  <!-- Calendar Container -->
<div class="w-full max-w-parent p-6 px-12 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-transparent">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <p class="text-sm text-gray-600 dark:text-gray-400">Today is</p>
        <h2 id="today" class="text-sm font-semibold"></h2>
      </div>    
      <div class="flex gap-3 text-blue-500">
        <button id="prev-week" class="hover:text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
          </svg>
        </button>
        <button id="next-week" class="hover:text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Weekdays -->
    <div class="grid grid-cols-7 text-center text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
      <div>Sat</div>
      <div>Sun</div>
      <div>Mon</div>
      <div>Tues</div>
      <div>Wed</div>
      <div>Thurs</div>
      <div>Fri</div>
    </div>

    <!-- Dates -->
    <div id="week-days" class="grid grid-cols-7 text-center text-sm font-medium"></div>
  </div>

 <script>
  const todayElement = document.getElementById("today");
  const weekDaysContainer = document.getElementById("week-days");
  const prevWeekBtn = document.getElementById("prev-week");
  const nextWeekBtn = document.getElementById("next-week");
  const html = document.documentElement;

  let currentDate = new Date();
  let selectedDate = new Date();

  function updateTodayLabel() {
    todayElement.textContent = selectedDate.toLocaleDateString("en-US", {
      month: "long",
      day: "numeric",
      year: "numeric",
    });
  }

  function getStartOfWeek(date) {
    const day = date.getDay(); // 0 = Sunday, 6 = Saturday
    const diff = date.getDate() - day - 1; // Start from Saturday (like your layout)
    return new Date(date.setDate(diff));
  }

  function renderWeek(date) {
    weekDaysContainer.innerHTML = "";
    const startOfWeek = getStartOfWeek(new Date(date));

    for (let i = 0; i < 7; i++) {
      const current = new Date(startOfWeek);
      current.setDate(startOfWeek.getDate() + i);

      const button = document.createElement("button");
      button.textContent = current.getDate();
      button.className =
        "mx-auto w-8 h-8 flex items-center justify-center rounded-lg transition-colors duration-200 " +
        (current.toDateString() === selectedDate.toDateString()
          ? "bg-blue-500 text-white"
          : "text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800");

      button.addEventListener("click", () => {
        selectedDate = current;
        updateTodayLabel();
        renderWeek(selectedDate);
      });

      weekDaysContainer.appendChild(button);
    }
  }

  // Navigation
  prevWeekBtn.addEventListener("click", () => {
    currentDate.setDate(currentDate.getDate() - 7);
    renderWeek(currentDate);
  });

  nextWeekBtn.addEventListener("click", () => {
    currentDate.setDate(currentDate.getDate() + 7);
    renderWeek(currentDate);
  });

  // Init
  updateTodayLabel();
  renderWeek(currentDate);
</script>
  @stack('scripts')
