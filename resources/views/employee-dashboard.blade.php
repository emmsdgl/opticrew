<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiCrew - Employee Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-indigo-900 text-white flex-shrink-0">
            <div class="p-6">
                <h1 class="text-2xl font-bold">FIN-NOYS</h1>
            </div>
            <nav class="mt-6">
                <a href="#" class="flex items-center px-6 py-3 bg-indigo-800 text-white">
                    <i class="fas fa-home mr-3"></i>
                    My Dashboard
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-indigo-200 hover:bg-indigo-800 hover:text-white transition">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    My Schedule
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-indigo-200 hover:bg-indigo-800 hover:text-white transition">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="px-8 py-4">
                    <h2 class="text-2xl font-bold text-gray-800">Hello, Sarah Johnson</h2>
                    <p class="text-gray-600 mt-1">Here are your tasks for today</p>
                </div>
            </header>

            <div class="p-8">
                <!-- My Tasks for Today Section -->
                <section class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">My Tasks for Today</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Task Card 1 -->
                        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-500">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-red-600 bg-red-100 px-3 py-1 rounded-full">URGENT</span>
                                <span class="text-sm text-gray-500">9:00 AM - 11:00 AM</span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-2">Deep Cleaning</h4>
                            <p class="text-gray-600 mb-1">Kakslauttanen - Small Cabin #5</p>
                            <div class="mt-4 mb-4">
                                <p class="text-sm text-gray-600 mb-2">Team Members:</p>
                                <div class="flex items-center space-x-2">
                                    <div class="flex -space-x-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-xs font-semibold border-2 border-white" title="Michael Brown">MB</div>
                                        <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center text-white text-xs font-semibold border-2 border-white" title="Emma Davis">ED</div>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-2">Michael Brown, Emma Davis</p>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <button class="flex-1 bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-play mr-2"></i>Start Task
                                </button>
                                <button class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-check mr-2"></i>Complete
                                </button>
                            </div>
                        </div>

                        <!-- Task Card 2 -->
                        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-orange-500">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-orange-600 bg-orange-100 px-3 py-1 rounded-full">HIGH</span>
                                <span class="text-sm text-gray-500">11:30 AM - 1:00 PM</span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-2">Window Cleaning</h4>
                            <p class="text-gray-600 mb-1">Arctic Resort - Suite #3</p>
                            <div class="mt-4 mb-4">
                                <p class="text-sm text-gray-600 mb-2">Team Members:</p>
                                <div class="flex items-center space-x-2">
                                    <div class="flex -space-x-2">
                                        <div class="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center text-white text-xs font-semibold border-2 border-white" title="Alex Turner">AT</div>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-2">Alex Turner</p>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <button class="flex-1 bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-play mr-2"></i>Start Task
                                </button>
                                <button class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-check mr-2"></i>Complete
                                </button>
                            </div>
                        </div>

                        <!-- Task Card 3 -->
                        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-3 py-1 rounded-full">NORMAL</span>
                                <span class="text-sm text-gray-500">2:00 PM - 3:30 PM</span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-2">Standard Cleaning</h4>
                            <p class="text-gray-600 mb-1">Snow Village - Igloo #2</p>
                            <div class="mt-4 mb-4">
                                <p class="text-sm text-gray-600 mb-2">Team Members:</p>
                                <div class="flex items-center space-x-2">
                                    <div class="flex -space-x-2">
                                        <div class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center text-white text-xs font-semibold border-2 border-white" title="Lisa Chen">LC</div>
                                        <div class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs font-semibold border-2 border-white" title="David Kim">DK</div>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-2">Lisa Chen, David Kim</p>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <button class="flex-1 bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-play mr-2"></i>Start Task
                                </button>
                                <button class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-check mr-2"></i>Complete
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- My Schedule View Section -->
                <section class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">My Schedule - October 2025</h3>
                    
                    <!-- Calendar Header -->
                    <div class="flex items-center justify-between mb-4">
                        <button class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h4 class="text-lg font-semibold text-gray-700">October 2025</h4>
                        <button class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-2">
                        <!-- Day Headers -->
                        <div class="text-center font-semibold text-gray-600 text-sm py-2">Sun</div>
                        <div class="text-center font-semibold text-gray-600 text-sm py-2">Mon</div>
                        <div class="text-center font-semibold text-gray-600 text-sm py-2">Tue</div>
                        <div class="text-center font-semibold text-gray-600 text-sm py-2">Wed</div>
                        <div class="text-center font-semibold text-gray-600 text-sm py-2">Thu</div>
                        <div class="text-center font-semibold text-gray-600 text-sm py-2">Fri</div>
                        <div class="text-center font-semibold text-gray-600 text-sm py-2">Sat</div>

                        <!-- Empty cells for start of month -->
                        <div class="text-center py-3 text-gray-400"></div>
                        <div class="text-center py-3 text-gray-400"></div>
                        <div class="text-center py-3 text-gray-400"></div>

                        <!-- Calendar Days -->
                        <div class="text-center py-3 bg-gray-100 rounded">1</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">2</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">3</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">4</div>
                        <div class="text-center py-3 bg-gray-100 rounded">5</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">6</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">7</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">8</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">9</div>
                        <div class="text-center py-3 bg-gray-100 rounded">10</div>
                        <div class="text-center py-3 bg-gray-100 rounded">11</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">12</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">13</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">14</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">15</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">16</div>
                        <div class="text-center py-3 bg-gray-100 rounded">17</div>
                        <div class="text-center py-3 bg-gray-100 rounded">18</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">19</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">20</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">21</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">22</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">23</div>
                        <div class="text-center py-3 bg-gray-100 rounded">24</div>
                        <div class="text-center py-3 bg-gray-100 rounded">25</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">26</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">27</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">28</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">29</div>
                        <div class="text-center py-3 bg-green-100 rounded font-semibold text-green-700">30</div>
                        <div class="text-center py-3 bg-gray-100 rounded">31</div>
                    </div>

                    <!-- Legend -->
                    <div class="mt-4 flex items-center justify-center gap-6">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-green-100 rounded"></div>
                            <span class="text-sm text-gray-600">Scheduled Work Day</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-gray-100 rounded"></div>
                            <span class="text-sm text-gray-600">Day Off</span>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
