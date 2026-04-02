@php
    $prefill = session('pip_prefill');
    $areas = $prefill['areas_to_improve'] ?? [];
    $actions = $prefill['action_items'] ?? [];
    $prefillTitle = $prefill['title'] ?? ('Performance Improvement Plan - ' . $evaluation->employee->fullName);
    $prefillDesc = $prefill['description'] ?? $evaluation->areas_for_improvement;
    $prefillStart = $prefill['start_date'] ?? now()->toDateString();
    $prefillEnd = $prefill['end_date'] ?? now()->addMonth()->toDateString();
@endphp

<x-layouts.general-employer :title="'Create Performance Improvement Plan'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col gap-4">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Reports', 'url' => route('admin.reports.index')],
                    ['label' => 'Employee Performance', 'url' => route('admin.reports.performance.index')],
                    ['label' => 'Create PIP'],
                ]" />
            </div>
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Performance Improvement Plan</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    For {{ $evaluation->employee->fullName }} - Based on {{ $evaluation->evaluation_period_start->format('F Y') }} evaluation
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($prefill)
            <div class="p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 rounded-lg text-sm flex items-center gap-2">
                <i class="fi fi-rr-magic-wand"></i>
                This plan has been auto-generated based on the evaluation scores. Please review and adjust before submitting.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.reports.performance.pip.store') }}" id="pipForm">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $evaluation->employee_id }}">
            <input type="hidden" name="evaluation_id" value="{{ $evaluation->id }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Plan Details</h2>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                            <input type="text" name="title" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="e.g., Attendance Improvement Plan"
                                value="{{ $prefillTitle }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                                placeholder="Describe the purpose and context of this improvement plan...">{{ $prefillDesc }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                <input type="date" name="start_date" required value="{{ $prefillStart }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                <input type="date" name="end_date" required value="{{ $prefillEnd }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Areas to Improve -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Areas to Improve</h2>
                            <button type="button" id="addAreaBtn"
                                class="px-3 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 dark:bg-amber-900/20 dark:text-amber-400 rounded-lg hover:bg-amber-100">
                                + Add Area
                            </button>
                        </div>
                        <div id="areasContainer" class="space-y-4">
                            @if (count($areas) > 0)
                                @foreach ($areas as $i => $area)
                                    <div class="area-row p-4 border border-gray-200 dark:border-gray-700 rounded-lg {{ $i > 0 ? 'relative' : '' }}">
                                        @if ($i > 0)
                                            <button type="button" onclick="this.closest('.area-row').remove()"
                                                class="absolute top-2 right-2 text-red-400 hover:text-red-600 text-xs">
                                                <i class="fi fi-rr-trash"></i>
                                            </button>
                                        @endif
                                        <div class="space-y-3">
                                            <input type="text" name="areas_to_improve[{{ $i }}][area]" required
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                placeholder="Area name (e.g., Attendance)"
                                                value="{{ $area['area'] }}">
                                            <textarea name="areas_to_improve[{{ $i }}][details]" rows="2" required
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                                                placeholder="Specific details about what needs improvement...">{{ $area['details'] }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="area-row p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="space-y-3">
                                        <input type="text" name="areas_to_improve[0][area]" required
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Area name (e.g., Attendance)">
                                        <textarea name="areas_to_improve[0][details]" rows="2" required
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                                            placeholder="Specific details about what needs improvement..."></textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Items -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Action Items</h2>
                            <button type="button" id="addActionBtn"
                                class="px-3 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 dark:bg-amber-900/20 dark:text-amber-400 rounded-lg hover:bg-amber-100">
                                + Add Action
                            </button>
                        </div>
                        <div id="actionsContainer" class="space-y-4">
                            @if (count($actions) > 0)
                                @foreach ($actions as $i => $action)
                                    <div class="action-row p-4 border border-gray-200 dark:border-gray-700 rounded-lg {{ $i > 0 ? 'relative' : '' }}">
                                        @if ($i > 0)
                                            <button type="button" onclick="this.closest('.action-row').remove()"
                                                class="absolute top-2 right-2 text-red-400 hover:text-red-600 text-xs">
                                                <i class="fi fi-rr-trash"></i>
                                            </button>
                                        @endif
                                        <div class="flex gap-3">
                                            <div class="flex-1">
                                                <input type="text" name="action_items[{{ $i }}][description]" required
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                    placeholder="Describe the action item..."
                                                    value="{{ $action['description'] }}">
                                            </div>
                                            <div class="w-40">
                                                <input type="date" name="action_items[{{ $i }}][target_date]" required
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                    value="{{ $action['target_date'] }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="action-row p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex gap-3">
                                        <div class="flex-1">
                                            <input type="text" name="action_items[0][description]" required
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                placeholder="Describe the action item...">
                                        </div>
                                        <div class="w-40">
                                            <input type="date" name="action_items[0][target_date]" required
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                value="{{ now()->addWeeks(2)->toDateString() }}">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <button type="submit"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                            Create Improvement Plan
                        </button>
                    </div>

                    <!-- Evaluation Summary -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Evaluation Summary</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Overall Rating</span>
                                <span class="font-bold text-{{ $evaluation->getRatingColor() }}-600">
                                    {{ number_format($evaluation->overall_rating, 1) }}/5
                                </span>
                            </div>
                            @foreach (\App\Models\PerformanceEvaluation::CRITERIA as $field => $label)
                                @if ($evaluation->$field <= 2)
                                    <div class="flex justify-between">
                                        <span class="text-red-500 dark:text-red-400">{{ $label }}</span>
                                        <span class="font-medium text-red-600 dark:text-red-400">{{ $evaluation->$field }}/5</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let areaCount = {{ count($areas) > 0 ? count($areas) : 1 }};
            let actionCount = {{ count($actions) > 0 ? count($actions) : 1 }};

            document.getElementById('addAreaBtn').addEventListener('click', function() {
                const container = document.getElementById('areasContainer');
                const html = `
                    <div class="area-row p-4 border border-gray-200 dark:border-gray-700 rounded-lg relative">
                        <button type="button" onclick="this.closest('.area-row').remove()"
                            class="absolute top-2 right-2 text-red-400 hover:text-red-600 text-xs">
                            <i class="fi fi-rr-trash"></i>
                        </button>
                        <div class="space-y-3">
                            <input type="text" name="areas_to_improve[${areaCount}][area]" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Area name">
                            <textarea name="areas_to_improve[${areaCount}][details]" rows="2" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                                placeholder="Details..."></textarea>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', html);
                areaCount++;
            });

            document.getElementById('addActionBtn').addEventListener('click', function() {
                const container = document.getElementById('actionsContainer');
                const html = `
                    <div class="action-row p-4 border border-gray-200 dark:border-gray-700 rounded-lg relative">
                        <button type="button" onclick="this.closest('.action-row').remove()"
                            class="absolute top-2 right-2 text-red-400 hover:text-red-600 text-xs">
                            <i class="fi fi-rr-trash"></i>
                        </button>
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <input type="text" name="action_items[${actionCount}][description]" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Action item description...">
                            </div>
                            <div class="w-40">
                                <input type="date" name="action_items[${actionCount}][target_date]" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', html);
                actionCount++;
            });
        });
    </script>
    @endpush
</x-layouts.general-employer>
