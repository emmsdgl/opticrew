@props([
    'records' => [],
    'showHeader' => true,
])

<div class="w-full overflow-x-auto">
    <!-- Table Header -->
    @if($showHeader)
    <div class="hidden md:grid grid-cols-6 gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800
                border-b border-gray-200 dark:border-gray-700 rounded-lg">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Status
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Employee / Date
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Type
        </div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Reason</div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Duration</div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Action</div>
    </div>
    @endif

    <!-- Table Body -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($records as $index => $record)
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 px-6 py-4 bg-white dark:bg-gray-900
                    hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">

            <!-- Status Badge -->
            <div class="flex items-center gap-2">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400">Status:</span>
                @if($record['status'] === 'present')
                    <x-badge
                        label="Approved"
                        colorClass="bg-[#2FBC0020] text-[#2FBC00]"
                        size="text-xs" />
                @elseif($record['status'] === 'late')
                    <x-badge
                        label="Pending"
                        colorClass="bg-[#FF7F0020] text-[#FF7F00]"
                        size="text-xs" />
                @else
                    <x-badge
                        label="Rejected"
                        colorClass="bg-[#FE1E2820] text-[#FE1E28]"
                        size="text-xs" />
                @endif
            </div>

            <!-- Employee / Date -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Employee / Date:</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $record['date'] }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $record['dayOfWeek'] }}</span>
            </div>

            <!-- Type -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Type:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $record['requestType'] ?? '-' }}</span>
            </div>

            <!-- Reason -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Reason:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" title="{{ $record['requestReason'] ?? '' }}">{{ $record['timeIn'] ?? '-' }}</span>
            </div>

            <!-- Duration -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Duration:</span>
                <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $record['hoursWorked'] ?? '-' }}</span>
            </div>

            <!-- Action Button -->
            <div class="flex items-center justify-center">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 mr-2">Action:</span>
                <button
                    @click="$dispatch('open-request-modal', { index: {{ $index }} })"
                    class="w-full px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    View
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if(count($records) === 0)
    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
        <i class="fa-regular fa-calendar-xmark text-4xl mb-3"></i>
        <p>No request records found</p>
    </div>
    @endif
</div>
