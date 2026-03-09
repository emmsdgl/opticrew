<x-layouts.general-employer :title="'Service Performance Report'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header with Date Filter -->
        <div class="flex flex-col md:items-center md:justify-between gap-4">
            <div class ="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Analytics', 'url' => route('admin.analytics')],
                    ['label' => 'Reports', 'url' => route('admin.reports.index')],
                    ['label' => 'Service Performance'],
                ]" />
            </div>
            <div class ="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Service Performance Report</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Client and employee feedback overview</p>
                </div>

                <!-- Date Range Filter -->
                <form method="GET" action="{{ route('admin.reports.service') }}" class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="px-3 py-2 text-xs border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <span class="text-gray-600 dark:text-gray-400">to</span>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="px-3 py-2 text-xs border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <button type="submit"
                        class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="py-6">
        <x-employer-components.stats-cards :stats="[
            ['label' => 'Total Feedback', 'value' => $totalFeedback],
            ['label' => 'Overall Avg. Rating', 'value' => number_format($overallAvgRating, 1) . ' / 5'],
            ['label' => 'Client Avg. Rating', 'value' => number_format($clientAvgRating, 1) . ' / 5'],
        ]" />
        </div>

        <!-- Client Feedback Table -->
        <div class="flex flex-col gap-4">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Client Feedback</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Feedback submitted by clients ({{ $clientFeedback->count() }})</p>
            </div>

            @if($clientFeedback->count() > 0)
                <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Client</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Service Type</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Rating</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Keywords</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Comments</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientFeedback as $feedback)
                                <tr class="even:bg-gray-50 dark:even:bg-gray-800/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $feedback->client?->full_name ?? 'Unknown Client' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">{{ $feedback->service_type ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php $rating = $feedback->rating ?? $feedback->overall_rating ?? 0; @endphp
                                        <div class="flex items-center gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-sm {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                            @endfor
                                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">{{ $rating }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1 max-w-[200px]">
                                            @if($feedback->keywords && is_array($feedback->keywords))
                                                @foreach($feedback->keywords as $keyword)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">{{ $keyword }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-[250px] truncate" title="{{ $feedback->feedback_text ?? $feedback->comments ?? '' }}">
                                            {{ Str::limit($feedback->feedback_text ?? $feedback->comments ?? '-', 50) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">{{ $feedback->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $feedback->created_at->format('h:i A') }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                    <i class="fa-solid fa-inbox text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No client feedback found</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Client feedback will appear here once submitted for this period</p>
                </div>
            @endif
        </div>

        <!-- Employee Feedback Table -->
        <div class="flex flex-col gap-4">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Employee Feedback</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Feedback submitted by employees ({{ $employeeFeedback->count() }})</p>
            </div>

            @if($employeeFeedback->count() > 0)
                <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Employee</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Task</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Rating</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Keywords</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Comments</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employeeFeedback as $feedback)
                                <tr class="even:bg-gray-50 dark:even:bg-gray-800/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $feedback->employee?->fullName ?? 'Unknown Employee' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900 dark:text-gray-200 max-w-[200px] truncate" title="{{ $feedback->task?->task_description ?? '' }}">
                                            {{ Str::limit($feedback->task?->task_description ?? '-', 40) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php $empRating = $feedback->rating ?? $feedback->overall_rating ?? 0; @endphp
                                        <div class="flex items-center gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-sm {{ $i <= $empRating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                            @endfor
                                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">{{ $empRating }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1 max-w-[200px]">
                                            @if($feedback->keywords && is_array($feedback->keywords))
                                                @foreach($feedback->keywords as $keyword)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ $keyword }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-[250px] truncate" title="{{ $feedback->feedback_text ?? $feedback->comments ?? '' }}">
                                            {{ Str::limit($feedback->feedback_text ?? $feedback->comments ?? '-', 50) }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">{{ $feedback->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $feedback->created_at->format('h:i A') }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                    <i class="fa-solid fa-inbox text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No employee feedback found</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Employee feedback will appear here once submitted for this period</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.general-employer>
