@props([
    'title' => ''
])

<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$title}}</title>
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>
    <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
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
        text-align: center;
    }

    #sidebar.w-20 #sidebar-header {
        justify-content: center;
    }

    /* Hide logo container's flex-1 when collapsed so toggle centers properly */
    #sidebar.w-20 #sidebar-header > div:first-child {
        flex: 0;
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

    /* Custom Scrollbar - Applied Globally to All Elements */
    *::-webkit-scrollbar {
        width: 8px;
    }

    *::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 10px;
    }

    *::-webkit-scrollbar-thumb {
        background-color: rgba(107, 114, 128, 0.4);
        border-radius: 10px;
        transition: background-color 0.3s ease;
    }

    *::-webkit-scrollbar-thumb:hover {
        background-color: rgba(107, 114, 128, 0.7);
    }

    * {
        scrollbar-width: thin;
        scrollbar-color: rgba(107, 114, 128, 0.4) transparent;
    }
</style>

<body class="bg-white text-gray-700 dark:bg-[#0F172A] dark:text-gray-100 font-sans transition-colors duration-300 overflow-x-hidden">
    {{$sidebar}}

    <!-- MAIN CONTENT -->
    <main id="main-content" class="lg:ml-64 min-h-screen flex flex-col transition-all duration-300">
   
            <!--DASHBOARD HEADER CONTENTS -->
            @php
                $notifications = [
                    [
                        'message' => 'New comment on your post',
                        'time' => '5 minutes ago',
                        'unread' => true,
                        'icon' => 'fa-solid fa-comment',
                        'icon_bg' => 'blue',
                        'icon_color' => 'blue'
                    ],
                    [
                        'message' => 'Your account was logged in from a new device',
                        'time' => '1 hour ago',
                        'unread' => true,
                        'icon' => 'fa-solid fa-shield-halved',
                        'icon_bg' => 'red',
                        'icon_color' => 'red'
                    ],
                    [
                        'message' => 'Payment successful',
                        'time' => '2 hours ago',
                        'unread' => false,
                        'icon' => 'fa-solid fa-check-circle',
                        'icon_bg' => 'green',
                        'icon_color' => 'green'
                    ]
                ];
            @endphp

            <x-header :notifications="$notifications" />
            
            <!-- DASHBOARD PANEL CONTENTS -->
            {{ $slot }}

    </main>
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
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileSidebarClose = document.getElementById('mobile-sidebar-close');
            const sidebarBackdrop = document.getElementById('sidebar-backdrop');

            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const currentTheme = savedTheme || (prefersDark ? 'dark' : 'light');

            applyTheme(currentTheme);
            updateIconRotation(currentTheme === 'dark');

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

            const mainContent = document.getElementById('main-content');

            // Desktop sidebar toggle (collapse/expand)
            sidebarToggle.addEventListener('click', () => {
                const collapsed = sidebar.classList.contains('w-20');

                if (collapsed) {
                    sidebar.classList.replace('w-20', 'w-64');
                    if (window.innerWidth >= 1024) { // Only adjust margin on desktop
                        mainContent.classList.replace('lg:ml-20', 'lg:ml-64');
                    }
                    document.querySelectorAll('.nav-label, .sidebar-logo-text, .sidebar-logo')
                        .forEach(el => el.classList.remove('hidden'));
                    sidebarToggle.querySelector('i').className = 'fi fi-rr-angle-small-left';
                } else {
                    sidebar.classList.replace('w-64', 'w-20');
                    if (window.innerWidth >= 1024) { // Only adjust margin on desktop
                        mainContent.classList.replace('lg:ml-64', 'lg:ml-20');
                    }
                    document.querySelectorAll('.nav-label, .sidebar-logo-text, .sidebar-logo')
                        .forEach(el => el.classList.add('hidden'));
                    sidebarToggle.querySelector('i').className = 'fi fi-rr-angle-small-right';
                }
            });

            // Mobile menu toggle (slide in/out)
            function toggleMobileSidebar() {
                const isOpen = !sidebar.classList.contains('-translate-x-full');

                if (isOpen) {
                    // Close sidebar
                    sidebar.classList.add('-translate-x-full');
                    sidebarBackdrop.classList.add('hidden');
                    document.body.style.overflow = ''; // Restore scroll
                } else {
                    // Open sidebar
                    sidebar.classList.remove('-translate-x-full');
                    sidebarBackdrop.classList.remove('hidden');
                    document.body.style.overflow = 'hidden'; // Prevent scroll
                }
            }

            // Mobile menu button click
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', toggleMobileSidebar);
            }

            // Mobile close button click
            if (mobileSidebarClose) {
                mobileSidebarClose.addEventListener('click', toggleMobileSidebar);
            }

            // Backdrop click to close
            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', toggleMobileSidebar);
            }

            // Close sidebar when clicking nav links on mobile
            const navLinks = sidebar.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) {
                        toggleMobileSidebar();
                    }
                });
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

            // ===== DROPDOWN FUNCTIONALITY =====
            const profileToggle = document.getElementById('profile-dropdown-toggle');
            const profileDropdown = document.getElementById('profile-dropdown');
            const caretIcon = document.getElementById('caret-icon');
            const notificationToggle = document.getElementById('notification-toggle');
            const notificationDropdown = document.getElementById('notification-dropdown');

            // Toggle profile dropdown
            if (profileToggle && profileDropdown) {
                profileToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    const isVisible = !profileDropdown.classList.contains('invisible');
                    
                    if (notificationDropdown) closeNotificationDropdown();
                    
                    if (isVisible) {
                        closeProfileDropdown();
                    } else {
                        openProfileDropdown();
                    }
                });
            }

            // Toggle notification dropdown
            if (notificationToggle && notificationDropdown) {
                notificationToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    const isVisible = !notificationDropdown.classList.contains('invisible');
                    
                    if (profileDropdown) closeProfileDropdown();
                    
                    if (isVisible) {
                        closeNotificationDropdown();
                    } else {
                        openNotificationDropdown();
                    }
                });
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (profileDropdown && profileToggle && 
                    !profileDropdown.contains(e.target) && !profileToggle.contains(e.target)) {
                    closeProfileDropdown();
                }
                if (notificationDropdown && notificationToggle && 
                    !notificationDropdown.contains(e.target) && !notificationToggle.contains(e.target)) {
                    closeNotificationDropdown();
                }
            });

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (profileDropdown) closeProfileDropdown();
                    if (notificationDropdown) closeNotificationDropdown();
                }
            });

            function openProfileDropdown() {
                profileDropdown.classList.remove('invisible', 'opacity-0', 'scale-95');
                profileDropdown.classList.add('opacity-100', 'scale-100');
                if (caretIcon) caretIcon.classList.add('rotate-180');
            }

            function closeProfileDropdown() {
                profileDropdown.classList.add('invisible', 'opacity-0', 'scale-95');
                profileDropdown.classList.remove('opacity-100', 'scale-100');
                if (caretIcon) caretIcon.classList.remove('rotate-180');
            }

            function openNotificationDropdown() {
                notificationDropdown.classList.remove('invisible', 'opacity-0', 'scale-95');
                notificationDropdown.classList.add('opacity-100', 'scale-100');
            }

            function closeNotificationDropdown() {
                notificationDropdown.classList.add('invisible', 'opacity-0', 'scale-95');
                notificationDropdown.classList.remove('opacity-100', 'scale-100');
            }

        });
    </script>

    <script>
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