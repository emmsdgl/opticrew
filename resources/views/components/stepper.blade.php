@props([
    'steps' => [],
    'currentStep' => 1,
    'maxWidth' => '4xl'
])

<div class="py-6 sm:py-8">
    <div class="relative max-w-{{ $maxWidth }} mx-auto px-4">
        <!-- Progress Line -->
        <div class="stepper-line">
            <div class="stepper-progress" 
                 style="width: {{ count($steps) > 1 ? (($currentStep - 1) / (count($steps) - 1)) * 100 : 0 }}%">
            </div>
        </div>

        <!-- Steps -->
        <div class="relative flex justify-between items-center">
            @foreach($steps as $index => $step)
                @php
                    $stepNumber = $index + 1;
                    $isActive = $stepNumber == $currentStep;
                    $isCompleted = $stepNumber < $currentStep;
                @endphp
                
                <div class="flex flex-col items-center flex-1">
                    <!-- Step Circle -->
                    <div class="step-circle w-10 h-10 sm:w-12 sm:h-12 rounded-full items-center inline-flex justify-center font-semibold text-sm sm:text-base
                        {{ $isCompleted ? 'bg-blue-500 text-white' : ($isActive ? 'bg-blue-500 text-white ring-4 ring-blue-100 dark:ring-blue-900' : 'bg-white dark:bg-gray-700 text-gray-400 dark:text-gray-500 border-2 border-gray-300 dark:border-gray-600') }}">
                        @if($isCompleted)
                            <i class="fi fi-rr-check text-sm sm:text-base"></i>
                        @else
                            {{ $stepNumber }}
                        @endif
                    </div>

                    <!-- Step Label -->
                    <span class="mt-2 text-xs sm:text-sm font-medium text-center
                        {{ $isActive ? 'text-blue-600 dark:text-blue-400' : ($isCompleted ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500') }}">
                        {{ $step }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .stepper-line {
        position: absolute;
        top: 40%;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #E5E7EB;
        transform: translateY(-50%);
        z-index: 0;
    }

    .dark .stepper-line {
        background-color: #374151;
    }

    .stepper-progress {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background-color: #3B82F6;
        transition: width 0.3s ease;
    }

    .step-circle {
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
    }
</style>