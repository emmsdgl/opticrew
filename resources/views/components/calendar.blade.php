@props([
    'holidays' => [],
    'calendarId' => 'default' // Unique ID for this calendar instance
])

  <!-- Calendar Container -->
<div class="w-full max-w-parent py-6 px-8 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-transparent">

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
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-black/60 dark:bg-black/80 transition-opacity" aria-hidden="true" onclick="closeHolidayModal()"></div>

      <!-- Modal panel -->
      <div class="relative inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full mx-4 border border-slate-700 dark:border-slate-800">
        <!-- Close button -->
        <button type="button" onclick="closeHolidayModal()" class="absolute top-4 right-4 sm:top-6 sm:right-6 text-gray-400 hover:text-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-slate-800 rounded-lg p-1 z-10">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div class="px-6 pt-6 pb-6 sm:px-8 sm:pt-8 sm:pb-8">
          <div class="flex items-start gap-8 pr-3">
            <div id="modal-icon-container-{{ $calendarId }}" class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full">
              <i id="modal-icon-{{ $calendarId }}" class="text-2xl"></i>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm text-[#081032] dark:text-white mb-2" id="modal-title-{{ $calendarId }}">
                Date Information
              </h3>
              <p id="modal-date-{{ $calendarId }}" class="text-lg font-bold text-[#081032] dark:text-gray-300 mb-3 "></p>
              <p id="modal-message-{{ $calendarId }}" class="text-sm text-[#081032] dark:text-gray-400 leading-relaxed"></p>
            </div>
          </div>
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
      container.className = "relative mx-auto w-8 h-12";

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
    const modalTitle = document.getElementById('modal-title-' + calendarId);
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
      modalTitle.textContent = 'Holiday Date';
      modalMessage.textContent = `This is a special holiday: ${holidayName}. Enjoy your day off!`;
      modalIcon.className = 'fa-solid fa-calendar-check text-lg text-blue-600';
      modalIconContainer.className = 'flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-blue-600/20';
    } else {
      // Not a holiday
      modalTitle.textContent = 'Regular Date';
      modalMessage.textContent = 'This is a regular working day. No holiday scheduled for this date.';
      modalIcon.className = 'fa-solid fa-calendar-day text-lg text-blue-600';
      modalIconContainer.className = 'flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl bg-blue-600/20';
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