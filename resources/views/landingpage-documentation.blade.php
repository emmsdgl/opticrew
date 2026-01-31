@extends('components.layouts.general-landing')

@section('title', __('common.nav.home'))

@push ('styles')

    <style>
        /* Smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
            background-color: white;
        }

        body {
            background-image: none;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Remove background image in dark mode */
        .dark body {
            background-image: none;
            background-color: #1f2937;
        }

        /* Custom scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="w-full text-center">
                <h1 class="text-4xl font-bold text-slate-900 mb-4">Getting Started Guide</h1>
                <p class="text-md text-slate-600 leading-relaxed">
                    Welcome to CastCrew. This guide will walk you through the initial setup
                    and basic navigation for each user role to ensure a smooth onboarding process.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 bg-white">
        <div class="flex gap-12">
            <!-- Sidebar Navigation -->
            <aside class="w-64 flex-shrink-0 sticky top-8 self-start">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">On this page</p>

                    <nav class="space-y-1">
                        <a href="#accessing"
                            class="block py-2 px-3 text-sm font-medium text-blue-600 bg-blue-50 rounded-md">
                            Accessing the Platform
                        </a>
                        <a href="#employers"
                            class="block py-2 px-3 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition-colors">
                            For Employers
                        </a>
                        <a href="#employees"
                            class="block py-2 px-3 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition-colors">
                            For Employees
                        </a>
                        <a href="#clients"
                            class="block py-2 px-3 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition-colors">
                            For Clients
                        </a>
                        <a href="#requirements"
                            class="block py-2 px-3 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-md transition-colors">
                            System Requirements
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Main Documentation Content -->
            <main class="flex-1 max-w-3xl">
                <!-- Accessing the Platform Section -->
                <section id="accessing" class="mb-12 scroll-mt-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-6">1. Accessing the Platform</h2>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-slate-700 leading-relaxed"><span class="font-semibold">URL:</span> Navigate to
                                the system link provided by your administrator.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-slate-700 leading-relaxed"><span class="font-semibold">Login:</span> Enter
                                your registered email address and password.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-slate-700 leading-relaxed"><span class="font-semibold">Role
                                    Redirection:</span> The system will automatically detect your role (Admin, Employee, or
                                Client) and direct you to your specific dashboard.</span>
                        </li>
                    </ul>
                </section>

                <!-- For Employers Section -->
                <section id="employers" class="mb-12 scroll-mt-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">2. For Employers (Admins)</h2>

                    <p class="text-slate-700 leading-relaxed mb-6 font-semibold">
                        Goal: Set up your workforce and start accepting bookings.
                    </p>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">1. Complete Business Profile</h3>
                            <p class="text-slate-700 leading-relaxed">
                                Go to General Account Management (ERWM-GAM-001) to update your business name, billing
                                address, and contact details.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">2. Define Services</h3>
                            <p class="text-slate-700 leading-relaxed">
                                Set up your cleaning service types and pricing to enable the Provisional Billing feature for
                                clients.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">3. Recruit & Tag Staff</h3>
                            <ul class="space-y-2 ml-6 mt-2">
                                <li class="text-slate-700 leading-relaxed list-disc">Upload employee profiles.</li>
                                <li class="text-slate-700 leading-relaxed list-disc">
                                    <span class="font-semibold">Crucial Step:</span> Assign Skill Tags to each employee
                                    (e.g., "Deep Cleaning," "Sanitization").
                                    This allows the Skill-Based Matching (ERWM-RS-001-02) engine to suggest the best staff
                                    for specific jobs.
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">4. Set Payroll Rules</h3>
                            <p class="text-slate-700 leading-relaxed">
                                Configure your salary settings, including Sunday premiums and holiday pay, to automate
                                monthly payroll.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- For Employees Section -->
                <section id="employees" class="mb-12 scroll-mt-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">3. For Employees (Cleaning Staff)</h2>

                    <p class="text-slate-700 leading-relaxed mb-6 font-semibold">
                        Goal: Manage your schedule and execute tasks efficiently.
                    </p>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">1. Verify Profile</h3>
                            <p class="text-slate-700 leading-relaxed">
                                Check your Account Details (EEWM-ACM-001-01) to ensure your contact information is correct.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">2. Check the Dashboard</h3>
                            <p class="text-slate-700 leading-relaxed">
                                Your home screen will show Task Assignments. You must manually "Approve" or "Decline" new
                                tasks.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">3. Attendance Setup</h3>
                            <p class="text-slate-700 leading-relaxed">
                                Ensure your mobile device's GPS is enabled. The system uses Geofencing to allow clock-ins
                                only when you are at the client's location.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">4. Visit the Training Hub</h3>
                            <p class="text-slate-700 leading-relaxed">
                                Before your first shift, check the Skill Training Hub for any assigned video tutorials or
                                cleaning checklists.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- For Clients Section -->
                <section id="clients" class="mb-12 scroll-mt-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">4. For Clients</h2>

                    <p class="text-slate-700 leading-relaxed mb-6 font-semibold">
                        Goal: Book your first cleaning service.
                    </p>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">1. Register Your Account</h3>
                            <p class="text-slate-700 leading-relaxed">
                                Provide your business or home details and primary contact information.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">2. Request a Quote</h3>
                            <p class="text-slate-700 leading-relaxed">
                                Use the Provisional Billing tool. Select your required service and time to see an instant
                                cost breakdown.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">3. Book a Service</h3>
                            <p class="text-slate-700 leading-relaxed">
                                Confirm the quote to send a request to the Admin. Once approved, it will appear on your
                                Service Schedule calendar.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">4. Track Progress</h3>
                            <p class="text-slate-700 leading-relaxed">
                                On the day of the service, check your dashboard to see real-time updates (e.g., "Cleaner En
                                Route," "Cleaning In Progress").
                            </p>
                        </div>
                    </div>
                </section>

                <!-- System Requirements Section -->
                <section id="requirements" class="mb-12 scroll-mt-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">5. System Requirements</h2>

                    <p class="text-slate-700 leading-relaxed mb-6">
                        To ensure all features (especially real-time notifications and tracking) work correctly:
                    </p>

                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-slate-700 leading-relaxed"><span class="font-semibold">Browser:</span> Use the
                                latest version of Google Chrome, Mozilla Firefox, or Safari.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-slate-700 leading-relaxed"><span class="font-semibold">Internet:</span> A
                                stable connection is required for real-time status syncing.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-slate-700 leading-relaxed"><span class="font-semibold">Permissions:</span>
                                Allow the browser/app to access your Location (for Employees) and Notifications (for all
                                users).</span>
                        </li>
                    </ul>
                </section>

                <!-- Footer Navigation -->
                <div class="border-t border-slate-200 pt-8 mt-12">
                    <div class="flex items-center justify-between">
                        <a href="#"
                            class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            Back to top
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Counter Animation
            const counterElements = document.querySelectorAll('.counter');

            function animateCount(element) {
                const target = parseInt(element.getAttribute('data-target'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        element.textContent = target;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(current);
                    }
                }, 16);
            }

            // Scroll Animation for Cards
            const featureCards = document.querySelectorAll('.feature-card');

            const cardObserverOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.2
            };

            const cardObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.remove('scroll-hidden');
                        entry.target.classList.add('scroll-visible');
                    }
                });
            }, cardObserverOptions);

            featureCards.forEach(card => {
                if (!card.classList.contains('scroll-hidden') && !card.classList.contains('scroll-visible')) {
                    card.classList.add('scroll-hidden');
                }
                cardObserver.observe(card);
            });

            // Hover Blur Effect
            featureCards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    featureCards.forEach(otherCard => {
                        if (otherCard !== card) {
                            otherCard.classList.add('blurred');
                        }
                    });
                });

                card.addEventListener('mouseleave', () => {
                    featureCards.forEach(otherCard => {
                        otherCard.classList.remove('blurred');
                    });
                });
            });

            // Counter Animation Trigger
            let counterAnimationTriggered = false;

            const counterObserverOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.5
            };

            const counterObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !counterAnimationTriggered) {
                        counterElements.forEach(animateCount);
                        counterAnimationTriggered = true;
                        observer.unobserve(entry.target);
                    }
                });
            }, counterObserverOptions);

            const statsContainer = document.getElementById('container-2');
            if (statsContainer) {
                counterObserver.observe(statsContainer);
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Smooth scroll behavior for navigation links
            const navLinks = document.querySelectorAll('aside nav a');

            navLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }

                    // Update active state
                    navLinks.forEach(l => {
                        l.classList.remove('text-blue-600', 'bg-blue-50');
                        l.classList.add('text-slate-600');
                    });
                    this.classList.remove('text-slate-600');
                    this.classList.add('text-blue-600', 'bg-blue-50');
                });
            });

            // Highlight current section on scroll
            const sections = document.querySelectorAll('section[id]');

            window.addEventListener('scroll', () => {
                let current = '';

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    if (window.pageYOffset >= sectionTop - 100) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('text-blue-600', 'bg-blue-50');
                    link.classList.add('text-slate-600');

                    if (link.getAttribute('href').substring(1) === current) {
                        link.classList.remove('text-slate-600');
                        link.classList.add('text-blue-600', 'bg-blue-50');
                    }
                });
            });
        });
    </script>
@endpush