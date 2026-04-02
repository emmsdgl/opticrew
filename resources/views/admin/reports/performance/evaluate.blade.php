<x-layouts.general-employer :title="'Evaluate ' . $employee->fullName">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col gap-4">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Analytics', 'url' => route('admin.analytics')],
                    ['label' => 'Reports', 'url' => route('admin.reports.index')],
                    ['label' => 'Employee Performance', 'url' => route('admin.reports.performance.index', ['month' => $month, 'year' => $year])],
                    ['label' => $employee->fullName],
                ]" />
            </div>
            <div class="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Evaluate: {{ $employee->fullName }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Performance evaluation for {{ $periodStart->format('F Y') }}
                    </p>
                </div>
                <button type="button" id="autoFillBtn"
                    class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 flex items-center gap-2 transition-all">
                    <i class="fi fi-rr-magic-wand text-sm"></i>
                    <span>Auto-Answer</span>
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Auto-fill metrics panel (hidden by default, shown after auto-fill) -->
        <div id="metricsPanel" class="hidden bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fi fi-rr-chart-histogram text-amber-500"></i>
                System Data Summary (used for auto-fill)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="metricsContent">
                <!-- Populated by JS -->
            </div>
        </div>

        <form method="POST" action="{{ route('admin.reports.performance.store') }}" id="evaluationForm">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <input type="hidden" name="evaluation_period_start" value="{{ $periodStart->toDateString() }}">
            <input type="hidden" name="evaluation_period_end" value="{{ $periodEnd->toDateString() }}">
            <input type="hidden" name="system_metrics" id="systemMetricsInput" value="">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Scoring Criteria -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Criteria Scores -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Performance Criteria</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Rate each criteria from 1 (Unsatisfactory) to 5 (Outstanding)</p>

                        <div class="space-y-6">
                            @php
                                $criteria = [
                                    'attendance_score' => ['label' => 'Attendance', 'desc' => 'Consistency of employee presence and adherence to work schedule', 'icon' => 'fi-rr-calendar-check'],
                                    'punctuality_score' => ['label' => 'Punctuality', 'desc' => 'Timeliness in arriving to work and completing tasks on schedule', 'icon' => 'fi-rr-clock'],
                                    'task_completion_score' => ['label' => 'Task Completion', 'desc' => 'Efficiency and reliability in completing assigned tasks', 'icon' => 'fi-rr-check-circle'],
                                    'quality_of_work_score' => ['label' => 'Quality of Work', 'desc' => 'Standard and quality of cleaning services delivered', 'icon' => 'fi-rr-star'],
                                    'professionalism_score' => ['label' => 'Professionalism', 'desc' => 'Professional conduct, communication, and client interaction', 'icon' => 'fi-rr-briefcase'],
                                    'teamwork_score' => ['label' => 'Teamwork', 'desc' => 'Collaboration with team members and contribution to team goals', 'icon' => 'fi-rr-users'],
                                ];
                            @endphp

                            @foreach ($criteria as $field => $info)
                                <div class="criteria-row">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-3 flex-1">
                                            <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg mt-0.5">
                                                <i class="fi {{ $info['icon'] }} text-amber-600 dark:text-amber-400 text-sm"></i>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-900 dark:text-white">{{ $info['label'] }}</label>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $info['desc'] }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="{{ $field }}" value="{{ $i }}"
                                                        class="sr-only peer score-radio" data-field="{{ $field }}"
                                                        {{ ($evaluation && $evaluation->$field == $i) ? 'checked' : '' }}
                                                        required>
                                                    <div class="w-10 h-10 rounded-lg border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center
                                                        peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/30 peer-checked:text-amber-600
                                                        hover:border-amber-300 transition-all text-sm font-semibold text-gray-400 dark:text-gray-500">
                                                        {{ $i }}
                                                    </div>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Overall Rating Display -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Overall Rating</span>
                                <div class="flex items-center gap-2">
                                    <span id="overallRating" class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                        {{ $evaluation ? number_format($evaluation->overall_rating, 1) : '-' }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">/ 5.0</span>
                                </div>
                            </div>
                            <div id="ratingLabel" class="text-right text-xs font-medium text-gray-500 dark:text-gray-400 mt-1">
                                {{ $evaluation ? $evaluation->getRatingLabel() : '' }}
                            </div>
                        </div>
                    </div>

                    <!-- Text Feedback -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Feedback & Goals</h2>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Strengths</label>
                            <textarea name="strengths" id="strengths" rows="3"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                                placeholder="Highlight areas where the employee excels...">{{ $evaluation->strengths ?? '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Areas for Improvement</label>
                            <textarea name="areas_for_improvement" id="areas_for_improvement" rows="3"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                                placeholder="Identify areas needing development...">{{ $evaluation->areas_for_improvement ?? '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Goals for Next Period</label>
                            <textarea name="goals_for_next_period" id="goals_for_next_period" rows="3"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                                placeholder="Set specific, measurable goals for the next month...">{{ $evaluation->goals_for_next_period ?? '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Additional Comments</label>
                            <textarea name="admin_comments" id="admin_comments" rows="2"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                                placeholder="Any additional notes or observations...">{{ $evaluation->admin_comments ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    <!-- Submit Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Actions</h3>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="requires_pip" id="requires_pip" value="1"
                                class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                                {{ ($evaluation && $evaluation->requires_pip) ? 'checked' : '' }}>
                            <label for="requires_pip" class="text-sm text-gray-700 dark:text-gray-300">
                                Requires
                                <a href="javascript:void(0)" onclick="showPipInfo()" class="text-amber-600 dark:text-amber-400 underline hover:text-amber-700 dark:hover:text-amber-300">
                                    Performance Improvement Plan
                                </a>
                            </label>
                        </div>

                        <div class="flex flex-col gap-2">
                            <button type="submit" name="status" value="draft"
                                class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                Save as Draft
                            </button>
                            <button type="submit" name="status" value="completed"
                                class="w-full px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                                Complete Evaluation
                            </button>
                        </div>
                    </div>

                    <!-- Employee Info Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Employee Info</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Name</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $employee->fullName }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Experience</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $employee->years_of_experience ?? 0 }} years</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Efficiency</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $employee->efficiency ? number_format($employee->efficiency * 100, 0) . '%' : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Period</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $periodStart->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Previous Evaluations -->
                    @if ($previousEvaluations->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Previous Evaluations</h3>
                            <div class="space-y-3">
                                @foreach ($previousEvaluations as $prev)
                                    <a href="{{ route('admin.reports.performance.show', $prev->id) }}"
                                        class="block p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $prev->evaluation_period_start->format('M Y') }}
                                            </span>
                                            <div class="flex items-center gap-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fi fi-{{ $i <= round($prev->overall_rating) ? 'sr' : 'rr' }}-star text-amber-400" style="font-size: 10px;"></i>
                                                @endfor
                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300 ml-1">{{ number_format($prev->overall_rating, 1) }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Rating Guide -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Rating Guide</h3>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded flex items-center justify-center font-bold">5</span>
                                <span class="text-gray-600 dark:text-gray-400">Outstanding</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded flex items-center justify-center font-bold">4</span>
                                <span class="text-gray-600 dark:text-gray-400">Exceeds Expectations</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded flex items-center justify-center font-bold">3</span>
                                <span class="text-gray-600 dark:text-gray-400">Meets Expectations</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded flex items-center justify-center font-bold">2</span>
                                <span class="text-gray-600 dark:text-gray-400">Needs Improvement</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded flex items-center justify-center font-bold">1</span>
                                <span class="text-gray-600 dark:text-gray-400">Unsatisfactory</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

    @push('scripts')
    <script>
        function showPipInfo() {
            window.showSuccessDialog(
                'What is a Performance Improvement Plan?',
                'When you check this box, you are flagging this employee for extra support. ' +
                'After completing the evaluation, you will be able to create a step-by-step plan to help them improve in the areas where they scored low.\n\n' +
                'The plan will include specific goals, action steps, and a timeline so the employee knows exactly what is expected of them and how they can get back on track.\n\n' +
                'Only check this if you feel the employee needs a structured follow-up — for example, if they are consistently falling behind on attendance, quality, or other key areas.',
                'Got it'
            );
        }

        document.addEventListener('DOMContentLoaded', function() {
            const scoreRadios = document.querySelectorAll('.score-radio');
            const overallRatingEl = document.getElementById('overallRating');
            const ratingLabelEl = document.getElementById('ratingLabel');

            // Update overall rating when scores change
            function updateOverallRating() {
                const fields = ['attendance_score', 'punctuality_score', 'task_completion_score', 'quality_of_work_score', 'professionalism_score', 'teamwork_score'];
                let total = 0, count = 0;

                fields.forEach(field => {
                    const checked = document.querySelector(`input[name="${field}"]:checked`);
                    if (checked) {
                        total += parseInt(checked.value);
                        count++;
                    }
                });

                if (count > 0) {
                    const avg = (total / count).toFixed(1);
                    overallRatingEl.textContent = avg;

                    if (avg >= 4.5) ratingLabelEl.textContent = 'Outstanding';
                    else if (avg >= 3.5) ratingLabelEl.textContent = 'Exceeds Expectations';
                    else if (avg >= 2.5) ratingLabelEl.textContent = 'Meets Expectations';
                    else if (avg >= 1.5) ratingLabelEl.textContent = 'Needs Improvement';
                    else ratingLabelEl.textContent = 'Unsatisfactory';
                }
            }

            scoreRadios.forEach(radio => radio.addEventListener('change', updateOverallRating));

            // Auto-Fill Button
            const autoFillBtn = document.getElementById('autoFillBtn');
            const metricsPanel = document.getElementById('metricsPanel');
            const metricsContent = document.getElementById('metricsContent');

            autoFillBtn.addEventListener('click', function() {
                autoFillBtn.disabled = true;
                autoFillBtn.innerHTML = '<i class="fi fi-rr-spinner animate-spin text-sm"></i> <span>Analyzing...</span>';

                fetch(`{{ route('admin.reports.performance.auto-fill', $employee->id) }}?month={{ $month }}&year={{ $year }}`)
                    .then(res => res.json())
                    .then(data => {
                        // Fill scores
                        Object.entries(data.scores).forEach(([field, score]) => {
                            const radio = document.querySelector(`input[name="${field}"][value="${score}"]`);
                            if (radio) radio.checked = true;
                        });

                        // Fill text fields
                        if (data.strengths) document.getElementById('strengths').value = data.strengths;
                        if (data.areas_for_improvement) document.getElementById('areas_for_improvement').value = data.areas_for_improvement;
                        if (data.goals_for_next_period) document.getElementById('goals_for_next_period').value = data.goals_for_next_period;

                        // Auto-check PIP if overall is low
                        const avgScore = Object.values(data.scores).reduce((a, b) => a + b, 0) / Object.values(data.scores).length;
                        if (avgScore <= 2.0) {
                            document.getElementById('requires_pip').checked = true;
                        }

                        // Store system metrics
                        document.getElementById('systemMetricsInput').value = JSON.stringify(data.metrics);

                        // Show metrics panel
                        metricsPanel.classList.remove('hidden');
                        metricsContent.innerHTML = buildMetricsHTML(data.metrics);

                        updateOverallRating();

                        autoFillBtn.innerHTML = '<i class="fi fi-rr-check text-sm"></i> <span>Auto-Filled</span>';
                        autoFillBtn.classList.remove('bg-amber-600', 'hover:bg-amber-700');
                        autoFillBtn.classList.add('bg-green-600', 'hover:bg-green-700');

                        setTimeout(() => {
                            autoFillBtn.disabled = false;
                            autoFillBtn.innerHTML = '<i class="fi fi-rr-magic-wand text-sm"></i> <span>Auto-Answer</span>';
                            autoFillBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                            autoFillBtn.classList.add('bg-amber-600', 'hover:bg-amber-700');
                        }, 3000);
                    })
                    .catch(err => {
                        console.error(err);
                        autoFillBtn.disabled = false;
                        autoFillBtn.innerHTML = '<i class="fi fi-rr-magic-wand text-sm"></i> <span>Auto-Answer</span>';
                        window.showErrorDialog('Auto-Fill Failed', 'Failed to auto-fill evaluation scores. Please try again or fill in manually.');
                    });
            });

            function buildMetricsHTML(metrics) {
                let html = '';

                if (metrics.attendance) {
                    html += `
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/10 rounded-lg">
                            <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 mb-2">Attendance</p>
                            <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                <p>Days Present: <span class="font-medium text-gray-900 dark:text-white">${metrics.attendance.total_days_present}/${metrics.attendance.expected_days}</span></p>
                                <p>Attendance Rate: <span class="font-medium text-gray-900 dark:text-white">${metrics.attendance.attendance_rate}%</span></p>
                                <p>Late Days: <span class="font-medium text-gray-900 dark:text-white">${metrics.attendance.late_days}</span></p>
                                <p>Punctuality: <span class="font-medium text-gray-900 dark:text-white">${metrics.attendance.punctuality_rate}%</span></p>
                            </div>
                        </div>`;
                }

                if (metrics.task_performance) {
                    html += `
                        <div class="p-3 bg-green-50 dark:bg-green-900/10 rounded-lg">
                            <p class="text-xs font-semibold text-green-600 dark:text-green-400 mb-2">Task Performance</p>
                            <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                <p>Tasks Completed: <span class="font-medium text-gray-900 dark:text-white">${metrics.task_performance.tasks_completed}</span></p>
                                <p>Avg Score: <span class="font-medium text-gray-900 dark:text-white">${metrics.task_performance.average_performance_score}</span></p>
                                <p>Rating: <span class="font-medium text-gray-900 dark:text-white">${metrics.task_performance.performance_rating}</span></p>
                            </div>
                        </div>`;
                }

                if (metrics.feedback) {
                    html += `
                        <div class="p-3 bg-purple-50 dark:bg-purple-900/10 rounded-lg">
                            <p class="text-xs font-semibold text-purple-600 dark:text-purple-400 mb-2">Client Feedback</p>
                            <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                <p>Total Feedbacks: <span class="font-medium text-gray-900 dark:text-white">${metrics.feedback.total_feedbacks}</span></p>
                                <p>Avg Rating: <span class="font-medium text-gray-900 dark:text-white">${metrics.feedback.avg_rating}/5</span></p>
                                <p>Quality: <span class="font-medium text-gray-900 dark:text-white">${metrics.feedback.avg_quality}/5</span></p>
                                <p>Professionalism: <span class="font-medium text-gray-900 dark:text-white">${metrics.feedback.avg_professionalism}/5</span></p>
                            </div>
                        </div>`;
                }

                if (metrics.performance_flags) {
                    html += `
                        <div class="p-3 bg-red-50 dark:bg-red-900/10 rounded-lg">
                            <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-2">Performance Flags</p>
                            <div class="space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                <p>Total Flags: <span class="font-medium text-gray-900 dark:text-white">${metrics.performance_flags.total_flags}</span></p>
                                <p>Reviewed: <span class="font-medium text-gray-900 dark:text-white">${metrics.performance_flags.reviewed_flags}</span></p>
                                <p>Avg Variance: <span class="font-medium text-gray-900 dark:text-white">${metrics.performance_flags.avg_variance_pct}%</span></p>
                            </div>
                        </div>`;
                }

                return html;
            }
        });
    </script>
    @endpush
</x-layouts.general-employer>
