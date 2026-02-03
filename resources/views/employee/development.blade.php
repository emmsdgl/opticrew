<x-layouts.general-employee :title="'Development'">
    <section class="flex flex-col lg:flex-row gap-0">

        {{-- Left Sidebar - Course List --}}
        <div id="courseSidebar"
            class="w-full lg:w-96 rounded-2xl overflow-y-auto transition-all duration-300">
            <div class="p-4 md:p-6">
                {{-- Header --}}
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Courses</h1>

                    {{-- Search Bar --}}
                    <div class="relative">
                        <input type="text" placeholder="Search courses..."
                            class="w-full px-4 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                        <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                </div>

                {{-- Course List --}}
                <div class="space-y-4 px-6">
                    {{-- Filter Tabs --}}
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-200 dark:border-gray-700">
                        <button
                            class="pb-2 px-1 text-sm font-medium text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400">All</button>
                        <button
                            class="pb-2 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Active</button>
                        <button
                            class="pb-2 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Completed</button>
                    </div>
                    
                    {{-- Course Card 1 - Active --}}
                    <div class="course-item cursor-pointer group" data-course-id="1" onclick="selectCourse(1)">
                        <div
                            class="flex gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-gray-700 transition-all border-2 border-transparent hover:border-blue-200 dark:hover:border-blue-800">
                            
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                    Learning strategy: how instead of what</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">This course
                                    discusses the main skills and principles of the human nervous system that underlie
                                    oral language...</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">4/5(66)</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        All
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Course Card 2 - Selected State --}}
                    <div class="course-item cursor-pointer group active" data-course-id="2" onclick="selectCourse(2)">
                        <div
                            class="flex gap-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 transition-all border-2 border-blue-500 dark:border-blue-500">
                           
                            <div class="flex-1 min-w-0">
                                <h1 class="font-semibold text-gray-900 dark:text-white mb-1">English for career
                                    development</h1>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">This course is
                                    designed for non-native English speakers who are interested in advancing their...
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1">

                                        <span class="text-xs text-gray-500 dark:text-gray-400">4/5(93)</span>
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

                    {{-- Course Card 3 --}}
                    <div class="course-item cursor-pointer group" data-course-id="3" onclick="selectCourse(3)">
                        <div
                            class="flex gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-gray-700 transition-all border-2 border-transparent hover:border-blue-200 dark:hover:border-blue-800">
                            
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                    First steps in Chinese</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">This is an
                                    elementary-level Chinese course offered by Peking University and covers basic oral
                                    language...</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">3/5(12)</span>
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

                    {{-- Course Card 4 --}}
                    <div class="course-item cursor-pointer group" data-course-id="4" onclick="selectCourse(4)">
                        <div
                            class="flex gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-gray-700 transition-all border-2 border-transparent hover:border-blue-200 dark:hover:border-blue-800">
                            
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                    English Teaching: managing the class</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">This course will
                                    introduce students to important aspects of classroom management (tips and tricks...
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">4/5(78)</span>
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
                        <span class="text-sm text-gray-600 dark:text-gray-400">1 Video Lecture • 2 hours</span>
                    </div>
                </div>

                {{-- Course Title --}}
                <h1 class="course-title text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                    English for career development
                </h1>

                {{-- Course Description --}}
                <div class="mb-8">
                    <p class="course-description text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        In this course, you will learn about the job search, application, and interview process in the
                        United States, while comparing and contrasting the same process in your home country. This
                        course will also give you the opportunity to explore your global career path, while building
                        your vocabulary and improving your language skills to achieve your professional goals.
                    </p>
                </div>

                {{-- Instructor Info --}}
                <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-500 flex items-center justify-center text-white font-semibold text-sm">
                            FM
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Fin-noys Management</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Fin-noys</p>
                        </div>
                    </div>
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
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const courses = {
                1: {
                    title: "Learning strategy: how instead of what",
                    description: "This course discusses the main skills and principles of the human nervous system that underlie oral language. You'll learn effective learning strategies to maximize your potential.",
                    rating: 4,
                    reviews: 66,
                    level: "All levels",
                    duration: "8 lectures • 1.5 hours"
                },
                2: {
                    title: "English for career development",
                    description: "In this course, you will learn about the job search, application, and interview process in the United States, while comparing and contrasting the same process in your home country. This course will also give you the opportunity to explore your global career path, while building your vocabulary and improving your language skills to achieve your professional goals.",
                    rating: 4,
                    reviews: 93,
                    level: "Intermediate",
                    duration: "12 lectures • 2 hours"
                },
                3: {
                    title: "First steps in Chinese",
                    description: "This is an elementary-level Chinese course offered by Peking University and covers basic oral language skills needed for daily communication. Learn fundamental Chinese characters and pronunciation.",
                    rating: 3,
                    reviews: 12,
                    level: "Beginner",
                    duration: "15 lectures • 3 hours"
                },
                4: {
                    title: "English Teaching: managing the class",
                    description: "This course will introduce students to important aspects of classroom management, including tips and tricks for creating an engaging learning environment, handling difficult situations, and maximizing student participation.",
                    rating: 4,
                    reviews: 78,
                    level: "Intermediate",
                    duration: "10 lectures • 2.5 hours"
                },
                5: {
                    title: "Pronunciation of American English",
                    description: "Learners will improve their pronunciation by practicing different syllables and sounds. This course focuses on the nuances of American English pronunciation and will help you speak more clearly and confidently.",
                    rating: 5,
                    reviews: 142,
                    level: "All levels",
                    duration: "20 lectures • 4 hours"
                }
            };

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
            }

            function toggleSidebar() {
                const sidebar = document.getElementById('courseSidebar');
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('fixed');
                sidebar.classList.toggle('inset-0');
                sidebar.classList.toggle('z-40');
            }

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