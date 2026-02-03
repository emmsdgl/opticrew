<x-layouts.general-employee :title="'Development'">
    <section class="flex flex-col lg:flex-row gap-0">

        {{-- Left Sidebar - Course List --}}
        <div id="courseSidebar"
            class="w-full lg:w-96 rounded-2xl transition-all duration-300 flex flex-col lg:h-screen">
            <div class="p-4 md:p-6 flex-shrink-0">
                {{-- Header --}}
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Courses</h1>

                    {{-- Search Bar --}}
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search courses..."
                            class="w-full px-4 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                        <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Filter Tabs --}}
                <div class="flex items-center gap-3 mb-6 border-b border-gray-200 dark:border-gray-700">
                    <button onclick="filterCourses('all')" data-filter="all"
                        class="filter-tab pb-2 px-1 text-sm font-medium text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400">
                        All
                    </button>
                    <button onclick="filterCourses('active')" data-filter="active"
                        class="filter-tab pb-2 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        Active
                    </button>
                    <button onclick="filterCourses('completed')" data-filter="completed"
                        class="filter-tab pb-2 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        Completed
                    </button>
                </div>
            </div>

            {{-- Course List - Scrollable --}}
            <div id="courseList" class="space-y-4 px-6 pb-6 flex-1 overflow-y-auto scrollbar-custom">
                {{-- Course Card 1 - Active --}}
                <div class="course-item cursor-pointer group" data-course-id="1" data-status="active"
                    onclick="selectCourse(1)">
                    <div
                        class="flex gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-gray-700 transition-all border-2 border-transparent hover:border-blue-200 dark:hover:border-blue-800">

                        <div class="flex-1 min-w-0">
                            <h3
                                class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                Deep Cleaning Fundamentals</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">Master the essential
                                techniques of deep cleaning for residential and commercial spaces. Learn proper
                                sanitization methods, equipment usage, and time-saving strategies for thorough cleaning.
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">4/5 (66)</span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Beginner
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Course Card 2 - Active & Selected --}}
                <div class="course-item cursor-pointer group active" data-course-id="2" data-status="active"
                    onclick="selectCourse(2)">
                    <div
                        class="flex gap-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 transition-all border-2 border-blue-500 dark:border-blue-500">

                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Professional Window Cleaning
                                Techniques</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">Learn advanced window
                                cleaning methods for both residential and high-rise buildings. This course covers safety
                                protocols, streak-free techniques, and proper use of squeegees and cleaning solutions.
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">4/5 (93)</span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Intermediate
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Course Card 3 - Completed --}}
                <div class="course-item cursor-pointer group" data-course-id="3" data-status="completed"
                    onclick="selectCourse(3)">
                    <div
                        class="flex gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-gray-700 transition-all border-2 border-transparent hover:border-blue-200 dark:hover:border-blue-800">

                        <div class="flex-1 min-w-0">
                            <h3
                                class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                Eco-Friendly Cleaning Solutions</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">Discover sustainable
                                and environmentally safe cleaning methods. Learn how to create effective green cleaning
                                solutions, reduce chemical usage, and implement eco-friendly practices in your cleaning
                                routine.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">5/5 (124)</span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    All levels
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Course Card 4 - Active --}}
                <div class="course-item cursor-pointer group" data-course-id="4" data-status="active"
                    onclick="selectCourse(4)">
                    <div
                        class="flex gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-gray-700 transition-all border-2 border-transparent hover:border-blue-200 dark:hover:border-blue-800">

                        <div class="flex-1 min-w-0">
                            <h3
                                class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                Industrial Floor Care & Maintenance</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">Master the art of
                                maintaining various floor types including hardwood, tile, carpet, and vinyl. Learn
                                buffing, stripping, waxing techniques, and proper maintenance schedules for commercial
                                spaces.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">4/5 (78)</span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Advanced
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Course Card 5 - Completed --}}
                <div class="course-item cursor-pointer group" data-course-id="5" data-status="completed"
                    onclick="selectCourse(5)">
                    <div
                        class="flex gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-gray-700 transition-all border-2 border-transparent hover:border-blue-200 dark:hover:border-blue-800">

                        <div class="flex-1 min-w-0">
                            <h3
                                class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                Sanitization & Disinfection Protocols</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">Learn
                                industry-standard sanitization practices for healthcare facilities, food service, and
                                high-traffic areas. Understand proper disinfectant usage, cross-contamination
                                prevention, and compliance with health regulations.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">5/5 (142)</span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Intermediate
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel - Course Detail --}}
        <div class="flex-1 rounded-2xl">
            <div class="max-w-5xl mx-auto p-4 md:p-6 lg:p-8">
                {{-- Video Player Section --}}
                <div class="mb-6">
                    <div class="relative rounded-2xl overflow-hidden bg-gray-900 shadow-2xl aspect-video">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800&h=450&fit=crop"
                            alt="Course instructor" class="w-full h-full object-cover">

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent">
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                {{-- Progress Bar --}}
                                <div class="mb-4">
                                    <div class="w-full h-1 bg-white/30 rounded-full overflow-hidden">
                                        <div class="h-full w-1/3 bg-white rounded-full"></div>
                                    </div>
                                </div>

                                {{-- Controls --}}
                                <div class="flex items-center justify-between text-white">
                                    <div class="flex items-center gap-4">
                                        <button class="hover:scale-110 transition-transform">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                            </svg>
                                        </button>
                                        <span class="text-sm font-medium">0:45 / 2:15</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button class="hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                            </svg>
                                        </button>
                                        <button class="hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pause Icon (shown when paused) --}}
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <div
                                class="w-16 h-16 bg-white/90 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Statistics --}}
                    <div class="flex items-center gap-3 mt-4">
                        <span class="text-sm text-gray-600 dark:text-gray-400">4/5 (93 employees completed this)</span>
                        <span class="text-sm text-gray-400 dark:text-gray-500">•</span>
                        <button class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Intermediate</button>
                        <span class="text-sm text-gray-600 dark:text-gray-400">12 Video Lectures • 2 hours</span>
                    </div>
                </div>

                {{-- Course Title --}}
                <h1 class="course-title text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                    Professional Window Cleaning Techniques
                </h1>
                
                <!-- Tags Section -->
                <div class="mb-6">
                    <div class="flex flex-wrap gap-2">
                        <!-- DYNAMIC STATUS TAG -->
                        <span id="courseStatusTag"
                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                            <i class="fas fa-play-circle text-blue-500 mr-1.5 text-xs"></i>
                            Active
                        </span>
                    </div>
                </div>
                
                {{-- Course Description --}}
                <div class="mb-3">
                    <p class="course-description text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        Learn advanced window cleaning methods for both residential and high-rise buildings. This course
                        covers safety protocols, streak-free techniques, and proper use of squeegees and cleaning
                        solutions. You'll gain the skills needed to clean windows efficiently and professionally in any
                        setting.
                    </p>
                </div>
            </div>
        </div>

        {{-- Mobile Toggle Button --}}
        <button id="toggleSidebar"
            class="lg:hidden fixed bottom-6 right-6 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center z-50 transition-all"
            onclick="toggleSidebar()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </section>

    @push('styles')
        <style>
            /* Custom scrollbar for course list */
            .scrollbar-custom {
                scrollbar-width: thin;
                scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
            }

            .scrollbar-custom::-webkit-scrollbar {
                width: 6px;
            }

            .scrollbar-custom::-webkit-scrollbar-track {
                background: transparent;
            }

            .scrollbar-custom::-webkit-scrollbar-thumb {
                background-color: rgba(156, 163, 175, 0.5);
                border-radius: 3px;
            }

            .scrollbar-custom::-webkit-scrollbar-thumb:hover {
                background-color: rgba(156, 163, 175, 0.7);
            }

            /* Dark mode scrollbar */
            .dark .scrollbar-custom {
                scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
            }

            .dark .scrollbar-custom::-webkit-scrollbar-thumb {
                background-color: rgba(75, 85, 99, 0.5);
            }

            .dark .scrollbar-custom::-webkit-scrollbar-thumb:hover {
                background-color: rgba(75, 85, 99, 0.7);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const courses = {
                1: {
                    title: "Deep Cleaning Fundamentals",
                    description: "Master the essential techniques of deep cleaning for residential and commercial spaces. Learn proper sanitization methods, equipment usage, and time-saving strategies for thorough cleaning. This comprehensive course covers everything from basic cleaning principles to advanced deep cleaning techniques.",
                    rating: 4,
                    reviews: 66,
                    level: "Beginner",
                    duration: "8 lectures • 1.5 hours",
                    status: "active"
                },
                2: {
                    title: "Professional Window Cleaning Techniques",
                    description: "Learn advanced window cleaning methods for both residential and high-rise buildings. This course covers safety protocols, streak-free techniques, and proper use of squeegees and cleaning solutions. You'll gain the skills needed to clean windows efficiently and professionally in any setting.",
                    rating: 4,
                    reviews: 93,
                    level: "Intermediate",
                    duration: "12 lectures • 2 hours",
                    status: "active"
                },
                3: {
                    title: "Eco-Friendly Cleaning Solutions",
                    description: "Discover sustainable and environmentally safe cleaning methods. Learn how to create effective green cleaning solutions, reduce chemical usage, and implement eco-friendly practices in your cleaning routine. This course emphasizes the importance of environmental responsibility in professional cleaning.",
                    rating: 5,
                    reviews: 124,
                    level: "All levels",
                    duration: "10 lectures • 1.5 hours",
                    status: "completed"
                },
                4: {
                    title: "Industrial Floor Care & Maintenance",
                    description: "Master the art of maintaining various floor types including hardwood, tile, carpet, and vinyl. Learn buffing, stripping, waxing techniques, and proper maintenance schedules for commercial spaces. This advanced course prepares you for professional floor care in any environment.",
                    rating: 4,
                    reviews: 78,
                    level: "Advanced",
                    duration: "15 lectures • 3 hours",
                    status: "active"
                },
                5: {
                    title: "Sanitization & Disinfection Protocols",
                    description: "Learn industry-standard sanitization practices for healthcare facilities, food service, and high-traffic areas. Understand proper disinfectant usage, cross-contamination prevention, and compliance with health regulations. This course is essential for anyone working in environments requiring strict hygiene standards.",
                    rating: 5,
                    reviews: 142,
                    level: "Intermediate",
                    duration: "14 lectures • 2.5 hours",
                    status: "completed"
                }
            };

            let currentFilter = 'all';

            // Check for course parameter in URL and auto-select on page load
            document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const courseId = urlParams.get('course');
                
                if (courseId && courses[courseId]) {
                    // Select the course from URL parameter
                    selectCourse(parseInt(courseId));
                    
                    // Scroll to the selected course card (optional)
                    const selectedCourseCard = document.querySelector(`[data-course-id="${courseId}"]`);
                    if (selectedCourseCard) {
                        selectedCourseCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }
            });

            function selectCourse(courseId) {
                // Remove active class from all courses
                document.querySelectorAll('.course-item').forEach(item => {
                    item.classList.remove('active');
                    const card = item.querySelector('div');
                    card.classList.remove('bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-500', 'dark:border-blue-500');
                    card.classList.add('bg-gray-50', 'dark:bg-gray-700/50', 'hover:bg-blue-50', 'dark:hover:bg-gray-700', 'border-transparent', 'hover:border-blue-200', 'dark:hover:border-blue-800');
                });

                // Add active class to selected course
                const selectedCourse = document.querySelector(`[data-course-id="${courseId}"]`);
                if (selectedCourse) {
                    selectedCourse.classList.add('active');
                    const card = selectedCourse.querySelector('div');
                    card.classList.remove('bg-gray-50', 'dark:bg-gray-700/50', 'hover:bg-blue-50', 'dark:hover:bg-gray-700', 'border-transparent', 'hover:border-blue-200', 'dark:hover:border-blue-800');
                    card.classList.add('bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-500', 'dark:border-blue-500');
                }

                // Update course details
                const course = courses[courseId];
                if (course) {
                    updateCourseDetails(course);
                }

                // Close sidebar on mobile after selection
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            }

            function updateCourseDetails(course) {
                // Update title
                document.querySelector('.course-title').textContent = course.title;

                // Update description
                document.querySelector('.course-description').textContent = course.description;

                // Update level button
                const levelButton = document.querySelector('.text-sm.text-blue-600');
                if (levelButton) {
                    levelButton.textContent = course.level;
                }

                // Update duration
                const durationSpan = document.querySelector('.text-2xl + .text-sm');
                if (durationSpan) {
                    durationSpan.textContent = course.duration;
                }

                // Update status tag
                const statusTag = document.getElementById('courseStatusTag');
                if (statusTag && course.status) {
                    if (course.status === 'active') {
                        statusTag.className = 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';
                        statusTag.innerHTML = '<i class="fas fa-play-circle text-blue-500 mr-1.5 text-xs"></i>Active';
                    } else if (course.status === 'completed') {
                        statusTag.className = 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
                        statusTag.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1.5 text-xs"></i>Completed';
                    }
                }
            }

            function filterCourses(status) {
                currentFilter = status;

                // Update tab styles
                document.querySelectorAll('.filter-tab').forEach(tab => {
                    tab.classList.remove('text-blue-600', 'dark:text-blue-400', 'border-b-2', 'border-blue-600', 'dark:border-blue-400');
                    tab.classList.add('text-gray-500', 'dark:text-gray-400');
                });

                const activeTab = document.querySelector(`[data-filter="${status}"]`);
                if (activeTab) {
                    activeTab.classList.remove('text-gray-500', 'dark:text-gray-400');
                    activeTab.classList.add('text-blue-600', 'dark:text-blue-400', 'border-b-2', 'border-blue-600', 'dark:border-blue-400');
                }

                // Filter courses
                const courseItems = document.querySelectorAll('.course-item');
                courseItems.forEach(item => {
                    const courseStatus = item.getAttribute('data-status');

                    if (status === 'all' || courseStatus === status) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            function toggleSidebar() {
                const sidebar = document.getElementById('courseSidebar');
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('fixed');
                sidebar.classList.toggle('inset-0');
                sidebar.classList.toggle('z-40');
                sidebar.classList.toggle('bg-white');
                sidebar.classList.toggle('dark:bg-gray-800');
            }

            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function (e) {
                const searchTerm = e.target.value.toLowerCase();
                const courseItems = document.querySelectorAll('.course-item');

                courseItems.forEach(item => {
                    const title = item.querySelector('h3, h1').textContent.toLowerCase();
                    const description = item.querySelector('p').textContent.toLowerCase();
                    const courseStatus = item.getAttribute('data-status');

                    const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
                    const matchesFilter = currentFilter === 'all' || courseStatus === currentFilter;

                    if (matchesSearch && matchesFilter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function (event) {
                const sidebar = document.getElementById('courseSidebar');
                const toggleButton = document.getElementById('toggleSidebar');

                if (window.innerWidth < 1024 &&
                    sidebar.classList.contains('fixed') &&
                    !sidebar.contains(event.target) &&
                    !toggleButton.contains(event.target)) {
                    toggleSidebar();
                }
            });
        </script>
    @endpush
</x-layouts.general-employee>   