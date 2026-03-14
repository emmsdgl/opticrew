@props([
    'job',
    'alreadyApplied' => false,
    'application'    => null,
    'isSaved'        => false,
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
        'fa-briefcase'       => 'Operations',
        'fa-wrench'          => 'Maintenance',
    ];
    $defaultSkills = [
        'Cleaning'          => ['Surface Sanitization', 'Disinfection Procedures', 'Waste Disposal', 'Deep Cleaning', 'Carpet Cleaning', 'Restroom Sanitation'],
        'Management'        => ['Team Leadership', 'Staff Supervision', 'Task Delegation', 'Performance Monitoring', 'Workflow Management'],
        'Logistics'         => ['Inventory Management', 'Supply Coordination', 'Route Planning', 'Resource Scheduling', 'Time Management'],
        'Quality Assurance' => ['Quality Inspection', 'Safety Compliance', 'Cleaning Standards', 'Process Monitoring', 'Issue Reporting', 'Quality Control'],
        'Customer Service'  => ['Client Communication', 'Complaint Handling', 'Service Coordination', 'Professional Communication', 'Client Support'],
        'Operations'        => ['Operations Coordination', 'Task Prioritization', 'Workflow Optimization', 'Resource Management', 'Service Monitoring', 'Operational Reporting'],
        'Maintenance'       => ['Equipment Maintenance', 'Preventive Maintenance', 'Facility Maintenance', 'Minor Repairs', 'Equipment Troubleshooting', 'Maintenance Reporting'],
    ];
    $color    = $iconColors[$job->icon_color] ?? $iconColors['blue'];
    $category = $categoryMap[$job->icon]      ?? 'General';
    $skills   = ($job->required_skills && count($job->required_skills) > 0)
        ? $job->required_skills
        : ($defaultSkills[$category] ?? []);
@endphp

<div
    x-data="{ slide: 0, saved: {{ $isSaved ? 'true' : 'false' }}, saving: false }"
    class="flex flex-col rounded-2xl p-4 overflow-hidden shadow-sm border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800/80 h-[320px]">

    {{-- ── Slide 1 — Card Face ── --}}
    <div
        x-show="slide === 0"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-3"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-3"
        class="flex flex-col flex-1 p-4 gap-2 min-h-0">

        {{-- Company / Type row --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full {{ $color['bg'] }} {{ $color['text'] }} {{ $color['dark_bg'] }} {{ $color['dark_text'] }} flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid {{ $job->icon ?? 'fa-briefcase' }} text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 leading-none my-1">{{ $category }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 leading-none">{{ $job->type_badge }}</p>
                </div>
            </div>
            <button
                @click.stop="if(saving) return; saving = true;
                    fetch('{{ route('applicant.jobs.toggle-save', $job->id) }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
                    }).then(r => r.json()).then(d => { saved = d.saved; saving = false; }).catch(() => saving = false)"
                :class="saved ? 'text-blue-500 dark:text-blue-400' : 'text-gray-300 dark:text-gray-600 hover:text-gray-600 dark:hover:text-gray-300'"
                class="transition-colors">
                <i :class="saved ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark'" class="text-base text-gray-300 dark:text-gray-400"></i>
            </button>
        </div>

        {{-- Job title + arrow --}}
        <div class="flex items-start justify-between gap-2 w-full">
            <h3 class="text-base pr-10 font-bold text-gray-900 dark:text-white leading-snug line-clamp-2 flex-1">
                {{ $job->title }}
            </h3>
            <button
                @click="slide = 1"
                class="w-7 h-7 rounded-full bg-blue-600 dark:bg-white flex items-center justify-center flex-shrink-0 hover:scale-110 transition-transform shadow-md mt-0.5">
                <i class="fa-solid fa-arrow-right text-white dark:text-gray-900 text-[9px]"></i>
            </button>
        </div>

        {{-- Location --}}
        @if($job->location)
        <p class="text-[10px] text-gray-400 dark:text-gray-500 mb-2">
            <i class="fa-solid fa-location-dot mr-1"></i>{{ $job->location }}
        </p>
        @endif

        {{-- Description snippet --}}
        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-3">
            {{ $job->description }}
        </p>

        {{-- Footer: salary + action --}}
        <div class="flex items-center justify-between py-2 mt-1 border-t border-gray-200/70 dark:border-gray-700/70">
            @if($job->salary)
            <span class="text-sm font-bold text-gray-800 dark:text-white">€{{ $job->salary }}</span>
            @else
            <span class="text-[10px] text-gray-400 dark:text-gray-500">Salary negotiable</span>
            @endif

            @if($alreadyApplied)
            <button @click="slide = 1"
                class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-blue-600 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                <i class="fa-solid fa-check mr-1"></i>Applied
            </button>
            @else
            <button @click="$dispatch('open-apply-modal', { title: @js($job->title), type: @js($job->type), requiredDocs: @js($job->required_docs ?? []) })"
                class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-blue-600 dark:bg-white text-white dark:text-gray-900 hover:bg-blue-700 dark:hover:bg-gray-100 transition-colors">
                Apply Now
            </button>
            @endif
        </div>
    </div>

    {{-- ── Slide 2 — Drawer ── --}}
    <div
        x-show="slide === 1"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-3"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-3"
        class="flex flex-col flex-1 min-h-0">

        {{-- Drawer header --}}
        <div class="flex items-center gap-2 px-4 pt-4 pb-3 border-b border-gray-200/70 dark:border-gray-700/70">
            <button @click="slide = 0"
                class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
                <i class="fa-solid fa-arrow-left text-gray-600 dark:text-gray-300 text-xs"></i>
            </button>
            <span class="text-xs font-bold text-gray-800 dark:text-white truncate">{{ $job->title }}</span>
        </div>

        {{-- Drawer body — same layout for both states --}}
        <div class="px-4 py-3 space-y-3 flex-1 overflow-y-auto min-h-0">

            {{-- Full description --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-1">About the Role</p>
                <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ $job->description ?? 'No description available.' }}
                </p>
            </div>

            {{-- Required skills --}}
            @if(count($skills) > 0)
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-1.5">Requirements</p>
                <div class="flex flex-wrap gap-1">
                    @foreach($skills as $skill)
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200/60 dark:border-gray-600">
                        {{ $skill }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Benefits --}}
            @if($job->benefits && count($job->benefits) > 0)
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-1.5">Benefits</p>
                <div class="space-y-0.5">
                    @foreach(array_slice($job->benefits, 0, 4) as $benefit)
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-circle-check text-green-400 mr-1"></i>{{ $benefit }}
                    </p>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

    </div>

    {{-- ── Dot indicators ── --}}
    <div class="flex justify-center gap-1.5 pb-2">
        <button @click="slide = 0"
            :class="slide === 0 ? 'w-4 bg-blue-600 dark:bg-gray-200' : 'w-1.5 bg-gray-300 dark:bg-gray-600'"
            class="h-1 rounded-full transition-all duration-300">
        </button>
        <button @click="slide = 1"
            :class="slide === 1 ? 'w-4 bg-blue-600 dark:bg-gray-200' : 'w-1.5 bg-gray-300 dark:bg-gray-600'"
            class="h-1 rounded-full transition-all duration-300">
        </button>
    </div>

</div>
