@props([
    'headerName' => null,   
    'headerDesc' => null,   
    'headerIcon' => null,   
])

<div
    class="flex flex-col lg:flex-row align-items-center justify-center w-full max-w-full min-h-full px-6 py-10 gap-6 bg-[#2A6DFA] border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 relative">

    <!-- Left content -->
    <div class="w-full lg:w-2/3 pl-0 lg:pl-12 text-center lg:text-left z-10">
        <h5
            class="mb-4 text-3xl sm:text-3xl md:text-3xl lg:text-3xl font-sans font-bold tracking-tight text-white">
            Hello, <span id="headername" class="ml-1">{{ $headerName }}</span>
        </h5>
        <p id="headerdesc"
           class="text-sm sm:text-sm md:text-sm font-sans font-normal text-white opacity-90 max-w-[90%] mx-auto lg:mx-0">
            {{ $headerDesc }}
        </p>
    </div>
        <div id="hero-icon" class="hidden md:hidden lg:block lg:w-[30rem] relative">
            <img src="{{ asset('images/icons/'.$headerIcon.'.svg') }}" 
                alt="Hero Icon"
                class="absolute bottom-[-3rem] right-[-1rem] w-[22rem] sm:w-[18rem] md:w-[20rem] lg:w-[30rem]" />
        </div>

</div>
