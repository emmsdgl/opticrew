@props([
    'duration' => '40 mins',
    'title' => 'Course Title',
    'description' => 'Course description goes here',
    'progress' => 0,
    'buttonText' => 'Check now',
    'buttonUrl' => '#'
])

@php
    // Determine status based on progress
    $status = match(true) {
        $progress == 0 => 'No Progress',
        $progress >= 100 => 'Completed',
        default => 'Pending'
    };
    
    // Determine status color
    $statusColor = match(true) {
        $progress == 0 => 'text-red-600 dark:text-red-400',
        $progress >= 100 => 'text-green-600 dark:text-green-400',
        default => 'text-orange-600 dark:text-orange-400'
    };
    
    // Determine progress bar color
    $progressBarColor = match(true) {
        $progress == 0 => 'bg-red-600',
        $progress >= 100 => 'bg-green-600',
        default => 'bg-blue-600'
    };
@endphp

<div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl px-6 py-8 shadow-sm hover:shadow-md transition-shadow duration-300 relative overflow-hidden">
    {{-- Decorative Background Pattern --}}
    <div class="absolute top-0 right-0 opacity-5 dark:opacity-10">
        <svg width="180" height="180" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg">
            {{-- Book Icon --}}
            <path d="M40 30H100C105.523 30 110 34.477 110 40V140C110 145.523 105.523 150 100 150H40C34.477 150 30 145.523 30 140V40C30 34.477 34.477 30 40 30Z" 
                  stroke="currentColor" stroke-width="2" class="text-gray-400 dark:text-gray-600"/>
            <line x1="30" y1="50" x2="110" y2="50" stroke="currentColor" stroke-width="2" class="text-gray-400 dark:text-gray-600"/>
            {{-- Mop Icon --}}
            <circle cx="140" cy="120" r="8" fill="currentColor" class="text-gray-400 dark:text-gray-600"/>
            <line x1="140" y1="112" x2="140" y2="40" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="text-gray-400 dark:text-gray-600"/>
        </svg>
    </div>

    {{-- Duration Badge --}}
    <div class="flex items-center gap-2 mb-3">
        <div class="w-5 h-5 rounded-full bg-gray-900 dark:bg-white flex items-center justify-center">
            <svg class="w-3 h-3 text-white dark:text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
        </div>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $duration }}</span>
    </div>

    {{-- Title --}}
    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">
        {{ $title }}
    </h3>

    {{-- Description --}}
    <p class="text-xs text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
        {{ $description }}
    </p>

    {{-- Progress Bar Section --}}
    <div class="mb-4">
        {{-- Progress Bar Background --}}
        <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mb-2">
            {{-- Progress Bar Fill --}}
            <div class="{{ $progressBarColor }} h-full rounded-full transition-all duration-500 ease-out" 
                 style="width: {{ $progress }}%">
            </div>
        </div>
        
        {{-- Progress Info --}}
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $progress }}%</span>
            <span class="text-xs font-semibold {{ $statusColor }}">{{ $status }}</span>
        </div>
    </div>

    {{-- Action Button --}}
    <a href="{{ $buttonUrl }}" 
       class="inline-block w-full text-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-full transition-colors duration-200 shadow-sm hover:shadow-md">
        {{ $buttonText }}
    </a>
</div>