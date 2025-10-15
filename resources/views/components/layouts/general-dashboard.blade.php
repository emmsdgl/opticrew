<!-- THIS IS THE DASHBOARD LAYOUT FOR ALL THE MODULE DASHBOARD -->
@props([
    'title' => ''
])

<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
    <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- SCRIPT TO APPLY THE SAVED THEME ON PAGE LOAD -->
    <script>
        // Checks localStorage for a 'theme' item. If it exists and is 'dark', or if it doesn't exist but the user's OS prefers dark mode, it adds the 'dark' class to the HTML element.
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
        }
    </script>

    @livewireStyles 
</head>
<style>
    #page-loader {
        transition: opacity 0.4s ease;
    }

    #sidebar nav a {
        transition: all 0.3s ease;
    }

    #sidebar nav a i {
        min-width: 20px;
        /* keeps icons consistent */
        text-align: center;
    }

    @keyframes rotate-flip {
        0% {
            transform: rotateY(0deg);
        }

        50% {
            transform: rotateY(180deg);
        }

        100% {
            transform: rotateY(360deg);
        }
    }

    /* Animation class applied by JS */
    .rotate-flip {
        animation: rotate-flip 0.4s ease-in-out;
        transform-origin: center;
        display: inline-block;
    }

    @keyframes spinOnce {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .rotate-once {
        animation: spinOnce 0.5s ease;
    }
</style>

<body class="bg-[#F3F3F3] text-gray-700 dark:bg-[#0F172A] dark:text-gray-100 font-sans transition-colors duration-300">
    <div class="flex min-h-screen">
        {{$sidebar}}

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col transition-colors duration-300">

        <!-- DASHBOARD HEADER CONTENTS -->
            <x-header />
            
            <!-- DASHBOARD PANEL CONTENTS -->
            {{ $slot }}

        </main>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const html = document.documentElement;
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const themeToggle = document.getElementById('theme-toggle');
            const icon = document.getElementById('theme-icon');

            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const currentTheme = savedTheme || (prefersDark ? 'dark' : 'light');

            applyTheme(currentTheme);
            updateIconRotation(currentTheme === 'dark'); // Set initial icon orientation

            themeToggle.addEventListener('click', () => {
                icon.classList.add('rotate-flip');
                icon.addEventListener('animationend', () => {
                    icon.classList.remove('rotate-flip');
                }, { once: true });

                const isDark = html.classList.contains('dark');
                const newTheme = isDark ? 'light' : 'dark';

                applyTheme(newTheme);
                updateIconRotation(newTheme === 'dark');
            });

            sidebarToggle.addEventListener('click', () => {
                const collapsed = sidebar.classList.contains('w-20');

                if (collapsed) {
                    // Expand sidebar
                    sidebar.classList.replace('w-20', 'w-64');
                    document.querySelectorAll('.nav-label, .sidebar-logo-text, .logout-label, .sidebar-logo')
                        .forEach(el => el.classList.remove('hidden'));
                    sidebarToggle.querySelector('i').className = 'fi fi-rr-angle-small-left';
                } else {
                    // Collapse sidebar
                    sidebar.classList.replace('w-64', 'w-20');
                    document.querySelectorAll('.nav-label, .sidebar-logo-text, .logout-label, .sidebar-logo')
                        .forEach(el => el.classList.add('hidden'));
                    sidebarToggle.querySelector('i').className = 'fi fi-rr-angle-small-right';
                }
            });

            function applyTheme(mode) {
                let iconClasses = '';

                if (mode === 'dark') {
                    html.classList.add('dark');
                    html.setAttribute('data-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                    iconClasses = 'fi fi-rr-moon text-indigo-300';
                } else {
                    html.classList.remove('dark');
                    html.setAttribute('data-theme', 'light');
                    localStorage.setItem('theme', 'light');
                    iconClasses = 'fi fi-rr-brightness text-yellow-500';
                }

                // Keep transition/rotation classes
                const keepClasses = Array.from(icon.classList).filter(cls =>
                    cls.startsWith('rotate-') || cls.startsWith('transition-') ||
                    cls.startsWith('duration-') || cls.startsWith('ease-')
                ).join(' ');

                icon.className = `${iconClasses} ${keepClasses}`;
            }

            function updateIconRotation(isDark) {
                icon.classList.remove('rotate-0', 'rotate-180');
                icon.classList.add(isDark ? 'rotate-180' : 'rotate-0');
            }

        });
    </script>

    {{-- ADD THIS NEW SCRIPT BLOCK --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 400); // Wait for the fade-out transition
            }
        });
    </script>
    @stack('scripts')
    @livewireScripts
</body>

</html>