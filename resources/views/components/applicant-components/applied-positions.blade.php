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
    <div class="ap-scroll flex flex-nowrap gap-4 overflow-x-auto pb-1 w-full">
        @foreach($applications as $application)
            @php
                $matchedJob = $jobPostings->firstWhere('title', $application->job_title);
            @endphp
            <div class="flex-shrink-0 w-[calc(33.333%-0.67rem)]">
                <x-applicant-components.applied-job-card
                    :application="$application"
                    :job="$matchedJob"
                />
            </div>
        @endforeach
    </div>
    @else
    <x-applicant-components.empty-state
        icon="fa-solid fa-folder-plus"
        title="No applications"
        description="Get started by creating a new application."
    />
    @endif

</div>
