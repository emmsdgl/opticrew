@props([
    'headerName' => null,   
    'headerDesc' => null,   
    'headerIcon' => null,   
])

<div
    class="flex flex-col lg:flex-row items-center justify-center w-full max-w-full px-6 py-6 gap-4 border border-gray-300/30 dark:border-gray-700 rounded-lg shadow-sm relative bg-gradient-to-br from-[#1730F0] via-[#2A6DFA] to-[#4169E1] dark:bg-none dark:bg-gray-800">

    <!-- Left content -->
    <div class="w-full lg:w-2/3 pl-0 lg:pl-12 text-center lg:text-left relative z-10 py-6">
        <p class="text-sm sm:text-sm md:text-sm font-sans font-bold text-white opacity-90 max-w-[90%] mx-0 mb-3 lg:mx-0">
            <x-blur-text text="Applicant Dashboard" :delay="80" direction="top" :stepDuration="0.30" />
        </p>
        <h5 class="mb-4 text-3xl sm:text-3xl md:text-3xl lg:text-3xl font-sans font-bold tracking-tight text-white lg:my-2">
            <x-blur-text text="Hello, {{ $headerName }}" :delay="150" direction="top" :stepDuration="0.35" />
        </h5>
        <p class="text-sm sm:text-sm md:text-sm font-sans font-normal text-white opacity-90 max-w-[90%] mx-auto lg:mx-0">
            <x-blur-text text="{{ $headerDesc }}" :delay="80" direction="top" :stepDuration="0.30" />
        </p>
    </div>
        <div id="hero-icon" class="hidden md:hidden lg:block lg:w-[30rem] relative z-10">
            {{-- <img src="{{ asset('images/icons/'.$headerIcon.'.svg') }}" 
                alt="Hero Icon"
                class="absolute bottom-[-6rem] right-[-1rem] w-[22rem] sm:w-[18rem] md:w-[20rem] lg:w-[30rem]" /> --}}
        </div>

</div>
