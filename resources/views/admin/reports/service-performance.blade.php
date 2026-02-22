<x-layouts.general-employer :title="'Service Performance Report'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header with Date Filter -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        <i class="fi fi-rr-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Service Performance Report</h1>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Client and employee feedback overview</p>
            </div>

            <!-- Date Range Filter -->
            <form method="GET" action="{{ route('admin.reports.service') }}" class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <span class="text-gray-600 dark:text-gray-400">to</span>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Filter
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Feedback</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $totalFeedback }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                        <i class="fi fi-rr-comment-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Overall Avg. Rating</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ number_format($overallAvgRating, 1) }} <span class="text-sm font-normal text-gray-500">/ 5</span></p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                        <i class="fi fi-rr-star text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Client Avg. Rating</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ number_format($clientAvgRating, 1) }} <span class="text-sm font-normal text-gray-500">/ 5</span></p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                        <i class="fi fi-rr-users-alt text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>

            
        </div>

        <!-- Client Feedback Table -->
        <div class="rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Client Feedback</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Feedback submitted by clients ({{ $clientFeedback->count() }})</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Service Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keywords</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Comments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($clientFeedback as $feedback)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $feedback->client?->full_name ?? 'Unknown Client' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $feedback->service_type ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $rating = $feedback->rating ?? $feedback->overall_rating ?? 0;
                                    @endphp
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-sm {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                        @endfor
                                        <span class="text-sm font-medium text-gray-900 dark:text-white ml-1">{{ $rating }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-[200px]">
                                        @if($feedback->keywords && is_array($feedback->keywords))
                                            @foreach($feedback->keywords as $keyword)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                                    {{ $keyword }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 max-w-[250px] truncate" title="{{ $feedback->feedback_text ?? $feedback->comments ?? '' }}">
                                        {{ Str::limit($feedback->feedback_text ?? $feedback->comments ?? '-', 50) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $feedback->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $feedback->created_at->format('h:i A') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <i class="fi fi-rr-inbox text-4xl mb-2"></i>
                                        <p>No client feedback found for this period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Employee Feedback Table -->
        <div class="rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Employee Feedback</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Feedback submitted by employees ({{ $employeeFeedback->count() }})</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keywords</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Comments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($employeeFeedback as $feedback)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $feedback->employee?->fullName ?? 'Unknown Employee' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900 dark:text-white max-w-[200px] truncate" title="{{ $feedback->task?->task_description ?? '' }}">
                                        {{ Str::limit($feedback->task?->task_description ?? '-', 40) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $empRating = $feedback->rating ?? $feedback->overall_rating ?? 0;
                                    @endphp
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-sm {{ $i <= $empRating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                        @endfor
                                        <span class="text-sm font-medium text-gray-900 dark:text-white ml-1">{{ $empRating }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-[200px]">
                                        @if($feedback->keywords && is_array($feedback->keywords))
                                            @foreach($feedback->keywords as $keyword)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                                    {{ $keyword }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 max-w-[250px] truncate" title="{{ $feedback->feedback_text ?? $feedback->comments ?? '' }}">
                                        {{ Str::limit($feedback->feedback_text ?? $feedback->comments ?? '-', 50) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $feedback->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $feedback->created_at->format('h:i A') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <i class="fi fi-rr-inbox text-4xl mb-2"></i>
                                        <p>No employee feedback found for this period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.general-employer>
