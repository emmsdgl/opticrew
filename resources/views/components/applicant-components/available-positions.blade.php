@props([
    'jobPostings'    => collect(),
    'applications'   => collect(),
    'savedJobIds'    => [],
])

@php
    $appliedTitles   = $applications->pluck('job_title')->map(fn($t) => strtolower(trim($t)))->toArray();
    $available       = $jobPostings->filter(fn($job) => !in_array(strtolower(trim($job->title)), $appliedTitles));
    $total           = $available->count();
@endphp

<style>
.avp-scroll::-webkit-scrollbar { display: none; }
.avp-scroll { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="w-full min-w-0 flex flex-col gap-4">

    {{-- ── Cards ── --}}
    @if($total > 0)
    <div class="avp-scroll flex flex-nowrap gap-4 overflow-x-auto pb-1 w-full max-w-full">
        @foreach($available as $job)
        <div class="flex-shrink-0 w-72">
            <x-applicant-components.job-card
                :job="$job"
                :alreadyApplied="false"
                :application="null"
                :isSaved="in_array($job->id, $savedJobIds)"
            />
        </div>
        @endforeach
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-12 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-800/30">
        <i class="fa-regular fa-briefcase text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
        <p class="text-sm font-medium text-gray-400 dark:text-gray-500">No open positions at the moment</p>
        <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Check back later for new opportunities</p>
    </div>
    @endif

</div>
