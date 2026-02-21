<x-layouts.general-employee :title="'Task Details'">
    @php
        // Determine where the user came from to provide dynamic back navigation
        $from = request()->query('from', 'tasks');

        // Map source pages to their labels and routes
        $backNavigation = [
            'performance' => [
                'label' => 'Performance',
                'route' => route('employee.performance')
            ],
            'dashboard' => [
                'label' => 'Dashboard',
                'route' => route('employee.dashboard')
            ],
            'history' => [
                'label' => 'History',
                'route' => route('employee.history')
            ],
            'tasks' => [
                'label' => 'Tasks',
                'route' => route('employee.tasks')
            ],
        ];

        // Get the back navigation details, default to tasks if source is unknown
        $backNav = $backNavigation[$from] ?? $backNavigation['tasks'];
        $backLabel = $backNav['label'];
        $backUrl = $backNav['route'];
    @endphp

    <script>
        var __feedbackStoreUrl = @json(route('employee.tasks.feedback.store', $task->id));

        function feedbackModal() {
            return {
                showFeedbackModal: false,
                selectedRating: 0,
                selectedKeywords: [],
                feedbackText: '',

                closeFeedbackModal() {
                    this.showFeedbackModal = false;
                    this.selectedRating = 0;
                    this.selectedKeywords = [];
                    this.feedbackText = '';
                },

                toggleKeyword(keyword) {
                    const index = this.selectedKeywords.indexOf(keyword);
                    if (index > -1) {
                        this.selectedKeywords.splice(index, 1);
                    } else {
                        this.selectedKeywords.push(keyword);
                    }
                },

                isKeywordSelected(keyword) {
                    return this.selectedKeywords.includes(keyword);
                },

                async submitFeedback() {
                    if (this.selectedRating === 0) {
                        return;
                    }

                    try {
                        const response = await fetch(__feedbackStoreUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                rating: this.selectedRating,
                                keywords: this.selectedKeywords,
                                comment: this.feedbackText
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.closeFeedbackModal();
                            alert('Thank you for your feedback!');
                        } else {
                            alert('Error submitting feedback. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error submitting feedback. Please try again.');
                    }
                }
            }
        }
    </script>

    <div x-data="feedbackModal()">

        {{-- MOBILE LAYOUT (< 1024px) --}} <section role="status"
            class="w-full lg:hidden flex flex-col min-h-screen bg-white dark:bg-gray-900">

            {{-- Header with Back Button --}}
            <div
                class="sticky top-0 bg-white dark:bg-gray-900 z-10 px-4 py-4 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ $backUrl }}"
                    class="inline-flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="text-sm font-medium">Back to {{ $backLabel }}</span>
                </a>

                {{-- Completed Task Badge --}}
                @if($task->employee_approved === true && $task->status === 'Completed')
                    <div class="mt-3 bg-green-100 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg px-3 py-2 flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                        <span class="text-xs font-semibold text-green-700 dark:text-green-300">Task Completed - Great job!</span>
                    </div>
                @endif
            </div>

            {{-- Illustration and Title --}}
            <div class="px-6 py-6 text-center">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/task-illustration.svg') }}" alt="Task Illustration"
                        class="w-48 h-48 object-contain" onerror="this.style.display='none'">
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                    Task Details
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    The service is availed by
                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                        <!-- CHANGE, SHOULD BE FROM THE DATABASE -->
                        Kakslauttanen
                    </span>.
                    This task has been assigned to you and should be started in
                    <span class="font-semibold text-green-600 dark:text-green-400">
                        @php
                            $scheduledDate = \Carbon\Carbon::parse($task->scheduled_date);
                            if ($task->scheduled_time) {
                                $scheduledTime = \Carbon\Carbon::parse($task->scheduled_time);
                                $scheduledDateTime = $scheduledDate->setTimeFromTimeString($scheduledTime->format('H:i:s'));
                            } else {
                                $scheduledDateTime = $scheduledDate;
                            }
                            $now = \Carbon\Carbon::now();
                            $diff = $now->diff($scheduledDateTime);
                            $timeRemaining = '';
                            if ($diff->d > 0) {
                                $timeRemaining = $diff->d . ' days and ' . $diff->h . ' hours';
                            } elseif ($diff->h > 0) {
                                $timeRemaining = $diff->h . ' hrs and ' . $diff->i . ' mins';
                            } else {
                                $timeRemaining = $diff->i . ' mins';
                            }
                        @endphp
                        {{ $timeRemaining }}
                    </span>
                </p>
            </div>

            {{-- Task Details Summary Card --}}
            <div class="px-6 pb-6">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-sm">
                    <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                            Tasks Details Summary
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            View the details for this task
                        </p>
                    </div>

                    <div class="p-4 space-y-4">
                        {{-- Task ID --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Task ID</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $task->id }}
                            </span>
                        </div>

                        {{-- Task Date --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Task Date</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($task->scheduled_date)->format('Y-m-d') }}
                            </span>
                        </div>

                        {{-- Task Starting Time --}}
                        @if($task->scheduled_time)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Task Starting Time</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}
                                </span>
                            </div>
                        @endif

                        {{-- Task Type --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Task Type</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $task->task_description }}
                            </span>
                        </div>

                        {{-- Task Location --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Task Location</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white text-right">
                                @if($task->location)
                                    {{ $task->location->address ?? $task->location->location_name }}
                                @else
                                    External Client
                                @endif
                            </span>
                        </div>

                        {{-- Task Members --}}
                        @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Task Members</span>
                                <div class="flex -space-x-2">
                                    @foreach($task->optimizationTeam->members->take(3) as $member)
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 dark:from-gray-600 dark:dark:to-gray-700 border-2 border-white dark:border-gray-900 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                                                {{ strtoupper(substr($member->employee->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Special Requests --}}
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Special Requests</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ $task->notes ?? 'No special requests for this task.' }}
                            </p>
                        </div>

                        {{-- Status Message --}}
                        @if(is_null($task->employee_approved))
                            <div
                                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                <p class="text-xs text-blue-700 dark:text-blue-300 text-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    This task is awaiting your approval. Please accept or decline below.
                                </p>
                            </div>
                        @elseif($task->employee_approved === true)
                            <div
                                class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                                <p class="text-xs text-green-700 dark:text-green-700 text-center">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Task approved and ready to start
                                </p>
                            </div>
                        @else
                            <div
                                class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                                <p class="text-xs text-red-700 dark:text-red-300 text-center">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    You have declined this task
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="px-6 pb-8 mt-auto">
                @if(is_null($task->employee_approved))
                    {{-- Approval Buttons - Show when task needs approval --}}
                    <div class="flex gap-3">
                        <button type="button" onclick="document.getElementById('approve-task-form').submit()"
                            class="flex-1 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold py-4 rounded-full transition-colors shadow-lg shadow-green-600/30 dark:shadow-green-600/20">
                            <i class="fas fa-check mr-2"></i>Accept
                        </button>
                        <button type="button" onclick="if(confirm('Are you sure you want to decline this task?')) document.getElementById('decline-task-form').submit()"
                            class="flex-1 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold py-4 rounded-full transition-colors shadow-lg shadow-red-600/30 dark:shadow-red-600/20">
                            <i class="fas fa-times mr-2"></i>Decline
                        </button>
                    </div>
                    <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        Please accept or decline this task
                    </p>
                @elseif($task->employee_approved === true)
                    {{-- Task Action Buttons - Show when task is approved --}}
                    <div class="space-y-3">
                        @if($task->status === 'Pending' || $task->status === 'Scheduled')
                            {{-- Show Start Task button --}}
                            <button type="button" onclick="document.getElementById('start-task-form').submit()"
                                class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-4 rounded-full transition-colors shadow-lg shadow-blue-600/30 dark:shadow-blue-600/20">
                                <i class="fas fa-play mr-2"></i>Start Task
                            </button>
                        @elseif($task->status === 'In Progress')
                            {{-- Show Mark Complete button - disabled until all checklist items are completed --}}
                            <button type="button" id="mark-complete-btn-mobile"
                                onclick="if(confirm('Are you sure you want to mark this task as complete?')) document.getElementById('complete-task-form').submit()"
                                class="w-full bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold py-4 rounded-full transition-colors shadow-lg shadow-green-600/30 dark:shadow-green-600/20 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:shadow-none"
                                disabled>
                                <i class="fas fa-check mr-2"></i>Mark Complete
                            </button>
                            <p id="checklist-warning-mobile" class="text-xs text-amber-600 dark:text-amber-400 text-center mt-2">
                                <i class="fas fa-info-circle mr-1"></i>Complete all checklist items to enable
                            </p>
                        @endif
                    </div>
                @else
                    {{-- Declined State --}}
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4 text-center">
                        <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-3xl mb-2"></i>
                        <p class="text-sm font-semibold text-red-700 dark:text-red-300">Task Declined</p>
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">You have declined this task</p>
                    </div>
                @endif

                {{-- Rate this task (mobile) --}}
                <div class="my-6 text-center">
                    <button @click="showFeedbackModal = true" type="button"
                        class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                        <i class="fas fa-star"></i>
                        <span class="font-medium text-sm">Rate this task</span>
                    </button>
                </div>
            </div>
            </div>
            </section>

            {{-- DESKTOP LAYOUT (≥ 1024px) - Modern Sidebar Layout --}}
            <section role="status" class="w-full px-6 hidden lg:flex min-h-screen bg-gray-50 dark:bg-gray-900 rounded-lg">

                <!-- Main Content Area (Left Side - 70%) -->
                <div class="flex-1 px-12 overflow-y-auto">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ $backUrl }}"
                            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                            <i class="fas fa-arrow-left"></i>
                            <span class="font-medium text-sm">Back to {{ $backLabel }}</span>
                        </a>

                    </div>
                    
                    <!-- Task Title and Meta -->
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 mt-6">
                            {{ $task->task_description }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            Task #{{ $task->id }} • Created
                            {{ \Carbon\Carbon::parse($task->created_at)->format('M d, Y') }}
                        </p>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                        <nav class="flex gap-8" aria-label="Tabs">
                            <button onclick="switchTaskTab('details')" id="task-tab-details"
                                class="task-tab-button pb-4 border-b-2 font-medium text-sm transition-colors border-blue-500 text-blue-600 dark:text-blue-400">
                                Details
                            </button>
                            <button onclick="switchTaskTab('activities')" id="task-tab-activities"
                                class="task-tab-button pb-4 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                                Activities
                            </button>
                            <button onclick="switchTaskTab('checklist')" id="task-tab-checklist"
                                class="task-tab-button pb-4 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                                Checklist
                            </button>
                            <button onclick="switchTaskTab('team')" id="task-tab-team"
                                class="task-tab-button pb-4 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                                Team
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div>
                        <!-- Details Tab -->
                        <div id="task-content-details" class="task-tab-content">
                            <!-- Description Section -->
                            <div class="rounded-xl p-6 shadow-sm mb-6">
                                <h3
                                    class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                    <i class="fas fa-align-left text-gray-400"></i>
                                    Description
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                                    The service is availed by
                                    <span class="font-bold text-blue-600 dark:text-blue-400">
                                        <!-- CHNAGE, SHOULD BE FROM THE DATABASE -->
                                        Kakslauttanen
                                    </span>.
                                    This task has been assigned to you and should be started in
                                    <span class="font-bold text-green-600 dark:text-green-400">
                                        {{ $timeRemaining ?? '2 hrs and 12 mins' }}
                                    </span>
                                </p>
                            </div>

                            <!-- Location & Schedule Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                                <!-- Client Card -->
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-building text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Client
                                            </p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $task->location?->contractedClient?->name
                                                    ?? ($task->client ? trim(($task->client->first_name ?? '') . ' ' . ($task->client->last_name ?? '')) : null)
                                                    ?? 'Unknown Client' }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $task->location?->contractedClient ? 'Contracted Client'
                                                    : ($task->client ? 'External Client' : 'Unknown Type') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Location Card -->
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-map-marker-alt text-purple-600 dark:text-purple-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Service
                                                Location
                                            </p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                <!-- CHANGE, SHOULD BE FORM THE DATABASE -->
                                                Inari, Finland
                                            </p>
                                            @if($task->location && $task->location->address)
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $task->location->address }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Duration Card -->
                                @if($task->estimated_duration_minutes)
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-clock text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Estimated
                                                    Duration</p>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $task->estimated_duration_minutes }} minutes
                                                </p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                    ({{ number_format($task->estimated_duration_minutes / 60, 1) }} hours)
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Schedule Card -->
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-calendar text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                Scheduled
                                                Date & Time</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}
                                            </p>
                                            @if($task->scheduled_time)
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Vehicle Card -->
                                @if($task->optimizationTeam && $task->optimizationTeam->car)
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-car text-orange-600 dark:text-orange-400"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                                    Assigned
                                                    Vehicle</p>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $task->optimizationTeam->car->car_name }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Special Requests Section -->
                            <div class="rounded-xl p-6 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <h3
                                        class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                        <i class="fas fa-paperclip text-gray-400"></i>
                                        Special Requests
                                    </h3>
                                </div>
                                @if($task->notes)
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                        {{ $task->notes }}
                                    </p>
                                @else
                                    <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                                        <i class="fas fa-file text-3xl mb-2"></i>
                                        <p class="text-sm">No special requests yet</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Activities Tab -->
                        <div id="task-content-activities" class="task-tab-content hidden">
                            <div class="p-6">
                                <div class="space-y-6">
                                    @php
                                        // Build activity timeline
                                        $activities = collect();

                                        // Task created
                                        $activities->push([
                                            'type' => 'created',
                                            'icon' => 'fa-plus',
                                            'icon_color' => 'text-blue-600 dark:text-blue-400',
                                            'bg_color' => 'bg-blue-100 dark:bg-blue-900/30',
                                            'user' => ($task->assignedBy && isset($task->assignedBy->name)) ? $task->assignedBy->name : 'System',
                                            'action' => 'created this task',
                                            'timestamp' => $task->created_at,
                                        ]);

                                        // Task assigned to team
                                        if ($task->optimizationTeam) {
                                            $activities->push([
                                                'type' => 'assigned',
                                                'icon' => 'fa-users',
                                                'icon_color' => 'text-purple-600 dark:text-purple-400',
                                                'bg_color' => 'bg-purple-100 dark:bg-purple-900/30',
                                                'user' => ($task->assignedBy && isset($task->assignedBy->name)) ? $task->assignedBy->name : 'System',
                                                'action' => 'assigned this task to team',
                                                'timestamp' => $task->created_at,
                                            ]);
                                        }

                                        // Employee approval/decline
                                        if (!is_null($task->employee_approved)) {
                                            $activities->push([
                                                'type' => $task->employee_approved ? 'approved' : 'declined',
                                                'icon' => $task->employee_approved ? 'fa-check-circle' : 'fa-times-circle',
                                                'icon_color' => $task->employee_approved ? 'text-green-600 dark:text-green-800/30' : 'text-red-600 dark:text-red-400/30',
                                                'bg_color' => $task->employee_approved ? 'bg-green-100 dark:bg-green-800/30' : 'bg-red-100 dark:bg-red-900/30',
                                                'user' => $employee->user->name,
                                                'action' => $task->employee_approved ? 'approved this task' : 'declined this task',
                                                'timestamp' => $task->employee_approved_at,
                                            ]);
                                        }

                                        // Task started
                                        if (in_array($task->status, ['In Progress', 'Completed'])) {
                                            $activities->push([
                                                'type' => 'started',
                                                'icon' => 'fa-play',
                                                'icon_color' => 'text-blue-600 dark:text-blue-400',
                                                'bg_color' => 'bg-blue-100 dark:bg-blue-900/30',
                                                'user' => ($task->startedBy && isset($task->startedBy->name)) ? $task->startedBy->name : 'Team Member',
                                                'action' => 'started working on this task',
                                                'timestamp' => $task->started_at,
                                            ]);
                                        }

                                        // Task completed
                                        if ($task->status === 'Completed') {
                                            $activities->push([
                                                'type' => 'completed',
                                                'icon' => 'fa-check-circle',
                                                'icon_color' => 'text-green-600 dark:text-green-400',
                                                'bg_color' => 'bg-green-100 dark:bg-green-900/30',
                                                'user' => ($task->completedBy && isset($task->completedBy->name)) ? $task->completedBy->name : 'Team Member',
                                                'action' => 'marked this task as completed',
                                                'timestamp' => $task->completed_at,
                                            ]);
                                        }

                                        // Sort by timestamp (oldest first)
                                        $activities = $activities->sortBy('timestamp');
                                    @endphp

                                    <!-- Activity Timeline -->
                                    <div class="relative">
                                        <!-- Activity Items -->
                                        <div class="space-y-6">
                                            @foreach($activities as $activity)
                                                <div class="flex gap-4 relative">
                                                    <!-- Icon -->
                                                    <div class="flex-shrink-0 relative z-10">
                                                        <div class="w-8 h-8 {{ $activity['bg_color'] }} rounded-full flex items-center justify-center">
                                                            <i class="fas {{ $activity['icon'] }} {{ $activity['icon_color'] }} text-xs"></i>
                                                        </div>
                                                    </div>

                                                    <!-- Content -->
                                                    <div class="flex-1 pt-0.5">
                                                        <p class="text-sm text-gray-900 dark:text-white">
                                                            <span class="font-semibold">{{ $activity['user'] }}</span>
                                                            {{ $activity['action'] }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            @if($activity['timestamp'])
                                                                {{ \Carbon\Carbon::parse($activity['timestamp'])->format('M d, Y g:i A') }}
                                                                <span class="text-gray-400 dark:text-gray-500">•</span>
                                                                {{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}
                                                            @else
                                                                <span class="text-gray-400 dark:text-gray-500 italic">Time not recorded</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Checklist Tab -->
                        <div id="task-content-checklist" class="task-tab-content hidden">
                            <div class="p-6 shadow-sm">
                                <!-- Header -->
                                <div class="mb-6">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                        Tasks Checklist
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        @if($task->status === 'In Progress')
                                            Mark items as completed as you work
                                        @elseif($task->employee_approved && in_array($task->status, ['Pending', 'Scheduled']))
                                            Start the task to enable checklist editing
                                        @elseif($task->status === 'Completed')
                                            Task completed - checklist is locked. Approve and start the task first.
                                        @else
                                            Accept the task to enable checklist
                                        @endif
                                    </p>
                                </div>

                                @php
                                    // Checklist can only be edited when task is In Progress
                                    $canEditChecklist = $task->employee_approved === true && $task->status === 'In Progress';

                                    // Get existing checklist completions from database
                                    $checklistCompletions = $task->checklistCompletions->keyBy('checklist_item_id');
                                @endphp

                                <!-- Status Message for non-editable states -->
                                @if(!$canEditChecklist && $task->status !== 'Completed')
                                    <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                        <p class="text-xs text-yellow-700 dark:text-yellow-300 text-center">
                                            <i class="fas fa-lock mr-1"></i>
                                            @if(!$task->employee_approved)
                                                Accept the task to enable checklist editing
                                            @else
                                                Start the task to enable checklist editing
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                <!-- Checklist Items -->
                                <div class="space-y-3">
                                    @php
                                        // Checklist templates organized by service type
                                        $checklistTemplates = [
                                            'daily_cleaning' => [
                                                'Sweep and mop floors',
                                                'Vacuum carpets/rugs',
                                                'Dust furniture and surfaces',
                                                'Wipe tables and countertops',
                                                'Empty trash bins',
                                                'Wipe kitchen counters',
                                                'Clean sink',
                                                'Wash visible dishes',
                                                'Wipe appliance exteriors',
                                                'Clean toilet and sink',
                                                'Wipe mirrors',
                                                'Mop floor',
                                                'Organize cluttered areas',
                                                'Light deodorizing',
                                            ],
                                            'snowout_cleaning' => [
                                                'Remove mud, water, and debris',
                                                'Clean door mats',
                                                'Mop and dry floors',
                                                'Deep vacuum carpets',
                                                'Mop with disinfectant solution',
                                                'Wipe walls near entrances',
                                                'Dry wet surfaces',
                                                'Check for water accumulation',
                                                'Clean and sanitize affected areas',
                                                'Dispose of tracked-in debris',
                                                'Replace trash liners',
                                            ],
                                            'deep_cleaning' => [
                                                'Dust high and low areas (vents, corners, baseboards)',
                                                'Clean behind and under furniture',
                                                'Wash walls and remove stains',
                                                'Deep vacuum carpets',
                                                'Clean inside microwave',
                                                'Degrease stove and range hood',
                                                'Clean inside refrigerator (if included)',
                                                'Scrub tile grout',
                                                'Remove limescale and mold buildup',
                                                'Deep scrub tiles and grout',
                                                'Sanitize all fixtures thoroughly',
                                                'Clean window interiors',
                                                'Polish handles and knobs',
                                                'Disinfect frequently touched surfaces',
                                            ],
                                            'general_cleaning' => [
                                                'Dust surfaces',
                                                'Sweep/vacuum floors',
                                                'Mop hard floors',
                                                'Clean glass and mirrors',
                                                'Wipe countertops',
                                                'Clean sink',
                                                'Take out trash',
                                                'Clean toilet, sink, and mirror',
                                                'Mop floor',
                                                'Arrange items neatly',
                                                'Dispose of garbage',
                                                'Light air freshening',
                                            ],
                                            'hotel_cleaning' => [
                                                'Make bed with fresh linens',
                                                'Replace pillowcases and sheets',
                                                'Dust all surfaces (tables, headboard, shelves)',
                                                'Vacuum carpet / sweep & mop floor',
                                                'Clean mirrors and glass surfaces',
                                                'Check under bed for trash/items',
                                                'Empty trash bins and replace liners',
                                                'Clean and disinfect toilet',
                                                'Scrub shower walls, tub, and floor',
                                                'Clean sink and countertop',
                                                'Polish fixtures',
                                                'Replace towels, bath mat, tissue, and toiletries',
                                                'Mop bathroom floor',
                                                'Refill water, coffee, and room amenities',
                                                'Replace slippers and hygiene kits',
                                                'Check minibar (if applicable)',
                                                'Ensure lights, AC, TV working',
                                                'Arrange curtains neatly',
                                                'Deodorize room',
                                            ],
                                        ];

                                        // Determine service type from task description
                                        $taskDescription = strtolower($task->task_description ?? '');
                                        $serviceType = 'general_cleaning'; // Default

                                        if (str_contains($taskDescription, 'daily') || str_contains($taskDescription, 'routine')) {
                                            $serviceType = 'daily_cleaning';
                                        } elseif (str_contains($taskDescription, 'snowout') || str_contains($taskDescription, 'weather')) {
                                            $serviceType = 'snowout_cleaning';
                                        } elseif (str_contains($taskDescription, 'deep')) {
                                            $serviceType = 'deep_cleaning';
                                        } elseif (str_contains($taskDescription, 'hotel') || str_contains($taskDescription, 'room turnover')) {
                                            $serviceType = 'hotel_cleaning';
                                        }

                                        $checklistItems = $checklistTemplates[$serviceType] ?? $checklistTemplates['general_cleaning'];

                                        // Count completed items
                                        $completedCount = 0;
                                        foreach($checklistItems as $index => $item) {
                                            if(isset($checklistCompletions[$index]) && $checklistCompletions[$index]->is_completed) {
                                                $completedCount++;
                                            }
                                        }
                                    @endphp

                                    @forelse($checklistItems as $index => $item)
                                        @php
                                            $isCompleted = isset($checklistCompletions[$index]) && $checklistCompletions[$index]->is_completed;
                                        @endphp
                                        <label
                                            class="flex items-start gap-3 p-3 rounded-lg {{ $canEditChecklist ? 'hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer' : 'cursor-default' }} transition-colors group">
                                            <div class="flex items-center h-6 mt-0.5">
                                                <input type="checkbox" id="checklist-{{ $index }}"
                                                    data-item-index="{{ $index }}"
                                                    data-item-name="{{ $item }}"
                                                    class="checklist-item w-4 h-4 text-green-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-green-500 dark:focus:ring-green-600 focus:ring-2 {{ $canEditChecklist ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}"
                                                    {{ $isCompleted ? 'checked' : '' }}
                                                    {{ !$canEditChecklist ? 'disabled' : '' }}
                                                    onchange="toggleChecklistItem(this)">
                                            </div>

                                            <!-- Item Text -->
                                            <div class="flex-1">
                                                <span
                                                    class="text-sm {{ $isCompleted ? 'line-through text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-300' }} group-hover:text-gray-900 dark:group-hover:text-white transition-colors checklist-text-{{ $index }}">
                                                    {{ $item }}
                                                </span>
                                            </div>
                                        </label>
                                    @empty
                                        <!-- Empty State -->
                                        <div class="text-center py-12">
                                            <i class="fas fa-clipboard-list text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm">No checklist items</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Checklist items will appear here</p>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Progress Bar -->
                                @if(count($checklistItems) > 0)
                                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                <span id="checklist-completed">{{ $completedCount }}</span> of
                                                <span id="checklist-total">{{ count($checklistItems) }}</span> completed
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            @php
                                                $progressPercent = count($checklistItems) > 0 ? ($completedCount / count($checklistItems)) * 100 : 0;
                                            @endphp
                                            <div id="checklist-progress-bar"
                                                class="{{ $completedCount === count($checklistItems) ? 'bg-green-600' : 'bg-blue-600' }} h-2 rounded-full transition-all duration-300"
                                                style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Team Tab -->
                        <div id="task-content-team" class="task-tab-content hidden">
                            @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                                <div class="rounded-xl p-6 shadow-sm">
                                    <h3
                                        class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <i class="fas fa-users text-gray-400"></i>
                                        Team Members
                                    </h3>
                                    <div class="space-y-3">
                                        @foreach($task->optimizationTeam->members as $member)
                                            <div
                                                class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                                                    {{ strtoupper(substr($member->employee->user->name, 0, 1)) }}
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $member->employee->user->name }}
                                                    </p>
                                                    @if($member->role === 'driver')
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Driver</p>
                                                    @else
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Team Member</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                                    <div class="text-center py-12">
                                        <i class="fas fa-users text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">No team members assigned</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Right Side - 30%) -->
                <div
                    class="w-96 rounded-xl bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 p-6 overflow-y-auto">
                    <!-- Status Section -->
                    <div class="mb-6">
                        <label
                            class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-2">
                            <i class="fas fa-info-circle"></i>
                            Status
                        </label>
                        @php
                            $statusColors = [
                                'Scheduled' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                'In Progress' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'On Hold' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                'Completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                'Pending' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                            ];
                            $statusClass = $statusColors[$task->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';

                            $statusIcons = [
                                'Completed' => 'fa-check-circle',
                                'In Progress' => 'fa-spinner',
                                'Scheduled' => 'fa-clock',
                                'Pending' => 'fa-hourglass-half',
                                'On Hold' => 'fa-pause-circle',
                            ];
                            $statusIcon = $statusIcons[$task->status] ?? null;
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $statusClass }}">
                            @if($statusIcon)
                                <i class="fas {{ $statusIcon }} mr-1.5 text-xs"></i>
                            @endif
                            {{ $task->status }}
                        </span>
                        @php
                            $priorityColors = [
                                'Not Urgent' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                'Priority' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'Urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                            ];
                            $priorityClass = $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';

                            $priorityIcons = [
                                'Urgent' => 'fa-exclamation-circle',
                                'Priority' => 'fa-flag',
                                'Not Urgent' => 'fa-check',
                            ];
                            $priorityIcon = $priorityIcons[$task->priority] ?? null;
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $priorityClass }}">
                            @if($priorityIcon)
                                <i class="fas {{ $priorityIcon }} mr-1.5 text-xs"></i>
                            @endif
                            {{ $task->priority ?? 'Not Urgent' }}
                        </span>
                    </div>

                    <!-- Rooms/Units Section -->
                    <div class="mb-6">
                        <label
                            class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-2">
                            <i class="fas fa-door-open"></i>
                            Rooms/Units (1)
                        </label>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            @php
                                // Get room/cabin name
                                $roomName = null;
                                if ($task->location) {
                                    // For contracted clients - use location name
                                    $roomName = $task->location->location_name;
                                } else {
                                    // For external clients - extract cabin name from task_description
                                    // Format: "ServiceType - CabinName" e.g. "Deep Cleaning - Kelo A"
                                    if (preg_match('/^(.+?)\s*-\s*(.+)$/', $task->task_description, $matches)) {
                                        $roomName = trim($matches[2]);
                                    }
                                }
                            @endphp
                            @if($roomName)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    <i class="fas fa-door-open text-blue-500 mr-1.5 text-xs"></i>
                                    {{ $roomName }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    <i class="fas fa-door-open text-gray-500 mr-1.5 text-xs"></i>
                                    No room specified
                                </span>
                            @endif
                        </p>
                    </div>

                    <!-- Assigned Section -->
                    @if($task->optimizationTeam && $task->optimizationTeam->members->isNotEmpty())
                        <div class="mb-6">
                            <label
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-2">
                                <i class="fas fa-users"></i>
                                Assigned Members
                            </label>
                            <div class="flex items-center gap-2 flex-wrap">
                                @foreach($task->optimizationTeam->members->take(3) as $member)
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold"
                                        title="{{ $member->employee->user->name }}">
                                        {{ strtoupper(substr($member->employee->user->name, 0, 1)) }}
                                    </div>
                                @endforeach
                                @if($task->optimizationTeam->members->count() > 3)
                                    <button
                                        class="w-8 h-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-full flex items-center justify-center text-gray-400 hover:border-gray-400 hover:text-gray-600 dark:hover:border-gray-500 dark:hover:text-gray-300 transition-colors text-xs">
                                        +{{ $task->optimizationTeam->members->count() - 3 }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Tags Section -->
                    <div class="mb-6">
                        <label
                            class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-2">
                            <i class="fas fa-tag"></i>
                            Tags
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <span
                                class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                                <i class="fas fa-briefcase text-purple-500 mr-1.5 text-xs"></i>
                                {{ $task->task_description }}
                            </span>
                            <!-- CHANGE, SHOULD BE FROM THE DATABASE (TYPE OF CLIENT) -->
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                <i class="fas fa-map-marker-alt text-blue-500 mr-1.5 text-xs"></i>
                                External Client
                            </span>
                        </div>
                    </div>

                    <!-- Assigned By Section -->
                    @if($task->assigned_by)
                        <div class="mb-6">
                            <label
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-2">
                                <i class="fas fa-user-tie"></i>
                                Assigned By
                            </label>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $task->assigned_by->name }}
                            </p>
                        </div>
                    @endif

                    @if(is_null($task->employee_approved))
                        <!-- Approval Action Buttons - Show when task needs approval -->
                        <div class="space-y-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" onclick="document.getElementById('approve-task-form').submit()"
                                class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                                <i class="fas fa-check"></i>
                                Accept Task
                            </button>
                            <button type="button" onclick="if(confirm('Are you sure you want to decline this task?')) document.getElementById('decline-task-form').submit()"
                                class="w-full px-4 py-2.5 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg border border-gray-300 dark:border-gray-600 transition-colors text-sm flex items-center justify-center gap-2">
                                <i class="fas fa-times"></i>
                                Decline Task
                            </button>
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-4 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Please accept or decline this task
                        </p>

                    @elseif($task->employee_approved === true)
                        <!-- Task Action Buttons - Show when task is approved -->
                        <div class="space-y-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            @if($task->status === 'Pending' || $task->status === 'Scheduled')
                                {{-- Show Start Task button --}}
                                <button type="button" onclick="document.getElementById('start-task-form').submit()"
                                    class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-play"></i>
                                    Start Task
                                </button>
                            @elseif($task->status === 'In Progress')
                                {{-- Show Mark Complete button - disabled until all checklist items are completed --}}
                                <button type="button" id="mark-complete-btn-desktop"
                                    onclick="if(confirm('Are you sure you want to mark this task as complete?')) document.getElementById('complete-task-form').submit()"
                                    class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors text-sm flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    disabled>
                                    <i class="fas fa-check"></i>
                                    Mark Complete
                                </button>
                                <p id="checklist-warning-desktop" class="text-xs text-amber-600 dark:text-amber-400 text-center mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>Complete all checklist items to enable
                                </p>
                            @endif
                        </div>

                    @else
                        <!-- Declined State -->
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-center">
                                <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-2xl mb-2"></i>
                                <p class="text-sm font-semibold text-red-700 dark:text-red-300">Task Declined</p>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">You have declined this task</p>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Rate this task -->
                    <div class="my-6 text-center">
                        <button @click="showFeedbackModal = true" type="button"
                            class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                            <i class="fas fa-star"></i>
                            <span class="font-medium text-sm">Rate this task</span>
                        </button>
                    </div>
                </div>
            </section>

            <!-- Feedback Modal (teleported to body to avoid CSS containment issues) -->
            <template x-teleport="body">
            <div x-show="showFeedbackModal" x-cloak @click="closeFeedbackModal()"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 dark:bg-black/70 p-4"
                style="display: none;">
                <div @click.stop
                    class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-800 overflow-hidden"
                    x-show="showFeedbackModal" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">

                    <!-- Close button -->
                    <button type="button" @click="closeFeedbackModal()"
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-gray-900 dark:bg-gray-800 text-white rounded-full hover:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700 z-10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Modal Body -->
                    <div class="p-8 sm:p-10">
                        <!-- Header -->
                        <div class="text-center flex flex-col gap-2 my-6">
                            <p class="text-xs text-gray-500 dark:text-gray-400 tracking-wide">
                                Your feedback matters
                            </p>
                            <h3
                                class="text-3xl sm:text-3xl font-bold text-gray-900 dark:text-white leading-tight my-3">
                                How would you rate<br class="hidden sm:block">this task?
                            </h3>
                            <p
                                class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
                                Your input is valuable in helping us better understand your needs.
                            </p>
                        </div>

                        <!-- Emoji Rating -->
                        <div class="flex justify-center items-end gap-2 sm:gap-3 mb-10">
                            @php
                                $emojis = [
                                    1 => asset('images/icons/emojis/Very-Dissatisfied.svg'),
                                    2 => asset('images/icons/emojis/Dissatisfied.svg'),
                                    3 => asset('images/icons/emojis/Neutral.svg'),
                                    4 => asset('images/icons/emojis/Satisfied.svg'),
                                    5 => asset('images/icons/emojis/Very-Satisfied.svg')
                                ];
                            @endphp
                            @foreach($emojis as $rating => $emojiSrc)
                                <button @click="selectedRating = {{ $rating }}"
                                    :class="selectedRating === {{ $rating }} ? 'scale-100 sm:scale-100' : 'scale-100'"
                                    class="relative flex flex-col items-center transition-all duration-200 focus:outline-none group"
                                    type="button">
                                    <div class="rounded-full flex items-center justify-center transition-all duration-200"
                                        :class="selectedRating === {{ $rating }} 
                                            ? 'bg-blue-600 dark:bg-blue-500 ring-4 ring-blue-200 dark:ring-blue-900 w-14 h-14 sm:w-16 sm:h-16' 
                                            : 'bg-gray-200 dark:bg-gray-800 w-12 h-12 sm:w-14 sm:h-14 group-hover:bg-gray-300 dark:group-hover:bg-gray-700'">
                                        <img src="{{ $emojiSrc }}" alt="Rating {{ $rating }}"
                                            :class="selectedRating === {{ $rating }} ? 'w-8 h-8 sm:w-10 sm:h-10' : 'w-6 h-6 sm:w-8 sm:h-8 grayscale opacity-60'"
                                            class="transition-all duration-200">
                                    </div>
                                    <span x-show="selectedRating === {{ $rating }}" x-transition
                                        class="absolute -bottom-8 text-xs font-semibold text-white bg-blue-600 dark:bg-blue-500 px-3 py-1 rounded-full whitespace-nowrap shadow-lg">
                                        {{ $rating }}.0 Medium
                                    </span>
                                </button>
                            @endforeach
                        </div>

                        <!-- Keyword Tags -->
                        <div class="mt-12 mb-4">
                            <div class="flex flex-wrap justify-center gap-2">
                                @php
                                    $keywords = [
                                        'Well-Scheduled',
                                        'Clear Instructions',
                                        'Professional Standards',
                                        'Hygiene-Compliant',
                                        'Time-Efficient',
                                        'Rushed Timeline',
                                        'Well-Defined Steps',
                                        'Skill-Appropriate'
                                    ];
                                @endphp
                                @foreach($keywords as $keyword)
                                    <button @click="toggleKeyword('{{ $keyword }}')"
                                        :class="isKeywordSelected('{{ $keyword }}') 
                                                ? 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 border-gray-900 dark:border-gray-100' 
                                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:border-gray-400 dark:hover:border-gray-600'"
                                        type="button"
                                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs font-medium border rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 dark:focus:ring-gray-700">
                                        {{ $keyword }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Detailed Review -->
                        <div class="mb-6">
                            <label class="block text-sm text-gray-900 dark:text-white mb-2">
                                Detailed Review
                            </label>
                            <textarea x-model="feedbackText" rows="3" placeholder="Add a comment"
                                class="w-full px-4 py-3 text-sm text-gray-900 dark:text-white border-0 bg-gray-50 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 focus:outline-none placeholder-gray-400 dark:placeholder-gray-500 resize-none transition-all"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button @click="submitFeedback()" :disabled="selectedRating === 0" :class="selectedRating === 0 
                    ? 'opacity-50 cursor-not-allowed bg-blue-600 dark:bg-blue-800' 
                    : 'bg-blue-900 dark:bg-blue-700 hover:bg-blue-800 dark:hover:bg-blue-600'" type="button"
                            class="w-full px-6 py-3.5 sm:py-4 text-sm font-bold text-white rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 dark:focus:ring-blue-700 disabled:hover:bg-blue-900 dark:disabled:hover:bg-blue-800">
                            Submit Feedback
                        </button>
                    </div>
                </div>
            </div>
            </template>

            @push('scripts')
                <script>
                    // Task tab switching functionality
                    function switchTaskTab(tabName) {
                        // Hide all tab contents
                        document.querySelectorAll('.task-tab-content').forEach(content => {
                            content.classList.add('hidden');
                        });

                        // Remove active state from all tabs
                        document.querySelectorAll('.task-tab-button').forEach(button => {
                            button.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                            button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
                        });

                        // Show selected tab content
                        document.getElementById('task-content-' + tabName).classList.remove('hidden');

                        // Add active state to selected tab
                        const activeTab = document.getElementById('task-tab-' + tabName);
                        activeTab.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                        activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
                    }

                    // Toggle checklist item and save to database
                    async function toggleChecklistItem(checkbox) {
                        const itemIndex = checkbox.dataset.itemIndex;
                        const itemName = checkbox.dataset.itemName;
                        const isCompleted = checkbox.checked;

                        // Update text styling
                        const textElement = document.querySelector('.checklist-text-' + itemIndex);
                        if (textElement) {
                            if (isCompleted) {
                                textElement.classList.add('line-through', 'text-gray-400', 'dark:text-gray-500');
                                textElement.classList.remove('text-gray-700', 'dark:text-gray-300');
                            } else {
                                textElement.classList.remove('line-through', 'text-gray-400', 'dark:text-gray-500');
                                textElement.classList.add('text-gray-700', 'dark:text-gray-300');
                            }
                        }

                        // Update progress immediately for better UX
                        updateChecklistProgress();

                        // Save to database via AJAX
                        try {
                            // Get total items count for progress notifications
                            const totalItems = document.querySelectorAll('.checklist-item').length;

                            const response = await fetch('{{ route("employee.tasks.checklist.toggle", $task->id) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    item_index: parseInt(itemIndex),
                                    item_name: itemName,
                                    is_completed: isCompleted,
                                    total_items: totalItems
                                })
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                // Revert checkbox state on error
                                checkbox.checked = !isCompleted;
                                updateChecklistProgress();
                                alert(data.error || 'Failed to update checklist item');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            // Revert checkbox state on error
                            checkbox.checked = !isCompleted;
                            updateChecklistProgress();
                            alert('Error updating checklist item. Please try again.');
                        }
                    }

                    // Checklist progress update functionality
                    function updateChecklistProgress() {
                        // Get all checklist items
                        const checklistItems = document.querySelectorAll('.checklist-item');
                        const totalItems = checklistItems.length;

                        // Count checked items
                        let checkedItems = 0;
                        checklistItems.forEach(item => {
                            if (item.checked) {
                                checkedItems++;
                            }
                        });

                        // Calculate percentage
                        const percentage = totalItems > 0 ? (checkedItems / totalItems) * 100 : 0;

                        // Update progress bar
                        const progressBar = document.getElementById('checklist-progress-bar');
                        if (progressBar) {
                            progressBar.style.width = percentage + '%';

                            // Update color based on completion
                            if (checkedItems === totalItems && totalItems > 0) {
                                progressBar.classList.remove('bg-blue-600');
                                progressBar.classList.add('bg-green-600');
                            } else {
                                progressBar.classList.remove('bg-green-600');
                                progressBar.classList.add('bg-blue-600');
                            }
                        }

                        // Update counter text
                        const completedCounter = document.getElementById('checklist-completed');
                        if (completedCounter) {
                            completedCounter.textContent = checkedItems;
                        }

                        // Enable/disable Mark Complete buttons based on checklist completion
                        const allCompleted = checkedItems === totalItems && totalItems > 0;

                        // Mobile button
                        const mobileBtn = document.getElementById('mark-complete-btn-mobile');
                        const mobileWarning = document.getElementById('checklist-warning-mobile');
                        if (mobileBtn) {
                            mobileBtn.disabled = !allCompleted;
                            if (allCompleted) {
                                mobileBtn.classList.remove('disabled:bg-gray-400', 'disabled:cursor-not-allowed', 'disabled:shadow-none');
                            }
                        }
                        if (mobileWarning) {
                            mobileWarning.style.display = allCompleted ? 'none' : 'block';
                        }

                        // Desktop button
                        const desktopBtn = document.getElementById('mark-complete-btn-desktop');
                        const desktopWarning = document.getElementById('checklist-warning-desktop');
                        if (desktopBtn) {
                            desktopBtn.disabled = !allCompleted;
                            if (allCompleted) {
                                desktopBtn.classList.remove('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                            }
                        }
                        if (desktopWarning) {
                            desktopWarning.style.display = allCompleted ? 'none' : 'block';
                        }
                    }

                    // Initialize progress on page load
                    document.addEventListener('DOMContentLoaded', function () {
                        updateChecklistProgress();
                    });
                </script>
            @endpush

        {{-- Hidden Forms for Approval Actions --}}
        <form id="approve-task-form" action="{{ route('employee.tasks.approve', $task->id) }}" method="POST" style="display: none;">
            @csrf
        </form>
        <form id="decline-task-form" action="{{ route('employee.tasks.decline', $task->id) }}" method="POST" style="display: none;">
            @csrf
        </form>

        {{-- Hidden Forms for Task Actions --}}
        <form id="start-task-form" action="{{ route('employee.tasks.start', $task->id) }}" method="POST" style="display: none;">
            @csrf
        </form>
        <form id="complete-task-form" action="{{ route('employee.tasks.complete', $task->id) }}" method="POST" style="display: none;">
            @csrf
        </form>

    </div>
</x-layouts.general-employee>