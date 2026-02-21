<x-layouts.general-employee :title="'Development'">
    <section class="flex flex-col lg:flex-row gap-0">

        {{-- Left Sidebar - Course List --}}
        <div id="courseSidebar"
            class="w-full lg:w-96 rounded-2xl transition-all duration-300 flex flex-col lg:h-full">
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
                    <button onclick="filterCourses('pending')" data-filter="pending"
                        class="filter-tab pb-2 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        Pending
                    </button>
                    <button onclick="filterCourses('in_progress')" data-filter="in_progress"
                        class="filter-tab pb-2 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        In Progress
                    </button>
                    <button onclick="filterCourses('completed')" data-filter="completed"
                        class="filter-tab pb-2 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        Completed
                    </button>
                </div>
            </div>

            {{-- Course List - Scrollable --}}
            <div id="courseList" class="space-y-4 px-6 pb-6 flex-1 overflow-y-auto scrollbar-custom">
                {{-- Course Card 1 - Pending --}}
                <div class="course-item cursor-pointer group" data-course-id="1" data-status="pending"
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
                            <div class="flex items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">4/5 (66)</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Course Card 2 - Pending & Selected --}}
                <div class="course-item cursor-pointer group active" data-course-id="2" data-status="pending"
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
                            <div class="flex items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">4/5 (93)</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Course Card 3 - Pending --}}
                <div class="course-item cursor-pointer group" data-course-id="3" data-status="pending"
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
                            <div class="flex items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">5/5 (124)</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Course Card 4 - Pending --}}
                <div class="course-item cursor-pointer group" data-course-id="4" data-status="pending"
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
                            <div class="flex items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">4/5 (78)</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Course Card 5 - Pending --}}
                <div class="course-item cursor-pointer group" data-course-id="5" data-status="pending"
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
                            <div class="flex items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">5/5 (142)</span>
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
                    <div class="relative rounded-2xl overflow-hidden bg-gray-900 shadow-2xl aspect-video" id="videoContainer">
                        {{-- YouTube Video Player (will be replaced by API) --}}
                        <div id="courseVideo" class="w-full h-full"></div>

                        {{-- Fallback Placeholder (hidden by default, shown if video fails) --}}
                        <div id="videoFallback" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 text-white">
                            <div class="text-center p-8">
                                <i class="fa-solid fa-play-circle text-6xl text-blue-400 mb-4"></i>
                                <h3 id="fallbackTitle" class="text-xl font-bold mb-2">Professional Window Cleaning Techniques</h3>
                                <p class="text-gray-400 text-sm mb-4">Video content is being prepared</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-600/20 text-blue-400 text-xs">
                                    <i class="fa-solid fa-clock mr-1"></i>
                                    Coming Soon
                                </span>
                            </div>
                        </div>

                        {{-- Completion Overlay (shown when video is completed) --}}
                        <div id="completionOverlay" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-black/80 text-white z-10">
                            <div class="text-center p-8">
                                <i class="fa-solid fa-circle-check text-6xl text-green-400 mb-4"></i>
                                <h3 class="text-xl font-bold mb-2">Course Completed!</h3>
                                <p class="text-gray-300 text-sm mb-4">Great job! You've finished this lesson.</p>
                                <button onclick="replayVideo()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fa-solid fa-rotate-right mr-2"></i>Watch Again
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar Section --}}
                    <div class="mt-4 bg-gray-100 dark:bg-gray-800 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-play-circle text-blue-500"></i>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Watch Progress</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span id="progressTime" class="text-xs text-gray-500 dark:text-gray-400">0:00 / 0:00</span>
                                <span id="progressPercent" class="text-sm font-semibold text-blue-600 dark:text-blue-400">0%</span>
                            </div>
                        </div>
                        <div class="relative w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div id="progressBar" class="absolute left-0 top-0 h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-300" style="width: 0%"></div>
                            {{-- Watched segments indicator --}}
                            <div id="watchedSegments" class="absolute inset-0"></div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span id="progressStatus" class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-info-circle mr-1"></i>
                                Watch the video to track progress
                            </span>
                            <span id="completionBadge" class="hidden inline-flex items-center px-2 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium">
                                <i class="fa-solid fa-check mr-1"></i>
                                Completed
                            </span>
                        </div>
                    </div>

                    {{-- Statistics --}}
                    <div class="flex items-center gap-3 mt-4 flex-wrap">
                        <span id="courseRating" class="text-sm text-gray-600 dark:text-gray-400">4/5 (93 employees completed this)</span>
                        <span class="text-sm text-gray-400 dark:text-gray-500">•</span>
                        <span id="courseLevel" class="text-sm text-blue-600 dark:text-blue-400">Intermediate</span>
                        <span class="text-sm text-gray-400 dark:text-gray-500">•</span>
                        <span id="courseDuration" class="text-sm text-gray-600 dark:text-gray-400">12 lectures • 2 hours</span>
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
                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                            <i class="fas fa-clock text-orange-500 mr-1.5 text-xs"></i>
                            Pending
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
        {{-- YouTube IFrame API --}}
        <script src="https://www.youtube.com/iframe_api"></script>
        <script>
            const courses = {
                1: {
                    title: "Deep Cleaning Fundamentals",
                    description: "Master the essential techniques of deep cleaning for residential and commercial spaces. Learn proper sanitization methods, equipment usage, and time-saving strategies for thorough cleaning. This comprehensive course covers everything from basic cleaning principles to advanced deep cleaning techniques.",
                    rating: 4,
                    reviews: 66,
                    level: "Beginner",
                    duration: "8 lectures • 1.5 hours",
                    status: "pending",
                    videoId: "Rx2Bh9n6VKc"  // Deep cleaning tutorial
                },
                2: {
                    title: "Professional Window Cleaning Techniques",
                    description: "Learn advanced window cleaning methods for both residential and high-rise buildings. This course covers safety protocols, streak-free techniques, and proper use of squeegees and cleaning solutions. You'll gain the skills needed to clean windows efficiently and professionally in any setting.",
                    rating: 4,
                    reviews: 93,
                    level: "Intermediate",
                    duration: "12 lectures • 2 hours",
                    status: "pending",
                    videoId: "B5s4uYNIM24"  // Window cleaning techniques
                },
                3: {
                    title: "Eco-Friendly Cleaning Solutions",
                    description: "Discover sustainable and environmentally safe cleaning methods. Learn how to create effective green cleaning solutions, reduce chemical usage, and implement eco-friendly practices in your cleaning routine. This course emphasizes the importance of environmental responsibility in professional cleaning.",
                    rating: 5,
                    reviews: 124,
                    level: "All levels",
                    duration: "10 lectures • 1.5 hours",
                    status: "pending",
                    videoId: "rcBPZs9mOe4"  // Natural cleaning products
                },
                4: {
                    title: "Industrial Floor Care & Maintenance",
                    description: "Master the art of maintaining various floor types including hardwood, tile, carpet, and vinyl. Learn buffing, stripping, waxing techniques, and proper maintenance schedules for commercial spaces. This advanced course prepares you for professional floor care in any environment.",
                    rating: 4,
                    reviews: 78,
                    level: "Advanced",
                    duration: "15 lectures • 3 hours",
                    status: "pending",
                    videoId: "P6HGlnSI7jo"  // Floor care maintenance
                },
                5: {
                    title: "Sanitization & Disinfection Protocols",
                    description: "Learn industry-standard sanitization practices for healthcare facilities, food service, and high-traffic areas. Understand proper disinfectant usage, cross-contamination prevention, and compliance with health regulations. This course is essential for anyone working in environments requiring strict hygiene standards.",
                    rating: 5,
                    reviews: 142,
                    level: "Intermediate",
                    duration: "14 lectures • 2.5 hours",
                    status: "pending",
                    videoId: "MbEmFGIkoZU"  // Sanitization protocols
                }
            };

            // Load saved progress from server
            const savedProgress = @json($courseProgress ?? new \stdClass);
            const savedStatuses = @json($courseStatuses ?? new \stdClass);

            // Apply saved progress to courses
            Object.keys(courses).forEach(id => {
                if (savedStatuses[id]) {
                    courses[id].status = savedStatuses[id];
                }
                if (savedProgress[id] !== undefined) {
                    courses[id].savedProgress = parseInt(savedProgress[id]);
                }
            });

            let currentFilter = 'all';
            let player = null;
            let currentCourseId = 2; // Default course
            let progressInterval = null;
            let watchedSeconds = new Set(); // Track unique seconds watched
            let videoDuration = 0;
            let isCompleted = false;
            const COMPLETION_THRESHOLD = 90; // Must watch 90% to complete
            let saveTimeout = null;

            // Save progress to server
            function saveProgressToServer(courseId, progress, status) {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    fetch('{{ route("employee.development.save-progress") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            course_id: courseId,
                            progress: Math.min(100, Math.max(0, progress)),
                            status: status,
                        })
                    }).catch(e => console.error('Failed to save progress:', e));
                }, 1000);
            }

            // Save on page leave
            window.addEventListener('beforeunload', function() {
                if (currentCourseId && courses[currentCourseId]) {
                    const progress = calculateWatchedPercentage();
                    const status = courses[currentCourseId].status;
                    if (status !== 'pending') {
                        navigator.sendBeacon(
                            '{{ route("employee.development.save-progress") }}',
                            new Blob([JSON.stringify({
                                _token: document.querySelector('meta[name="csrf-token"]').content,
                                course_id: currentCourseId,
                                progress: Math.min(100, Math.max(0, progress)),
                                status: status,
                            })], { type: 'application/json' })
                        );
                    }
                }
            });

            // YouTube API ready callback
            function onYouTubeIframeAPIReady() {
                initializePlayer(courses[currentCourseId].videoId);
            }

            function initializePlayer(videoId) {
                // Stop any existing progress tracking first
                stopProgressTracking();

                // Destroy existing player if any
                if (player) {
                    player.destroy();
                    player = null;

                    // Recreate the div element since YouTube API removes it when destroying
                    const container = document.getElementById('videoContainer');
                    const existingVideo = document.getElementById('courseVideo');

                    // Remove old element if it exists (might be an iframe now)
                    if (existingVideo) {
                        existingVideo.remove();
                    }

                    // Create new div for the player
                    const newDiv = document.createElement('div');
                    newDiv.id = 'courseVideo';
                    newDiv.className = 'w-full h-full';
                    container.insertBefore(newDiv, container.firstChild);
                }

                // Reset all progress tracking state
                watchedSeconds = new Set();
                videoDuration = 0;
                isCompleted = false;

                // Reset all UI elements
                updateProgressUI(0, 0, 0);
                hideCompletionOverlay();
                updateProgressStatus('default');

                // Hide completion badge
                const completionBadge = document.getElementById('completionBadge');
                if (completionBadge) {
                    completionBadge.classList.add('hidden');
                }

                // Hide fallback if it was shown
                document.getElementById('videoFallback').classList.add('hidden');

                player = new YT.Player('courseVideo', {
                    videoId: videoId,
                    playerVars: {
                        'rel': 0,
                        'modestbranding': 1,
                        'enablejsapi': 1
                    },
                    events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange,
                        'onError': onPlayerError
                    }
                });
            }

            function onPlayerReady(event) {
                videoDuration = player.getDuration();
                updateProgressUI(0, videoDuration, 0);
            }

            function onPlayerStateChange(event) {
                if (event.data === YT.PlayerState.PLAYING) {
                    // Start tracking progress
                    startProgressTracking();
                    updateProgressStatus('watching');

                    // Update course status to "in_progress" if it was pending
                    if (courses[currentCourseId].status === 'pending') {
                        courses[currentCourseId].status = 'in_progress';
                        updateCourseStatusTag('in_progress');
                        saveProgressToServer(currentCourseId, calculateWatchedPercentage(), 'in_progress');

                        // Update sidebar course item
                        const courseItem = document.querySelector(`[data-course-id="${currentCourseId}"]`);
                        if (courseItem) {
                            courseItem.setAttribute('data-status', 'in_progress');
                        }
                    }
                } else if (event.data === YT.PlayerState.PAUSED) {
                    stopProgressTracking();
                    updateProgressStatus('paused');
                } else if (event.data === YT.PlayerState.ENDED) {
                    stopProgressTracking();
                    checkCompletion(true);
                }
            }

            function onPlayerError(event) {
                console.error('YouTube Player Error:', event.data);
                document.getElementById('videoFallback').classList.remove('hidden');
            }

            let saveCounter = 0;
            function startProgressTracking() {
                if (progressInterval) return;

                progressInterval = setInterval(() => {
                    if (player && player.getCurrentTime) {
                        const currentTime = Math.floor(player.getCurrentTime());
                        watchedSeconds.add(currentTime);

                        const watchedPercentage = calculateWatchedPercentage();
                        updateProgressUI(player.getCurrentTime(), videoDuration, watchedPercentage);

                        // Save to server every 10 seconds
                        saveCounter++;
                        if (saveCounter % 10 === 0) {
                            saveProgressToServer(currentCourseId, watchedPercentage, courses[currentCourseId].status);
                        }

                        // Check for completion
                        if (watchedPercentage >= COMPLETION_THRESHOLD && !isCompleted) {
                            checkCompletion(false);
                        }
                    }
                }, 1000);
            }

            function stopProgressTracking() {
                if (progressInterval) {
                    clearInterval(progressInterval);
                    progressInterval = null;

                    // Save current progress when tracking stops
                    if (currentCourseId && courses[currentCourseId] && courses[currentCourseId].status !== 'pending') {
                        saveProgressToServer(currentCourseId, calculateWatchedPercentage(), courses[currentCourseId].status);
                    }
                }
            }

            function calculateWatchedPercentage() {
                if (videoDuration <= 0) return 0;
                const uniqueSecondsWatched = watchedSeconds.size;
                return Math.min(100, Math.round((uniqueSecondsWatched / videoDuration) * 100));
            }

            function updateProgressUI(currentTime, duration, percentage) {
                // Update progress bar
                const progressBar = document.getElementById('progressBar');
                if (progressBar) {
                    progressBar.style.width = `${percentage}%`;
                }

                // Update time display
                const progressTime = document.getElementById('progressTime');
                if (progressTime) {
                    progressTime.textContent = `${formatTime(currentTime)} / ${formatTime(duration)}`;
                }

                // Update percentage
                const progressPercent = document.getElementById('progressPercent');
                if (progressPercent) {
                    progressPercent.textContent = `${percentage}%`;
                }
            }

            function updateProgressStatus(status) {
                const progressStatus = document.getElementById('progressStatus');
                if (!progressStatus) return;

                switch(status) {
                    case 'watching':
                        progressStatus.innerHTML = '<i class="fa-solid fa-circle text-green-500 mr-1 animate-pulse text-xs"></i> Watching...';
                        break;
                    case 'paused':
                        progressStatus.innerHTML = '<i class="fa-solid fa-pause text-yellow-500 mr-1"></i> Paused';
                        break;
                    case 'completed':
                        progressStatus.innerHTML = '<i class="fa-solid fa-check-circle text-green-500 mr-1"></i> Course completed!';
                        break;
                    default:
                        progressStatus.innerHTML = '<i class="fa-solid fa-info-circle mr-1"></i> Watch the video to track progress';
                }
            }

            function checkCompletion(videoEnded) {
                const percentage = calculateWatchedPercentage();

                if (percentage >= COMPLETION_THRESHOLD || videoEnded) {
                    isCompleted = true;

                    // Update UI
                    updateProgressStatus('completed');
                    document.getElementById('completionBadge').classList.remove('hidden');

                    // Show completion overlay
                    if (videoEnded) {
                        showCompletionOverlay();
                    }

                    // Update course status to completed
                    courses[currentCourseId].status = 'completed';
                    updateCourseStatusTag('completed');

                    // Update sidebar course item
                    const courseItem = document.querySelector(`[data-course-id="${currentCourseId}"]`);
                    if (courseItem) {
                        courseItem.setAttribute('data-status', 'completed');
                    }

                    // Save completion to database
                    saveProgressToServer(currentCourseId, 100, 'completed');
                }
            }

            function showCompletionOverlay() {
                const overlay = document.getElementById('completionOverlay');
                if (overlay) {
                    overlay.classList.remove('hidden');
                }
            }

            function hideCompletionOverlay() {
                const overlay = document.getElementById('completionOverlay');
                if (overlay) {
                    overlay.classList.add('hidden');
                }
            }

            function replayVideo() {
                hideCompletionOverlay();
                if (player) {
                    player.seekTo(0);
                    player.playVideo();
                }
            }

            function updateCourseStatusTag(status) {
                const statusTag = document.getElementById('courseStatusTag');
                if (!statusTag) return;

                if (status === 'completed') {
                    statusTag.className = 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
                    statusTag.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1.5 text-xs"></i>Completed';
                } else if (status === 'in_progress') {
                    statusTag.className = 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';
                    statusTag.innerHTML = '<i class="fas fa-play text-blue-500 mr-1.5 text-xs"></i>In Progress';
                } else {
                    // pending (not started)
                    statusTag.className = 'inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300';
                    statusTag.innerHTML = '<i class="fas fa-clock text-orange-500 mr-1.5 text-xs"></i>Pending';
                }
            }

            function formatTime(seconds) {
                if (!seconds || isNaN(seconds)) return '0:00';
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${mins}:${secs.toString().padStart(2, '0')}`;
            }

            // Check for course parameter in URL and auto-select on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Apply saved statuses to sidebar course items
                Object.keys(courses).forEach(id => {
                    const courseItem = document.querySelector(`[data-course-id="${id}"]`);
                    if (courseItem && courses[id].status !== 'pending') {
                        courseItem.setAttribute('data-status', courses[id].status);
                    }
                });

                const urlParams = new URLSearchParams(window.location.search);
                const courseId = urlParams.get('course');

                if (courseId && courses[courseId]) {
                    currentCourseId = parseInt(courseId);
                    selectCourse(currentCourseId);

                    const selectedCourseCard = document.querySelector(`[data-course-id="${courseId}"]`);
                    if (selectedCourseCard) {
                        selectedCourseCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }
            });

            function selectCourse(courseId) {
                currentCourseId = courseId;

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

                // Update fallback title for error state
                const fallbackTitle = document.getElementById('fallbackTitle');
                if (fallbackTitle) fallbackTitle.textContent = course.title;

                // Initialize new video player (this resets all progress tracking to 0)
                if (course.videoId) {
                    initializePlayer(course.videoId);
                }

                // Update UI based on course status and saved progress
                if (course.status === 'completed') {
                    isCompleted = true;
                    updateProgressUI(0, 0, 100);
                    updateProgressStatus('completed');
                    document.getElementById('completionBadge').classList.remove('hidden');
                } else if (course.status === 'in_progress' && course.savedProgress > 0) {
                    updateProgressUI(0, 0, course.savedProgress);
                    updateProgressStatus('paused');
                } else if (course.status === 'in_progress') {
                    updateProgressStatus('paused');
                }
                // If pending, the initializePlayer already reset everything to 0%

                // Update statistics
                const courseRating = document.getElementById('courseRating');
                if (courseRating) {
                    courseRating.textContent = `${course.rating}/5 (${course.reviews} employees completed this)`;
                }

                const courseLevel = document.getElementById('courseLevel');
                if (courseLevel) {
                    courseLevel.textContent = course.level;
                }

                const courseDuration = document.getElementById('courseDuration');
                if (courseDuration) {
                    courseDuration.textContent = course.duration;
                }

                // Update status tag
                updateCourseStatusTag(course.status);
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