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
        <div class="flex-shrink-0 w-[calc(33.333%-0.67rem)]">
            <x-applicant-components.job-card
                :job="$job"
                :alreadyApplied="false"
                :application="null"
                :isSaved="in_array($job->id, $savedJobIds)"
            />
        </div>
        @endforeach
    </div>

    {{-- Filtered empty state (hidden by default, shown by JS when all cards are filtered out) --}}
    <div id="avp-filtered-empty" class="hidden">
        <x-applicant-components.empty-state
            icon="fa-solid fa-filter"
            title="No matching positions"
            description="No job postings match your current filters. Try adjusting your criteria."
        />
    </div>
    @else
    <x-applicant-components.empty-state
        icon="fa-solid fa-briefcase"
        title="No open positions"
        description="Check back later for new opportunities."
    />
    @endif

</div>
