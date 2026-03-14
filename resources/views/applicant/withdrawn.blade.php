<x-layouts.general-applicant title="Withdrawn Applications">

    {{-- Welcome Header --}}
    <x-applicant-components.herocard
        headerName="{{ $user->name }}"
        headerDesc="Applications you have withdrawn."
        headerIcon=""
    />

    {{-- Withdrawn Applications --}}
    <x-labelwithvalue
        label="Withdrawn Applications"
        count="{{ $withdrawnApplications->count() }}"
        class="mt-6 mb-2 text-blue-700 dark:text-gray-700"
    />

    @if($withdrawnApplications->count() > 0)
    <style>
    .withdrawn-scroll::-webkit-scrollbar { display: none; }
    .withdrawn-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <div class="withdrawn-scroll flex flex-nowrap gap-4 overflow-x-auto pb-1 w-full max-w-full">
        @foreach($withdrawnApplications as $application)
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
        <i class="fa-solid fa-rotate-left text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
        <p class="text-sm font-medium text-gray-400 dark:text-gray-500">No withdrawn applications</p>
        <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Applications you withdraw will appear here</p>
    </div>
    @endif

</x-layouts.general-applicant>
