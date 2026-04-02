<x-layouts.general-employer :title="'Employee Performance'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col md:items-center md:justify-between gap-4">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Analytics', 'url' => route('admin.analytics')],
                    ['label' => 'Reports', 'url' => route('admin.reports.index')],
                    ['label' => 'Employee Performance'],
                ]" />
            </div>
            <div class="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Employee Performance</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Monthly performance evaluations and improvement plans</p>
                </div>

                <!-- Month/Year Filter -->
                <div class="flex items-center gap-2">
                    <form method="GET" action="{{ route('admin.reports.performance.index') }}" class="flex items-center gap-2">
                        <select name="month" class="px-3 py-2 text-xs border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                        <select name="year" class="px-3 py-2 text-xs border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            @for ($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                            Filter
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.reports.performance.auto-fill-all') }}" class="inline" id="autoFillAllForm">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="button" class="px-4 py-2 text-xs font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 flex items-center gap-1"
                            onclick="confirmAutoFillAll()">
                            <i class="fi fi-rr-magic-wand text-xs"></i> Auto-Fill All
                        </button>
                    </form>
                    @if ($stats['drafts'] > 0)
                        <form method="POST" action="{{ route('admin.reports.performance.complete-all') }}" class="inline" id="completeAllForm">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <button type="button" class="px-4 py-2 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 flex items-center gap-1"
                                onclick="confirmCompleteAll()">
                                <i class="fi fi-rr-check-circle text-xs"></i> Complete All
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total Employees</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_employees'] }}</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <p class="text-xs text-green-600 dark:text-green-400 font-medium">Evaluated</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">{{ $stats['evaluated'] }}</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Drafts</p>
                <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300 mt-1">{{ $stats['drafts'] }}</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Pending</p>
                <p class="text-2xl font-bold text-gray-700 dark:text-gray-300 mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <p class="text-xs text-red-600 dark:text-red-400 font-medium">Active PIPs</p>
                <p class="text-2xl font-bold text-red-700 dark:text-red-300 mt-1">{{ $stats['active_pips'] }}</p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Evaluation Progress for {{ $periodStart->format('F Y') }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $stats['evaluated'] }}/{{ $stats['total_employees'] }}
                </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                @php $pct = $stats['total_employees'] > 0 ? ($stats['evaluated'] / $stats['total_employees']) * 100 : 0; @endphp
                <div class="bg-green-600 h-2.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
            </div>
        </div>

        <!-- Employee Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Employee</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Overall Rating</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Rating Label</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">PIP</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($employees as $employee)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $pic = $employee->user->profile_picture ?? null;
                                            $avatarUrl = $pic
                                                ? (str_starts_with($pic, 'http') ? $pic : (str_starts_with($pic, 'profile_pictures/') ? asset('storage/'.$pic) : asset($pic)))
                                                : asset('images/default-avatar.jpg');
                                        @endphp
                                        <div class="w-8 h-8 rounded-full overflow-hidden">
                                            <img src="{{ $avatarUrl }}" alt="{{ $employee->fullName }}"
                                                class="w-8 h-8 rounded-full object-cover"
                                                onerror="this.src='{{ asset('images/default-avatar.jpg') }}'">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->fullName }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->user->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($employee->current_evaluation)
                                        @if ($employee->current_evaluation->status === 'completed')
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full">Completed</span>
                                        @elseif ($employee->current_evaluation->status === 'draft')
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-full">Draft</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">{{ ucfirst($employee->current_evaluation->status) }}</span>
                                        @endif
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-full">Not Evaluated</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($employee->current_evaluation && $employee->current_evaluation->overall_rating)
                                        <div class="flex items-center gap-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fi fi-{{ $i <= round($employee->current_evaluation->overall_rating) ? 'sr' : 'rr' }}-star text-amber-400 text-xs"></i>
                                            @endfor
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ number_format($employee->current_evaluation->overall_rating, 1) }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($employee->current_evaluation && $employee->current_evaluation->overall_rating)
                                        @php $color = $employee->current_evaluation->getRatingColor(); @endphp
                                        <span class="text-xs font-medium text-{{ $color }}-600 dark:text-{{ $color }}-400">
                                            {{ $employee->current_evaluation->getRatingLabel() }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($employee->has_active_pip)
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full">Active PIP</span>
                                    @elseif ($employee->pip_pending)
                                        <span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded-full">PIP Needed</span>
                                    @else
                                        <span class="text-xs text-gray-400">None</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center gap-2 justify-end">
                                        @if ($employee->current_evaluation && $employee->current_evaluation->status === 'completed')
                                            <a href="{{ route('admin.reports.performance.show', $employee->current_evaluation->id) }}"
                                                class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30">
                                                View
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.reports.performance.evaluate', ['employeeId' => $employee->id, 'month' => $month, 'year' => $year]) }}"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700">
                                            {{ $employee->current_evaluation ? 'Edit' : 'Evaluate' }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No active employees found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        async function confirmAutoFillAll() {
            try {
                await window.showConfirmDialog(
                    'Auto-Fill All Evaluations',
                    'This will auto-fill evaluations for all employees who haven\'t been evaluated yet this month. You can still review and edit each one before completing. Continue?',
                    'Auto-Fill All',
                    'Cancel'
                );
            } catch (e) {
                return;
            }
            document.getElementById('autoFillAllForm').submit();
        }

        async function confirmCompleteAll() {
            try {
                await window.showConfirmDialog(
                    'Complete All Draft Evaluations',
                    'This will finalize all draft evaluations that have Strengths, Areas for Improvement, and Goals filled in. Drafts with missing fields will be skipped. Employees flagged for a Performance Improvement Plan will have one automatically created.\n\nThis action cannot be undone. Continue?',
                    'Complete All',
                    'Cancel'
                );
            } catch (e) {
                return;
            }
            document.getElementById('completeAllForm').submit();
        }
    </script>
    @endpush
</x-layouts.general-employer>
