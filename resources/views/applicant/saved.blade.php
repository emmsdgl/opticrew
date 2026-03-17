<x-layouts.general-applicant title="Saved">

    {{-- Welcome Header --}}
    <x-applicant-components.herocard
        headerName="{{ $user->name }}"
        headerDesc="Your saved job positions."
        headerIcon=""
    />

    {{-- Saved Jobs --}}
    <x-labelwithvalue
        label="Saved Positions"
        value="({{ $jobPostings->count() }})"
        class="mt-6 mb-2"
    />

    @if($jobPostings->count() > 0)
    @php
        $appliedTitles = $myApplications->pluck('job_title')->map(fn($t) => strtolower(trim($t)))->toArray();
    @endphp
    <div class="flex gap-4 overflow-x-auto pb-1" style="-ms-overflow-style:none;scrollbar-width:none;">
        @foreach($jobPostings as $job)
        <div class="flex-shrink-0 w-72">
            <x-applicant-components.job-card
                :job="$job"
                :alreadyApplied="in_array(strtolower(trim($job->title)), $appliedTitles)"
                :application="null"
                :isSaved="in_array($job->id, $savedJobIds)"
            />
        </div>
        @endforeach
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-12 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-800/30">
        <i class="fa-regular fa-bookmark text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
        <p class="text-sm font-medium text-gray-400 dark:text-gray-500">No saved positions yet</p>
        <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Bookmark job postings to save them here for later</p>
    </div>
    @endif

    {{-- Apply Modal --}}
    <x-applicant-components.apply-modal />

</x-layouts.general-applicant>
