<x-layouts.general-employer :title="'Evaluation Details'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col gap-4">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Analytics', 'url' => route('admin.analytics')],
                    ['label' => 'Reports', 'url' => route('admin.reports.index')],
                    ['label' => 'Employee Performance', 'url' => route('admin.reports.performance.index')],
                    ['label' => $evaluation->employee->fullName],
                ]" />
            </div>
            <div class="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $evaluation->employee->fullName }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Evaluation for {{ $evaluation->evaluation_period_start->format('F Y') }}
                        <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $evaluation->status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                            {{ ucfirst($evaluation->status) }}
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reports.performance.evaluate', ['employeeId' => $evaluation->employee_id, 'month' => $evaluation->evaluation_period_start->month, 'year' => $evaluation->evaluation_period_start->year]) }}"
                        class="px-4 py-2 text-sm font-medium text-amber-600 bg-amber-50 dark:bg-amber-900/20 dark:text-amber-400 rounded-lg hover:bg-amber-100">
                        Edit Evaluation
                    </a>
                    @if ($evaluation->requires_pip && !$evaluation->improvementPlan)
                        <a href="{{ route('admin.reports.performance.pip.create', $evaluation->id) }}"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                            Create PIP
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Overall Rating Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Overall Rating</h2>
                        @php $color = $evaluation->getRatingColor(); @endphp
                        <div class="text-center">
                            <span class="text-3xl font-bold text-{{ $color }}-600 dark:text-{{ $color }}-400">
                                {{ number_format($evaluation->overall_rating, 1) }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">/ 5.0</span>
                            <p class="text-xs font-medium text-{{ $color }}-600 dark:text-{{ $color }}-400 mt-1">
                                {{ $evaluation->getRatingLabel() }}
                            </p>
                        </div>
                    </div>

                    <!-- Criteria Breakdown -->
                    <div class="space-y-4">
                        @php
                            $criteria = \App\Models\PerformanceEvaluation::CRITERIA;
                        @endphp
                        @foreach ($criteria as $field => $label)
                            @php $score = $evaluation->$field ?? 0; @endphp
                            <div class="flex items-center gap-4">
                                <span class="text-sm text-gray-700 dark:text-gray-300 w-40">{{ $label }}</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all
                                        {{ $score >= 4 ? 'bg-green-500' : ($score >= 3 ? 'bg-yellow-500' : ($score >= 2 ? 'bg-orange-500' : 'bg-red-500')) }}"
                                        style="width: {{ ($score / 5) * 100 }}%"></div>
                                </div>
                                <div class="flex items-center gap-1 w-20">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fi fi-{{ $i <= $score ? 'sr' : 'rr' }}-star text-amber-400" style="font-size: 10px;"></i>
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Feedback Sections -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
                    @if ($evaluation->strengths)
                        <div>
                            <h3 class="text-sm font-semibold text-green-700 dark:text-green-400 mb-2 flex items-center gap-2">
                                <i class="fi fi-rr-thumbs-up"></i> Strengths
                            </h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $evaluation->strengths }}</p>
                        </div>
                    @endif

                    @if ($evaluation->areas_for_improvement)
                        <div>
                            <h3 class="text-sm font-semibold text-orange-700 dark:text-orange-400 mb-2 flex items-center gap-2">
                                <i class="fi fi-rr-arrow-trend-up"></i> Areas for Improvement
                            </h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $evaluation->areas_for_improvement }}</p>
                        </div>
                    @endif

                    @if ($evaluation->goals_for_next_period)
                        <div>
                            <h3 class="text-sm font-semibold text-blue-700 dark:text-blue-400 mb-2 flex items-center gap-2">
                                <i class="fi fi-rr-bullseye-arrow"></i> Goals for Next Period
                            </h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $evaluation->goals_for_next_period }}</p>
                        </div>
                    @endif

                    @if ($evaluation->admin_comments)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-400 mb-2 flex items-center gap-2">
                                <i class="fi fi-rr-comment-alt"></i> Additional Comments
                            </h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $evaluation->admin_comments }}</p>
                        </div>
                    @endif
                </div>

                <!-- System Metrics (if auto-filled) -->
                @if ($evaluation->system_metrics)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fi fi-rr-chart-histogram text-amber-500"></i>
                            System Data Used for Evaluation
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                            @if (isset($evaluation->system_metrics['attendance']))
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/10 rounded-lg">
                                    <p class="font-semibold text-blue-600 dark:text-blue-400 mb-1">Attendance</p>
                                    <p class="text-gray-600 dark:text-gray-400">Rate: {{ $evaluation->system_metrics['attendance']['attendance_rate'] ?? 0 }}%</p>
                                    <p class="text-gray-600 dark:text-gray-400">Late: {{ $evaluation->system_metrics['attendance']['late_days'] ?? 0 }} days</p>
                                </div>
                            @endif
                            @if (isset($evaluation->system_metrics['task_performance']))
                                <div class="p-3 bg-green-50 dark:bg-green-900/10 rounded-lg">
                                    <p class="font-semibold text-green-600 dark:text-green-400 mb-1">Tasks</p>
                                    <p class="text-gray-600 dark:text-gray-400">Completed: {{ $evaluation->system_metrics['task_performance']['tasks_completed'] ?? 0 }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">Score: {{ $evaluation->system_metrics['task_performance']['average_performance_score'] ?? 0 }}</p>
                                </div>
                            @endif
                            @if (isset($evaluation->system_metrics['feedback']))
                                <div class="p-3 bg-purple-50 dark:bg-purple-900/10 rounded-lg">
                                    <p class="font-semibold text-purple-600 dark:text-purple-400 mb-1">Feedback</p>
                                    <p class="text-gray-600 dark:text-gray-400">Count: {{ $evaluation->system_metrics['feedback']['total_feedbacks'] ?? 0 }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">Avg: {{ $evaluation->system_metrics['feedback']['avg_rating'] ?? 0 }}/5</p>
                                </div>
                            @endif
                            @if (isset($evaluation->system_metrics['performance_flags']))
                                <div class="p-3 bg-red-50 dark:bg-red-900/10 rounded-lg">
                                    <p class="font-semibold text-red-600 dark:text-red-400 mb-1">Flags</p>
                                    <p class="text-gray-600 dark:text-gray-400">Total: {{ $evaluation->system_metrics['performance_flags']['total_flags'] ?? 0 }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">Variance: {{ $evaluation->system_metrics['performance_flags']['avg_variance_pct'] ?? 0 }}%</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Evaluation Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Evaluation Details</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Evaluator</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $evaluation->evaluator->name ?? 'System' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Period</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $evaluation->evaluation_period_start->format('M d') }} - {{ $evaluation->evaluation_period_end->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Completed</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $evaluation->updated_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">PIP Required</span>
                            <span class="font-medium {{ $evaluation->requires_pip ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ $evaluation->requires_pip ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- PIP Card -->
                @if ($evaluation->improvementPlan)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-red-500">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Performance Improvement Plan</h3>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $evaluation->improvementPlan->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $evaluation->improvementPlan->start_date->format('M d') }} - {{ $evaluation->improvementPlan->end_date->format('M d, Y') }}
                        </p>
                        <div class="mt-3">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-500 dark:text-gray-400">Progress</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $evaluation->improvementPlan->getProgressPercentage() }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $evaluation->improvementPlan->getProgressPercentage() }}%"></div>
                            </div>
                        </div>
                        <a href="{{ route('admin.reports.performance.pip.show', $evaluation->improvementPlan->id) }}"
                            class="mt-3 inline-block text-xs text-red-600 dark:text-red-400 font-medium hover:underline">
                            View PIP Details
                        </a>
                    </div>
                @elseif ($evaluation->requires_pip)
                    <div class="bg-red-50 dark:bg-red-900/10 rounded-lg shadow p-6 border-l-4 border-red-500">
                        <h3 class="text-sm font-semibold text-red-700 dark:text-red-400 mb-2">PIP Required</h3>
                        <p class="text-xs text-red-600 dark:text-red-400 mb-3">This employee has been flagged for a Performance Improvement Plan.</p>
                        <a href="{{ route('admin.reports.performance.pip.create', $evaluation->id) }}"
                            class="px-4 py-2 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                            Create PIP
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.general-employer>
