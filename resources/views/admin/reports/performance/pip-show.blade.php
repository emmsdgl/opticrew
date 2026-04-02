<x-layouts.general-employer :title="'PIP Details'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col gap-4">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Reports', 'url' => route('admin.reports.index')],
                    ['label' => 'Employee Performance', 'url' => route('admin.reports.performance.index')],
                    ['label' => 'PIP: ' . $pip->employee->fullName],
                ]" />
            </div>
            <div class="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pip->title }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $pip->employee->fullName }} | {{ $pip->start_date->format('M d') }} - {{ $pip->end_date->format('M d, Y') }}
                        @if ($pip->isOverdue())
                            <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full">Overdue</span>
                        @endif
                    </p>
                </div>
                <span class="px-3 py-1.5 text-xs font-medium rounded-full
                    {{ $pip->status === 'active' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                       ($pip->status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                       'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400') }}">
                    {{ ucfirst($pip->status) }}
                </span>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                @if ($pip->description)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Description</h2>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $pip->description }}</p>
                    </div>
                @endif

                <!-- Areas to Improve -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Areas to Improve</h2>
                    <div class="space-y-4">
                        @foreach ($pip->areas_to_improve ?? [] as $area)
                            <div class="p-4 bg-red-50 dark:bg-red-900/10 rounded-lg border-l-4 border-red-400">
                                <h4 class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $area['area'] }}</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $area['details'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Items -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Action Items</h2>
                    <div class="space-y-3">
                        @foreach ($pip->action_items ?? [] as $index => $item)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="mt-0.5">
                                    @if (($item['status'] ?? 'pending') === 'completed')
                                        <i class="fi fi-sr-check-circle text-green-500"></i>
                                    @else
                                        <i class="fi fi-rr-circle text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900 dark:text-white {{ ($item['status'] ?? '') === 'completed' ? 'line-through opacity-60' : '' }}">
                                        {{ $item['description'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Target: {{ \Carbon\Carbon::parse($item['target_date'])->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Progress -->
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500 dark:text-gray-400">Progress</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $pip->getProgressPercentage() }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $pip->getProgressPercentage() }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Outcome Notes -->
                @if ($pip->outcome_notes)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Outcome Notes</h2>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $pip->outcome_notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Update -->
                @if ($pip->status === 'active')
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Update Status</h3>
                        <form method="POST" action="{{ route('admin.reports.performance.pip.update-status', $pip->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="space-y-3">
                                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="extended">Extended</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <textarea name="outcome_notes" rows="3"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                                    placeholder="Notes about outcome or reason for status change...">{{ $pip->outcome_notes }}</textarea>
                                <button type="submit"
                                    class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- PIP Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Plan Info</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Created By</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $pip->creator->name ?? 'System' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Duration</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $pip->start_date->diffInDays($pip->end_date) }} days</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Days Remaining</span>
                            <span class="font-medium {{ $pip->end_date->isPast() ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                {{ $pip->end_date->isPast() ? 'Overdue by ' . $pip->end_date->diffInDays(now()) . ' days' : $pip->end_date->diffInDays(now()) . ' days' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Action Items</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ count($pip->action_items ?? []) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Related Evaluation -->
                @if ($pip->evaluation)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Related Evaluation</h3>
                        <a href="{{ route('admin.reports.performance.show', $pip->evaluation_id) }}"
                            class="block p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $pip->evaluation->evaluation_period_start->format('F Y') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Rating: {{ number_format($pip->evaluation->overall_rating, 1) }}/5 - {{ $pip->evaluation->getRatingLabel() }}
                            </p>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.general-employer>
