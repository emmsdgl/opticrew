@props([
    'application',
    'job' => null,
])

@php
    $iconColors = [
        'blue'   => ['bg' => 'bg-blue-100',   'text' => 'text-blue-600',   'dark_bg' => 'dark:bg-blue-900/40',   'dark_text' => 'dark:text-blue-300'],
        'green'  => ['bg' => 'bg-green-100',  'text' => 'text-green-600',  'dark_bg' => 'dark:bg-green-900/40',  'dark_text' => 'dark:text-green-300'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'dark_bg' => 'dark:bg-purple-900/40', 'dark_text' => 'dark:text-purple-300'],
        'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'dark_bg' => 'dark:bg-orange-900/40', 'dark_text' => 'dark:text-orange-300'],
        'red'    => ['bg' => 'bg-red-100',    'text' => 'text-red-600',    'dark_bg' => 'dark:bg-red-900/40',    'dark_text' => 'dark:text-red-300'],
    ];
    $categoryMap = [
        'fa-broom'           => 'Cleaning',
        'fa-user-tie'        => 'Management',
        'fa-dolly'           => 'Logistics',
        'fa-clipboard-check' => 'Quality Assurance',
        'fa-headset'         => 'Customer Service',
        'fa-spray-can'       => 'Sanitation',
        'fa-users'           => 'Team Lead',
        'fa-briefcase'       => 'Operations',
        'fa-wrench'          => 'Maintenance',
        'fa-folder-open'     => 'Administration',
    ];

    $icon     = $job?->icon      ?? 'fa-briefcase';
    $color    = $iconColors[$job?->icon_color ?? 'blue'];
    $category = $categoryMap[$icon] ?? 'General';

    $statusConfig = [
        'pending'              => ['label' => 'Pending',             'color' => 'yellow', 'icon' => 'fa-clock'],
        'reviewed'             => ['label' => 'Under Review',        'color' => 'blue',   'icon' => 'fa-eye'],
        'interview_scheduled'  => ['label' => 'Interview Scheduled', 'color' => 'purple', 'icon' => 'fa-calendar-check'],
        'hired'                => ['label' => 'Hired',               'color' => 'green',  'icon' => 'fa-circle-check'],
        'rejected'             => ['label' => 'Not Selected',        'color' => 'red',    'icon' => 'fa-circle-xmark'],
        'withdrawn'            => ['label' => 'Withdrawn',            'color' => 'gray',   'icon' => 'fa-rotate-left'],
    ];
    $status = $statusConfig[$application->status] ?? ['label' => ucfirst($application->status), 'color' => 'gray', 'icon' => 'fa-circle'];
@endphp

<style>
.applied-scroll::-webkit-scrollbar { display: none; }
.applied-scroll { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div
    x-data="{ open: false, showWithdrawConfirm: false, withdrawing: false }"
    class="flex flex-col p-4 rounded-2xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700/50 bg-white dark:bg-gray-800/80 p-2 h-[fit-content]">

    {{-- ── Scrollable content body ── --}}
    <div class="applied-scroll flex-1 overflow-y-auto min-h-0 p-4 pb-0 flex flex-col gap-2 mb-6">

        {{-- Icon / category / type row + status badge --}}
        <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-lg {{ $color['bg'] }} {{ $color['text'] }} {{ $color['dark_bg'] }} {{ $color['dark_text'] }} flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid {{ $icon }} text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 leading-none my-1">{{ $category }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 titlecase leading-none">{{ $application->job_type ?? $job?->type_badge }}</p>
                </div>
            </div>
            <x-badge :label="$status['label']" :icon="$status['icon']" :color="$status['color']" />
        </div>

        {{-- Job title --}}
        <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug line-clamp-2">
            {{ $application->job_title }}
        </h3>

        {{-- Location --}}
        @if($job?->location)
        <p class="text-xs text-gray-400 dark:text-gray-500">
            <i class="fa-solid fa-location-dot mr-1"></i>{{ $job->location }}
        </p>
        @endif

        {{-- Description --}}
        @if($job?->description)
        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-3">
            {{ $job->description }}
        </p>
        @endif
    </div>

    {{-- ── Pinned footer: applied date + View ── --}}
    <div class="flex items-center justify-between px-4 py-3 flex-shrink-0 border-t border-gray-200/70 dark:border-gray-700/70">
        <span class="text-[10px] text-gray-400 dark:text-gray-500">
            <i class="fa-regular fa-clock mr-1"></i>Applied {{ $application->created_at->diffForHumans() }}
        </span>
        <button @click="open = true"
            class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-blue-600 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-gray-100 transition-colors">
            View
        </button>
    </div>


    {{-- ── Teleported Slide-In Drawer ── --}}
    <template x-teleport="body">
        <div>
            {{-- Blurred backdrop --}}
            <div
                x-show="open"
                style="display:none"
                class="fixed inset-0 z-[99] bg-black/40 backdrop-blur-sm"
                @click="open = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
            </div>

            {{-- Drawer panel slides in from right --}}
            <div
                x-show="open"
                style="display:none"
                class="fixed right-0 top-0 h-full w-80 z-[100] bg-white dark:bg-gray-800 shadow-2xl flex flex-col overflow-hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full">

                {{-- Drawer header --}}
                <div class="flex items-center gap-2 px-4 pt-4 pb-3 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <button @click="open = false"
                        class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
                        <i class="fa-solid fa-xmark text-gray-600 dark:text-gray-300 text-xs"></i>
                    </button>
                    <span class="text-xs font-semibold text-gray-800 dark:text-white truncate flex-1">{{ $application->job_title }}</span>
                    <x-badge :label="$status['label']" :icon="$status['icon']" :color="$status['color']" class="flex-shrink-0" />
                </div>

                {{-- Drawer body (scrollable) --}}
                <div class="applied-scroll flex-1 overflow-y-auto px-8 py-10 space-y-4">

                    @if($job?->description)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-1">About the Role</p>
                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed text-justify">{{ $job->description }}</p>
                    </div>
                    @endif

                    @if($job?->required_skills && count($job->required_skills) > 0)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-1.5">Requirements</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($job->required_skills as $skill)
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200/60 dark:border-gray-600">
                                {{ $skill }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($job?->benefits && count($job->benefits) > 0)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-1.5">Benefits</p>
                        <div class="space-y-1">
                            @foreach($job->benefits as $benefit)
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-green-400 flex-shrink-0"></i>{{ $benefit }}
                            </p>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- ── Personal Information ── --}}
                    @php
                        $authUser  = auth()->user();
                        $p         = $application->applicant_profile
                                        ? json_decode($application->applicant_profile, true)
                                        : [];
                        $pv = fn(string $key, string $fallback = '—') =>
                                (isset($p[$key]) && $p[$key] !== '') ? $p[$key] : $fallback;
                        $nameParts = $authUser ? explode(' ', trim($authUser->name ?? ''), 2) : [];
                        $firstName = $pv('first_name', $nameParts[0] ?? '—');
                        $lastName  = $pv('last_name',  $nameParts[1] ?? '—');
                    @endphp
                    <div>
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 my-4">
                            <i class="fa-solid fa-user mr-1"></i>Personal Information
                        </p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                            @foreach([
                                ['First Name',        $firstName],
                                ['Last Name',         $lastName],
                                ['Middle Initial',    $pv('middle_initial')],
                                ['Birthdate',         $pv('birthdate')],
                                ['Phone / Mobile',    $pv('phone', $authUser?->phone ?? '—')],
                                ['Email Address',     $application->email],
                                ['Alternative Email', $pv('alternative_email', $application->alternative_email ?? '—')],
                                ['Country',           $pv('country')],
                                ['City',              $pv('city')],
                                ['LinkedIn Profile',  $pv('linkedin')],
                            ] as [$lbl, $val])
                            <div class="min-w-0">
                                <p class="text-[9px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider truncate">{{ $lbl }}</p>
                                <p class="text-[11px] text-gray-700 dark:text-gray-300 truncate {{ $val === '—' ? 'opacity-40' : '' }}">{{ $val }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Qualifications ── --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 my-4">
                            <i class="fa-solid fa-star mr-1"></i>Qualifications
                        </p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                            @foreach([
                                ['Skills',           $pv('skills')],
                                ['Languages Spoken', $pv('languages')],
                            ] as [$lbl, $val])
                            <div class="min-w-0">
                                <p class="text-[9px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider truncate">{{ $lbl }}</p>
                                <p class="text-[11px] text-gray-700 dark:text-gray-300 truncate {{ $val === '—' ? 'opacity-40' : '' }}">{{ $val }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Resume Submitted ── --}}
                    @if($application->resume_original_name)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-1.5">
                            <i class="fa-solid fa-file-lines mr-1"></i>Resume Submitted
                        </p>
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-2 border border-gray-200/60 dark:border-gray-600/40">
                            <i class="fa-regular fa-file-pdf text-red-400 text-sm flex-shrink-0"></i>
                            <span class="text-[10px] text-gray-600 dark:text-gray-300 truncate flex-1">{{ $application->resume_original_name }}</span>
                            @if($application->resume_path)
                            <a href="{{ asset("storage/{$application->resume_path}") }}" target="_blank"
                                class="text-blue-500 dark:text-blue-400 hover:text-blue-600 flex-shrink-0">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- ── Recruiter Feedback ── --}}
                    @if($application->admin_notes)
                    <div class="rounded-xl py-3">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-1.5">
                            <i class="fa-solid fa-comment-dots mr-1"></i>Recruiter Feedback
                        </p>
                        <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed italic">
                            "{{ $application->admin_notes }}"
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Drawer footer --}}
                <div class="px-4 pb-5 pt-2 flex-shrink-0 border-t border-gray-100 dark:border-gray-700 space-y-2">
                    @if(in_array($application->status, ['pending', 'reviewed', 'interview_scheduled']))
                    <button
                        @click="showWithdrawConfirm = true"
                        class="w-full py-2 rounded-xl text-xs font-semibold border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <i class="fa-solid fa-rotate-left mr-1.5"></i>Withdraw Application
                    </button>
                    @endif
                    <button disabled
                        class="w-full py-2 rounded-xl text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed">
                        <i class="fa-solid fa-check mr-1.5"></i>Already Applied
                    </button>
                </div>
            </div>

            {{-- Withdraw confirmation dialog --}}
            <div
                x-show="open && showWithdrawConfirm"
                style="display:none"
                class="fixed inset-0 z-[110] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center"
                    @click.stop
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100">
                    <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 text-lg"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Withdraw Application?</h3>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-5">
                        Are you sure you want to withdraw your application for <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $application->job_title }}</span>? This action cannot be undone.
                    </p>
                    <div class="flex gap-3">
                        <button type="button" @click="showWithdrawConfirm = false"
                            class="flex-1 py-2 rounded-xl text-xs font-semibold border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            Cancel
                        </button>
                        <button type="button"
                            :disabled="withdrawing"
                            @click="withdrawing = true;
                                fetch('{{ route('applicant.applications.withdraw', $application->id) }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
                                }).then(r => r.json()).then(d => {
                                    if (d.success) { window.location.reload(); }
                                    else { withdrawing = false; showWithdrawConfirm = false; }
                                }).catch(() => { withdrawing = false; showWithdrawConfirm = false; })"
                            class="flex-1 py-2 rounded-xl text-xs font-bold bg-red-600 text-white hover:bg-red-700 transition-colors disabled:opacity-50">
                            <span x-show="!withdrawing">Withdraw</span>
                            <span x-show="withdrawing"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Withdrawing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>
