<x-layouts.general-applicant title="Interviews">

    <div class="flex gap-4 h-full min-h-0">

        {{-- ── Left Panel: Small Calendar + Appointment List ── --}}
        <div class="w-72 flex-shrink-0 flex flex-col gap-4 min-h-0">

            {{-- Small Calendar --}}
            <x-applicant-components.small-calendar :interviews="$interviewApplications" />

            {{-- Interview Appointment List --}}
            <div class="bg-white dark:bg-gray-900/80 rounded-2xl shadow-sm flex flex-col flex-1 min-h-0 overflow-hidden">

                {{-- List header --}}
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700/50 flex-shrink-0">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Interview Appointments</h3>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300">{{ $interviewApplications->count() }}</span>
                </div>

                {{-- Scrollable list --}}
                <div class="flex-1 min-h-0 overflow-y-auto px-3 py-2 space-y-2">
                    @forelse($interviewApplications as $interview)
                        @php
                            $interviewDate = \Carbon\Carbon::parse($interview->interview_date);
                            $colors = [
                                'blue', 'purple', 'green', 'amber', 'cyan', 'pink'
                            ];
                            $colorKey = $colors[$loop->index % count($colors)];
                            $colorMap = [
                                'blue'   => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'border' => 'border-blue-200 dark:border-blue-800/40', 'icon' => 'text-blue-500', 'title' => 'text-blue-900 dark:text-blue-100'],
                                'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-900/20', 'border' => 'border-purple-200 dark:border-purple-800/40', 'icon' => 'text-purple-500', 'title' => 'text-purple-900 dark:text-purple-100'],
                                'green'  => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'border' => 'border-green-200 dark:border-green-800/40', 'icon' => 'text-green-500', 'title' => 'text-green-900 dark:text-green-100'],
                                'amber'  => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'border' => 'border-amber-200 dark:border-amber-800/40', 'icon' => 'text-amber-500', 'title' => 'text-amber-900 dark:text-amber-100'],
                                'cyan'   => ['bg' => 'bg-cyan-50 dark:bg-cyan-900/20', 'border' => 'border-cyan-200 dark:border-cyan-800/40', 'icon' => 'text-cyan-500', 'title' => 'text-cyan-900 dark:text-cyan-100'],
                                'pink'   => ['bg' => 'bg-pink-50 dark:bg-pink-900/20', 'border' => 'border-pink-200 dark:border-pink-800/40', 'icon' => 'text-pink-500', 'title' => 'text-pink-900 dark:text-pink-100'],
                            ];
                            $c = $colorMap[$colorKey];
                        @endphp
                        <div class="flex items-center gap-3 p-3 rounded-xl border {{ $c['border'] }} {{ $c['bg'] }} transition-all hover:shadow-sm">
                            {{-- Icon --}}
                            <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center {{ $c['bg'] }}">
                                <i class="fa-solid fa-calendar-check text-sm {{ $c['icon'] }}"></i>
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-bold truncate {{ $c['title'] }}">{{ $interview->job_title }}</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">{{ $interview->applicant_name ?? $interview->first_name }}</p>
                            </div>

                            {{-- Time --}}
                            <div class="flex-shrink-0 text-right">
                                <div class="flex items-center gap-1 text-gray-400 dark:text-gray-500">
                                    <i class="fa-regular fa-clock text-[9px]"></i>
                                    <span class="text-[10px] font-semibold">{{ $interviewDate->format('h:i A') }}</span>
                                </div>
                                <p class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $interviewDate->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <i class="fa-regular fa-calendar text-2xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500">No scheduled interviews</p>
                            <p class="text-[9px] text-gray-300 dark:text-gray-600 mt-0.5">Your interviews will appear here</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── Main Panel: Big Calendar (Weekly View) ── --}}
        <div class="flex-1 min-w-0 min-h-0 flex flex-col px-2 dark:px-6 shadow-sm">
            <x-applicant-components.big-calendar :interviews="$interviewApplications" />
        </div>

    </div>

</x-layouts.general-applicant>
