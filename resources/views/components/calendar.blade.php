@props([
    'holidays' => [],
    'calendarId' => 'default' // Unique ID for this calendar instance
])

  <!-- Calendar Container -->
<div class="w-full max-w-parent p-6 px-12 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-transparent">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <p class="text-sm text-gray-600 dark:text-gray-400">Today is</p>
        <h2 id="today-{{ $calendarId }}" class="text-sm font-semibold"></h2>
      </div>
      <div class="flex gap-3 text-blue-500">
        <button id="prev-week-{{ $calendarId }}" class="hover:text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
          </svg>
        </button>
        <button id="next-week-{{ $calendarId }}" class="hover:text-blue-600">
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
    <div id="week-days-{{ $calendarId }}" class="grid grid-cols-7 text-center text-sm font-medium"></div>
  </div>

  <!-- Holiday Modal -->
  <div id="holiday-modal-{{ $calendarId }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-{{ $calendarId }}" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeHolidayModal()"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div id="modal-icon-container-{{ $calendarId }}" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
              <i id="modal-icon-{{ $calendarId }}" class="text-xl"></i>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title-{{ $calendarId }}">
                Date Information
              </h3>
              <div class="mt-2">
                <p id="modal-date-{{ $calendarId }}" class="text-sm text-gray-500 dark:text-gray-400 font-semibold mb-2"></p>
                <p id="modal-message-{{ $calendarId }}" class="text-sm text-gray-600 dark:text-gray-300"></p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="button" onclick="closeHolidayModal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>

 <script>
  (function() {
    const calendarId = '{{ $calendarId }}';
    const todayElement = document.getElementById("today-" + calendarId);
    const weekDaysContainer = document.getElementById("week-days-" + calendarId);
    const prevWeekBtn = document.getElementById("prev-week-" + calendarId);
    const nextWeekBtn = document.getElementById("next-week-" + calendarId);

    // Load holidays data
    const holidays = @js($holidays);
    const holidayDates = holidays.reduce((acc, holiday) => {
      acc[holiday.date] = holiday.name;
      return acc;
    }, {});

    const today = new Date(); // Actual today - never changes
    let currentWeekDate = new Date(); // For week navigation
    let clickedDate = null; // Track clicked date for outline

    function updateTodayLabel() {
      // Always show actual today's date
      todayElement.textContent = today.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
      });
    }

  function getStartOfWeek(date) {
    const day = date.getDay(); // 0 = Sunday, 6 = Saturday
    // Calculate days to go back to reach Saturday
    // Saturday (6) = 0 days back, Sunday (0) = 1 day back, Monday (1) = 2 days back, etc.
    const diff = -((day + 1) % 7);
    const startDate = new Date(date);
    startDate.setDate(date.getDate() + diff);
    return startDate;
  }

  function renderWeek(date) {
    weekDaysContainer.innerHTML = "";
    const startOfWeek = getStartOfWeek(new Date(date));

    for (let i = 0; i < 7; i++) {
      const current = new Date(startOfWeek);
      current.setDate(startOfWeek.getDate() + i);

      // Format date without timezone conversion to avoid date shifting
      const year = current.getFullYear();
      const month = String(current.getMonth() + 1).padStart(2, '0');
      const day = String(current.getDate()).padStart(2, '0');
      const dateString = `${year}-${month}-${day}`;

      const isHoliday = holidayDates[dateString];
      const isToday = current.toDateString() === today.toDateString();
      const isClicked = clickedDate && current.toDateString() === clickedDate.toDateString();

      const container = document.createElement("div");
      container.className = "relative mx-auto w-8 h-8";

      const button = document.createElement("button");
      button.textContent = current.getDate();

      // Build classes: blue bg for today, outline for clicked, default for others
      let buttonClasses = "w-full h-full flex items-center justify-center rounded-lg transition-colors duration-200 ";

      if (isToday) {
        buttonClasses += "bg-blue-500 text-white ";
      } else {
        buttonClasses += "text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 ";
      }

      // Add outline for clicked date
      if (isClicked) {
        buttonClasses += "ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-gray-800 ";
      }

      button.className = buttonClasses;

      button.addEventListener("click", () => {
        clickedDate = new Date(current);
        renderWeek(currentWeekDate); // Re-render with outline

        // Show holiday modal
        showHolidayModal(dateString, isHoliday);
      });

      container.appendChild(button);

      // Add holiday indicator with better visibility
      if (isHoliday) {
        const holidayBadge = document.createElement("span");
        holidayBadge.className = "absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center shadow-lg border border-white dark:border-gray-800";
        holidayBadge.title = isHoliday;
        holidayBadge.innerHTML = '<i class="fa-solid fa-star text-white" style="font-size: 8px;"></i>';
        container.appendChild(holidayBadge);
      }

      weekDaysContainer.appendChild(container);
    }
  }

  // Navigation
  prevWeekBtn.addEventListener("click", () => {
    currentWeekDate.setDate(currentWeekDate.getDate() - 7);
    renderWeek(currentWeekDate);
  });

  nextWeekBtn.addEventListener("click", () => {
    currentWeekDate.setDate(currentWeekDate.getDate() + 7);
    renderWeek(currentWeekDate);
  });

  // Holiday Modal Functions
  function showHolidayModal(dateString, holidayName) {
    const modal = document.getElementById('holiday-modal-' + calendarId);
    const modalDate = document.getElementById('modal-date-' + calendarId);
    const modalMessage = document.getElementById('modal-message-' + calendarId);
    const modalIcon = document.getElementById('modal-icon-' + calendarId);
    const modalIconContainer = document.getElementById('modal-icon-container-' + calendarId);

    // Format date for display
    const [year, month, day] = dateString.split('-');
    const date = new Date(year, month - 1, day);
    const formattedDate = date.toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });

    modalDate.textContent = formattedDate;

    if (holidayName) {
      // It's a holiday
      modalMessage.textContent = `🎉 ${holidayName}`;
      modalIcon.className = 'fa-solid fa-calendar-check text-xl text-red-500';
      modalIconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10';
    } else {
      // Not a holiday
      modalMessage.textContent = 'No holiday for this date';
      modalIcon.className = 'fa-solid fa-calendar text-xl text-gray-500';
      modalIconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 sm:mx-0 sm:h-10 sm:w-10';
    }

    modal.classList.remove('hidden');
    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden';
  }

  function closeHolidayModal() {
    // Close all holiday modals (in case multiple calendars exist)
    const modals = document.querySelectorAll('[id^="holiday-modal-"]');
    modals.forEach(modal => {
      modal.classList.add('hidden');
    });
    // Restore body scroll
    document.body.style.overflow = '';
  }

  // Make closeHolidayModal globally accessible for onclick handlers
  window.closeHolidayModal = closeHolidayModal;

  // Close modal on ESC key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeHolidayModal();
    }
  });

  // Init
  updateTodayLabel();
  renderWeek(currentWeekDate);
  })(); // End of IIFE
</script>
  @stack('scripts')
