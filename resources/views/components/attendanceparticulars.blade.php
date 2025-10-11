@props(['empName', 'empNum', 'attendanceStatus', 'attendanceDuration'])

<div
    class="flex items-center justify-between w-full bg-white dark:bg-gray-900 rounded-xl shadow-sm px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
    <!-- Left: Avatar + Info -->
    <div class="flex items-center space-x-3">
        <div
            class="flex items-center justify-center bg-blue-100 text-blue-600 font-semibold rounded-xl w-10 h-10 text-lg">
            {{ strtoupper(substr($empName, 0, 1)) }}
        </div>

        <div class="flex flex-col">
            <p class="text-sm font-sans font-bold text-[#081032] dark:text-white leading-tight">
                {{ $empName }}
            </p>
            <p class="text-xs font-sans text-gray-500 dark:text-gray-400 leading-tight">
                {{ $empNum }}
            </p>
        </div>
    </div>

    <!-- Right: Attendance -->
    <div class="flex flex-col items-end">
        <p class="text-xs font-sans text-gray-500 dark:text-gray-400 leading-tight">
            {{ $attendanceStatus }}
        </p>
        <p class="text-sm font-sans font-semibold
            {{ str_contains(strtolower($attendanceStatus), 'early') ? 'text-green-600' :
               (str_contains(strtolower($attendanceStatus), 'late') ? 'text-red-600' : 'text-blue-600') }}">
            {{ $attendanceDuration }}
        </p>
    </div>
</div>
