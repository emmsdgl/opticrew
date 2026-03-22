<x-layouts.general-employer :title="'Course Progress Report'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col gap-2">
            <div class="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Analytics', 'url' => route('admin.analytics')],
                    ['label' => 'Reports', 'url' => route('admin.reports.index')],
                    ['label' => 'Course Progress'],
                ]" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Course Progress Report</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Track employee training video completion and progress</p>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Total Employees</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalEmployees }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Training Videos</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalVideos }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Total Completions</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $totalCompleted }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Overall Completion Rate</p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $overallCompletionRate }}%</p>
            </div>
        </div>

        <!-- Employee Progress Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Employee Progress Overview</h2>
            </div>

            @if($employeeProgress->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Overall Progress</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Completed</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">In Progress</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pending</th>
                            </tr>
                        </thead>
                        @foreach($employeeProgress as $ep)
                            <tbody x-data="{ open: false }" class="border-b border-gray-200 dark:border-gray-700">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer" @click="open = !open">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <i class="fa-solid fa-chevron-right text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-90': open }"></i>
                                            @if($ep['employee']->user && $ep['employee']->user->profile_photo)
                                                <img src="{{ asset('storage/' . $ep['employee']->user->profile_photo) }}" alt="{{ $ep['employee']->fullName }}" class="w-8 h-8 rounded-full object-cover">
                                            @else
                                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">{{ strtoupper(substr($ep['employee']->fullName, 0, 2)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $ep['employee']->fullName }}</div>
                                                @if($ep['employee']->user && $ep['employee']->user->email)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ep['employee']->user->email }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ $ep['overall_progress'] == 100 ? 'bg-green-500' : ($ep['overall_progress'] > 0 ? 'bg-purple-500' : 'bg-gray-400 dark:bg-gray-600') }}"
                                                     style="width: {{ $ep['overall_progress'] }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $ep['overall_progress'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">{{ $ep['completed'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">{{ $ep['in_progress'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">{{ $ep['pending'] }}</span>
                                    </td>
                                </tr>
                                <tr x-show="open" x-collapse>
                                    <td colspan="5" class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($ep['courses'] as $course)
                                                <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $course['title'] }}</p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                                <div class="h-1.5 rounded-full
                                                                    @if($course['status'] === 'completed') bg-green-500
                                                                    @elseif($course['status'] === 'in_progress') bg-blue-500
                                                                    @else bg-gray-400 dark:bg-gray-600 @endif"
                                                                    style="width: {{ $course['progress'] }}%"></div>
                                                            </div>
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $course['progress'] }}%</span>
                                                        </div>
                                                    </div>
                                                    @if($course['status'] === 'completed')
                                                        <i class="fa-solid fa-circle-check text-green-500 text-sm"></i>
                                                    @elseif($course['status'] === 'in_progress')
                                                        <i class="fa-solid fa-spinner text-blue-500 text-sm"></i>
                                                    @else
                                                        <i class="fa-regular fa-circle text-gray-400 text-sm"></i>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                </div>
            @else
                <div class="w-full px-6 py-24 text-center">
                    <i class="fa-solid fa-graduation-cap text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No employee progress data found</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.general-employer>
