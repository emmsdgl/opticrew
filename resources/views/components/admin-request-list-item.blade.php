@props([
    'records' => [],
    'showHeader' => true,
])

<div class="w-full overflow-x-auto">
    <!-- Table Header -->
    @if($showHeader)
    <div class="hidden md:grid grid-cols-7 gap-4 px-6 py-4 bg-gray-50 dark:bg-gray-800
                border-b border-gray-200 dark:border-gray-700 rounded-lg">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Status
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Employee
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Date
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
            Type
        </div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Time Range</div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Reason</div>
        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center">Action</div>
    </div>
    @endif

    <!-- Table Body -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($records as $index => $record)
        <div class="grid grid-cols-1 md:grid-cols-7 gap-4 px-6 py-4 bg-white dark:bg-gray-900
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
                @elseif($record['status'] === 'archived')
                    <x-badge
                        label="Cancelled"
                        colorClass="bg-[#6B728020] text-[#6B7280]"
                        size="text-xs" />
                @else
                    <x-badge
                        label="Rejected"
                        colorClass="bg-[#FE1E2820] text-[#FE1E28]"
                        size="text-xs" />
                @endif
            </div>

            <!-- Employee -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Employee:</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $record['requestEmployeeName'] ?? 'Unknown' }}</span>
            </div>

            <!-- Date -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Date:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $record['requestDate'] ?? '-' }}</span>
            </div>

            <!-- Type -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Type:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $record['requestType'] ?? '-' }}</span>
            </div>

            <!-- Time Range -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Time Range:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $record['requestTimeRange'] ?? '-' }}</span>
            </div>

            <!-- Reason -->
            <div class="flex flex-col">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Reason:</span>
                <span class="text-sm text-gray-600 dark:text-gray-400 truncate" title="{{ $record['requestReason'] ?? '' }}">{{ Str::limit($record['requestReason'] ?? '-', 20) }}</span>
            </div>

            <!-- Action Button -->
            <div class="flex items-center justify-center">
                <span class="md:hidden text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 mr-2">Action:</span>
                <button
                    @click="$dispatch('open-request-modal', { index: {{ $index }} })"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                    <i class="fa-regular fa-eye mr-1 text-xs"></i> View
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
