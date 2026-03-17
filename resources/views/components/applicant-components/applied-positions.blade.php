@props([
    'applications' => collect(),
    'jobPostings'  => collect(),
])

@php
    $total = $applications->count();
@endphp

<style>
.ap-scroll::-webkit-scrollbar { display: none; }
.ap-scroll { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="w-full min-w-0 flex flex-col gap-4">

    {{-- ── Cards ── --}}
    @if($total > 0)
    <div class="ap-scroll flex flex-nowrap gap-4 overflow-x-auto pb-1 w-full max-w-full">
        @foreach($applications as $application)
            @php
                $matchedJob = $jobPostings->firstWhere('title', $application->job_title);
            @endphp
            <div class="flex-shrink-0 w-72">
                <x-applicant-components.applied-job-card
                    :application="$application"
                    :job="$matchedJob"
                />
            </div>
        @endforeach
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-12 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-800/30">
        <i class="fa-regular fa-folder-open text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
        <p class="text-sm font-medium text-gray-400 dark:text-gray-500">No applications yet</p>
        <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Browse open positions and apply</p>
    </div>
    @endif

</div>
