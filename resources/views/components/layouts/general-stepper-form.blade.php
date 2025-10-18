@props([
'title' => 'Form Title',
// Example steps: ['Client Details', 'Service Details', 'Confirmation']
'steps' => [],
'currentStep' => 1,
'logoText' => 'FINNOYS',
'logoSubtext' => ''
])

<!DOCTYPE html>

<html lang="en" class="transition-colors duration-300">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }}</title>
<!-- Flaticon/UI Icons -->
<link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Alpine JS -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<!-- Flowbite for Datepicker/UI components (if needed by slot content) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
<script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
<!-- Apexcharts (if needed by slot content) -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
// Initial check to apply dark mode class before content loads
if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
    document.documentElement.setAttribute('data-theme', 'dark');
} else {
    document.documentElement.classList.remove('dark');
    document.documentElement.setAttribute('data-theme', 'light');
}
</script>

@livewireStyles

<style>
    /* Cleaned up CSS */
    #page-loader {
        transition: opacity 0.4s ease;
    }

    @keyframes rotate-flip {
        0% { transform: rotateY(0deg); }
        50% { transform: rotateY(180deg); }
        100% { transform: rotateY(360deg); }
    }

    .rotate-flip {
        animation: rotate-flip 0.4s ease-in-out;
        transform-origin: center;
        display: inline-block;
    }

    /* Enhanced Stepper Styles */
    .stepper-container {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stepper-line-container {
        position: absolute;
        top: 40%;
        left: 10%;
        right: 10%;
        height: 2px;
        transform: translateY(-50%);
        z-index: 0;
    }

    .stepper-line {
        width: 100%;
        height: 100%;
        background-color: #BFDBFE; /* blue-200 */
        border-radius: 1px;
    }

    .dark .stepper-line {
        background-color: #1E3A8A; /* blue-900 */
    }

    .stepper-progress {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background-color: #3B82F6; /* blue-500 */
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 1px;
    }

    .step-item {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .step-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 18px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .dark .step-circle {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    /* Completed step */
    .step-circle.completed {
        background-color: #3B82F6; /* blue-500 */
        color: white;
    }

    /* Active/Current step */
    .step-circle.active {
        background-color: #3B82F6; /* blue-500 */
        color: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }

    .dark .step-circle.active {
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);
    }

    /* Inactive step */
    .step-circle.inactive {
        background-color: #BFDBFE; /* blue-200 */
        color: #3B82F6; /* blue-500 */
    }

    .dark .step-circle.inactive {
        background-color: #1E3A8A; /* blue-900 */
        color: #93C5FD; /* blue-300 */
    }

    .step-label {
        margin-top: 12px;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
        transition: color 0.3s ease;
    }

    /* Label colors */
    .step-label.completed {
        color: #3B82F6; /* blue-500 */
    }

    .step-label.active {
        color: #3B82F6; /* blue-500 */
        font-weight: 600;
    }

    .step-label.inactive {
        color: #9CA3AF; /* gray-400 */
    }

    .dark .step-label.completed {
        color: #60A5FA; /* blue-400 */
    }

    .dark .step-label.active {
        color: #60A5FA; /* blue-400 */
    }

    .dark .step-label.inactive {
        color: #6B7280; /* gray-500 */
    }
</style>


</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-100 font-sans transition-colors duration-300 min-h-screen flex flex-col">

<header class="bg-white dark:bg-[#1E293B] shadow transition-colors duration-300 sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-row gap-2 justify-center w-full items-center py-4 border-b border-gray-100 dark:border-gray-800">
            <!-- Logo (Updated to use text props) -->
            <div class="flex items-center space-x-1">
                <div class="flex-1 absolute">
                    <span class="text-blue-600 dark:text-blue-400">
                        <img src="{{ asset('/images/finnoys-text-logo-light.svg') }}" class="h-20 w-auto">
                    </span>
                </div>
            </div>

            <!-- Enhanced Stepper Component -->
            <div x-data="{ 
                steps: @js($steps), 
                current: @js($currentStep), 
                progressWidth() {
                    if (this.steps.length <= 1) return '0%';
                    const completedSteps = this.current - 1;
                    return (completedSteps / (this.steps.length - 1)) * 100 + '%';
                },
                getStepClass(index) {
                    const stepNum = index + 1;
                    if (stepNum < this.current) return 'completed';
                    if (stepNum === this.current) return 'active';
                    return 'inactive';
                }
            }"
            x-init="window.addEventListener('update-stepper', (e) => { current = e.detail.step; })"
            class="flex-1 max-w-4xl mx-auto px-12 hidden md:block">
                
                <div class="stepper-container py-4">
                    <!-- Progress Line Container -->
                    <div class="stepper-line-container">
                        <div class="stepper-line"></div>
                        <div class="stepper-progress" :style="{ width: progressWidth() }"></div>
                    </div>

                    @foreach ($steps as $index => $step)
                        <div class="step-item">
                            <!-- Circle -->
                            <div class="step-circle"
                                :class="getStepClass({{ $index }})">
                                {{ $index + 1 }}
                            </div>

                            <!-- Label -->
                            <span class="step-label"
                                :class="getStepClass({{ $index }})">
                                {{ $step }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" 
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <i id="theme-icon" class="fi text-xl transition-transform duration-300 ease-in-out"></i>
            </button>
        </div>
        
        <!-- Mobile Stepper Indicator -->
        <div class="md:hidden py-3">
            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Step {{ $currentStep }} of {{ count($steps) }}: 
                <span class="text-blue-600 dark:text-blue-400">{{ $steps[$currentStep - 1] ?? 'Untitled Step' }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
                <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300 ease-out" 
                    style="width: {{ count($steps) > 1 ? (($currentStep - 1) / (count($steps) - 1)) * 100 : 0 }}%">
                </div>
            </div>
        </div>

    </div>
</header>

<main class="flex-1 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{ $slot }}
    </div>
</main>

<!-- Footer could go here if needed -->
<!-- <footer class="py-4 bg-gray-100 dark:bg-gray-800 mt-auto">...</footer> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const html = document.documentElement;
        const themeToggle = document.getElementById('theme-toggle');
        const icon = document.getElementById('theme-icon');

        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const currentTheme = savedTheme || (prefersDark ? 'dark' : 'light');

        // Initialize theme on load
        applyTheme(currentTheme, false);

        themeToggle.addEventListener('click', () => {
            // Apply rotation animation
            icon.classList.add('rotate-flip');
            icon.addEventListener('animationend', () => {
                icon.classList.remove('rotate-flip');
            }, { once: true });

            const isDark = html.classList.contains('dark');
            const newTheme = isDark ? 'light' : 'dark';

            applyTheme(newTheme, true);
        });

        function applyTheme(mode, animate) {
            let iconClasses = '';

            if (mode === 'dark') {
                html.classList.add('dark');
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                iconClasses = 'fi-rr-moon text-indigo-300';
            } else {
                html.classList.remove('dark');
                html.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
                iconClasses = 'fi-rr-brightness text-yellow-500';
            }

            // Preserve necessary animation and transition classes
            const keepClasses = Array.from(icon.classList).filter(cls =>
                cls.startsWith('rotate-') || cls.startsWith('transition-') ||
                cls.startsWith('duration-') || cls.startsWith('ease-') ||
                cls === 'fi' || cls === 'text-xl'
            ).join(' ');

            // Update icon classes
            icon.className = `fi ${iconClasses} ${keepClasses}`;
            
            // Update icon's visual rotation (only on animation end for a smooth effect, or immediately on load)
            if (!animate) {
                icon.classList.remove('rotate-0', 'rotate-180');
                icon.classList.add(mode === 'dark' ? 'rotate-180' : 'rotate-0');
            }
        }
    });
</script>

<script>
    // Simple page loader logic (kept from original)
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 400);
        }
    });
</script>

@stack('scripts')
@livewireScripts


</body>

</html>