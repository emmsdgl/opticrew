<x-layouts.general-employer :title="'Training Management'">
    <x-skeleton-page :preset="'training'">
    <section role="status" class="w-full flex flex-col lg:flex-col gap-4 p-4 md:p-6">

        <!-- Header -->
        <div class="flex flex-col gap-2 mb-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Training Management</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage training videos displayed on the employee
                development page</p>
        </div>

        <!-- Stats Cards -->
        <div class ="my-8">
        <x-employer-components.stats-cards :stats="[
            [
                'label' => 'Cleaning Techniques',
                'value' => $trainingVideos->where('category', 'cleaning_techniques')->count(),
                'icon' => 'fa-solid fa-spray-can-sparkles',
                'iconColor' => '#2563eb',
            ],
            [
                'label' => 'Body Safety',
                'value' => $trainingVideos->where('category', 'body_safety')->count(),
                'icon' => 'fa-solid fa-shield-halved',
                'iconColor' => '#22c55e',
            ],
            [
                'label' => 'Hazard Prevention',
                'value' => $trainingVideos->where('category', 'hazard_prevention')->count(),
                'icon' => 'fa-solid fa-triangle-exclamation',
                'iconColor' => '#f59e0b',
            ],
            [
                'label' => 'Chemical Safety',
                'value' => $trainingVideos->where('category', 'chemical_safety')->count(),
                'icon' => 'fa-solid fa-flask',
                'iconColor' => '#ef4444',
            ],
        ]" />
        </div>

        <!-- Training Videos Table Section -->
        <div class="flex flex-col gap-4 w-full" x-data="trainingVideosData()">

            <!-- Section header with Add button -->
            <div class="flex flex-row items-center justify-between">
                <x-labelwithvalue label="Training Videos" count="({{ $trainingVideos->count() }})" />
                <div class="flex flex-row items-center gap-4">

                    <!-- Category Sort Dropdown -->
                    <div class="flex items-center">
                        <div x-data="{ open: false }" class="relative inline-block">
                            <button @click="open = !open" type="button"
                                class="bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex justify-between items-center gap-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-blue-800 transition-all duration-300">
                                <span class="text-gray-700 dark:text-white text-xs font-normal">Sort by:</span>
                                <span class="text-gray-700 dark:text-white text-xs font-normal"
                                    x-text="filterCategory === 'all' ? 'All Categories' : categories[filterCategory]?.title || filterCategory"></span>
                                <svg class="w-2.5 h-2.5 ms-2 transition-transform duration-300 text-gray-600 dark:text-gray-400"
                                    :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 top-full mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-lg min-w-[10rem] dark:bg-gray-700 origin-top"
                                style="display: none;">
                                <ul class="py-2 text-xs text-gray-700 dark:text-white">
                                    <li>
                                        <button @click="filterCategory = 'all'; currentPage = 1; open = false" type="button"
                                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                            :class="{ 'bg-gray-100 dark:bg-gray-600': filterCategory === 'all' }">
                                            All Categories
                                        </button>
                                    </li>
                                    <template x-for="(cat, key) in categories" :key="key">
                                        <li>
                                            <button @click="filterCategory = key; currentPage = 1; open = false" type="button"
                                                class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                :class="{ 'bg-gray-100 dark:bg-gray-600': filterCategory === key }"
                                                x-text="cat.title">
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <button @click="openModal()"
                        class="px-4 py-2 text-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors text-sm font-medium">
                        <i class="fa-solid fa-plus mr-2"></i>Add Training Video
                    </button>
                </div>
            </div>


            <!-- Status Filter Tabs -->
            <div class="flex items-center gap-1 border-b border-gray-200 dark:border-gray-700">
                <button @click="filterStatus = 'all'; currentPage = 1"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                    :class="filterStatus === 'all' ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                    All
                    <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full"
                        :class="filterStatus === 'all' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
                        x-text="trainingVideos.length"></span>
                </button>
                <button @click="filterStatus = 'active'; currentPage = 1"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                    :class="filterStatus === 'active' ? 'border-green-600 text-green-600 dark:text-green-400 dark:border-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                    Active
                    <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full"
                        :class="filterStatus === 'active' ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
                        x-text="trainingVideos.filter(v => v.is_active).length"></span>
                </button>
                <button @click="filterStatus = 'draft'; currentPage = 1"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                    :class="filterStatus === 'draft' ? 'border-gray-600 text-gray-600 dark:text-gray-400 dark:border-gray-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                    Drafts
                    <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full"
                        :class="filterStatus === 'draft' ? 'bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
                        x-text="trainingVideos.filter(v => !v.is_active).length"></span>
                </button>
            </div>

            <!-- Bulk Actions Bar -->
            <div x-show="selectedIds.length > 0" x-transition
                class="flex flex-row justify-between items-center gap-3 px-4 py-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300"
                    x-text="selectedIds.length + ' selected'"></span>
                <div class="flex flex-row gap-3">
                    <button @click="confirmBulkDelete()"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        <i class="fa-solid fa-trash mr-1"></i>Delete Selected
                    </button>
                    <button @click="selectedIds = []"
                        class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Deselect All
                    </button>
                </div>
            </div>

            <!-- Data Table -->
            <div x-show="filteredVideos().length > 0"
                class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="w-10 px-3 py-4 text-center">
                                <input type="checkbox"
                                    :checked="paginatedVideos().length > 0 && paginatedVideos().every(v => selectedIds.includes(v.id))"
                                    @change="toggleSelectAll()"
                                    class="appearance-none w-4 h-4 rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 checked:bg-blue-600 checked:border-blue-600 checked:bg-[url('data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%2016%2016%22%20fill%3D%22white%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12.207%204.793a1%201%200%20010%201.414l-5%205a1%201%200%2001-1.414%200l-2-2a1%201%200%20011.414-1.414L6.5%209.086l4.293-4.293a1%201%200%20011.414%200z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-colors">
                            </th>
                            <th class="w-[30%] px-4 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Title</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Category</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Source</th>
                            <th class="w-20 px-4 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Duration</th>
                            <th class="w-24 px-4 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Required</th>
                            <th class="w-24 px-4 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Status</th>
                            <th class="w-24 px-4 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(video, index) in paginatedVideos()" :key="video.id">
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800/50"
                                :class="{ 'bg-blue-50/50 dark:bg-blue-900/10': selectedIds.includes(video.id) }">
                                <!-- Checkbox -->
                                <td class="w-10 px-3 py-4 text-center">
                                    <input type="checkbox"
                                        :value="video.id"
                                        :checked="selectedIds.includes(video.id)"
                                        @change="toggleSelect(video.id)"
                                        class="appearance-none w-4 h-4 rounded-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 checked:bg-blue-600 checked:border-blue-600 checked:bg-[url('data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%2016%2016%22%20fill%3D%22white%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12.207%204.793a1%201%200%20010%201.414l-5%205a1%201%200%2001-1.414%200l-2-2a1%201%200%20011.414-1.414L6.5%209.086l4.293-4.293a1%201%200%20011.414%200z%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer transition-colors">
                                </td>
                                <!-- Title with thumbnail -->
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <template x-if="video.platform === 'youtube' && video.video_id">
                                            <img :src="'https://img.youtube.com/vi/' + video.video_id + '/default.jpg'"
                                                class="w-12 h-9 rounded object-cover flex-shrink-0" alt="">
                                        </template>
                                        <template x-if="video.platform === 'upload'">
                                            <div class="w-12 h-9 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                                <i class="fa-solid fa-file-video text-gray-400 text-sm"></i>
                                            </div>
                                        </template>
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate"
                                                x-text="video.title"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate"
                                                x-text="video.description"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category badge -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="getCategoryBadgeClass(video.category)"
                                        x-text="getCategoryLabel(video.category)"></span>
                                </td>

                                <!-- Source -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <template x-if="video.platform === 'youtube'">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-brands fa-youtube text-red-500 text-xs"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-300 font-mono truncate max-w-[100px]" x-text="video.video_id"></span>
                                        </div>
                                    </template>
                                    <template x-if="video.platform === 'upload'">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-upload text-blue-500 text-xs"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-300">Uploaded</span>
                                        </div>
                                    </template>
                                </td>

                                <!-- Duration -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-gray-200"
                                        x-text="video.duration || '-'"></span>
                                </td>

                                <!-- Required -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="video.required ?
                                            'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400' :
                                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
                                        x-text="video.required ? 'Required' : 'Optional'"></span>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="video.is_active ?
                                            'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' :
                                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
                                        x-text="video.is_active ? 'Active' : 'Draft'"></span>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="editVideo(video)"
                                            class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                            title="Edit">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button @click="deleteVideo(video)"
                                            class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                            title="Delete">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="totalPages > 1" class="mt-3">
                <nav role="navigation" aria-label="Pagination" class="mx-auto flex w-full justify-center">
                    <ul class="flex flex-row items-center gap-1">
                        {{-- Previous --}}
                        <li>
                            <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1"
                                :class="currentPage <= 1 ? 'text-gray-400 dark:text-gray-600 cursor-not-allowed' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800'"
                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                <span>Previous</span>
                            </button>
                        </li>

                        {{-- Page Numbers --}}
                        <template x-for="page in pageNumbers" :key="'page-'+page">
                            <li>
                                <template x-if="page === '...'">
                                    <span class="flex h-9 w-9 items-center justify-center text-gray-400 dark:text-gray-500" aria-hidden="true">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                    </span>
                                </template>
                                <template x-if="page !== '...' && page === currentPage">
                                    <span aria-current="page"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-100 shadow-sm"
                                        x-text="page"></span>
                                </template>
                                <template x-if="page !== '...' && page !== currentPage">
                                    <button @click="goToPage(page)"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                        x-text="page"></button>
                                </template>
                            </li>
                        </template>

                        {{-- Next --}}
                        <li>
                            <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= totalPages"
                                :class="currentPage >= totalPages ? 'text-gray-400 dark:text-gray-600 cursor-not-allowed' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800'"
                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg transition-colors">
                                <span>Next</span>
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Empty State -->
            <template x-if="filteredVideos().length === 0">
                <div
                    class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                    <i class="fa-solid fa-video text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No training videos found
                    </p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Click "Add Training Video" to
                        create your first training video</p>
                </div>
            </template>

            <!-- Create/Edit Modal -->
            <div x-show="showModal" x-cloak @click="closeModal()"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/80 p-4"
                style="display: none;">
                <div @click.stop
                    class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700"
                    x-show="showModal" x-transition>

                    <!-- Modal Header -->
                    <div
                        class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white"
                            x-text="editingId !== null ? 'Edit Training Video' : 'Add Training Video'"></h3>
                        <button @click="closeModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title
                                *</label>
                            <input type="text" x-model="formData.title"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="e.g., Safe Chemical Handling">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description
                                *</label>
                            <textarea x-model="formData.description" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="Brief description of the training video (minimum 180 characters)"></textarea>
                            <p class="mt-1 text-xs" :class="formData.description.length < 180 ? 'text-red-500' : 'text-green-500'">
                                <span x-text="formData.description.length"></span>/180 characters
                                <span x-show="formData.description.length < 180">(<span x-text="180 - formData.description.length"></span> more needed)</span>
                            </p>
                        </div>

                        <!-- Video Source Toggle -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Video Source *</label>
                            <div class="flex rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600">
                                <button type="button" @click="formData.platform = 'youtube'"
                                    :class="formData.platform === 'youtube' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                    class="flex-1 px-4 py-2 text-sm font-medium transition-colors">
                                    <i class="fa-brands fa-youtube mr-1.5"></i>YouTube
                                </button>
                                <button type="button" @click="formData.platform = 'upload'"
                                    :class="formData.platform === 'upload' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                                    class="flex-1 px-4 py-2 text-sm font-medium transition-colors border-l border-gray-300 dark:border-gray-600">
                                    <i class="fa-solid fa-upload mr-1.5"></i>Upload
                                </button>
                            </div>
                        </div>

                        <!-- YouTube Video ID (shown when platform is youtube) -->
                        <div x-show="formData.platform === 'youtube'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">YouTube Video ID *</label>
                            <input type="text" x-model="formData.video_id" @input.debounce.500ms="parseAndFetchDuration()"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="e.g., dQw4w9WgXcQ or paste YouTube URL">
                            <div x-show="formData.video_id && formData.duration" class="mt-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-clock text-xs text-gray-400"></i>
                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="'Duration: ' + formData.duration"></span>
                            </div>
                            <div x-show="fetchingDuration" class="mt-1.5">
                                <span class="text-xs text-gray-400 dark:text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Fetching duration...</span>
                            </div>
                        </div>

                        <!-- File Upload (shown when platform is upload) -->
                        <div x-show="formData.platform === 'upload'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Video File *</label>
                            <div class="relative">
                                <input type="file" x-ref="videoFile" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo"
                                    @change="handleFileSelect($event)"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-900/30 dark:file:text-blue-400">
                            </div>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Accepted: MP4, WebM, MOV, AVI (max 500MB)</p>
                            <template x-if="formData.video_file_name">
                                <div class="mt-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-video text-xs text-gray-400"></i>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="formData.video_file_name"></span>
                                </div>
                            </template>
                            <template x-if="editingId && formData.video_path && !formData.video_file_name">
                                <div class="mt-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-video text-xs text-green-500"></i>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Current file uploaded</span>
                                </div>
                            </template>
                        </div>

                        <!-- Two-column fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category
                                    *</label>
                                <select x-model="formData.category"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                                    <template x-for="(cat, key) in categories" :key="key">
                                        <option :value="key" x-text="cat.title"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Sort Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort
                                    Order</label>
                                <input type="number" x-model="formData.sort_order" min="0"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                    placeholder="0">
                            </div>

                            <!-- Required -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Required</label>
                                <select x-model="formData.required"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                                    <option :value="true">Yes - Required</option>
                                    <option :value="false">No - Optional</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select x-model="formData.is_active"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                                    <option :value="true">Active</option>
                                    <option :value="false">Draft</option>
                                </select>
                            </div>
                        </div>

                        <!-- YouTube Preview -->
                        <div x-show="formData.video_id">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preview</label>
                            <img :src="'https://img.youtube.com/vi/' + formData.video_id + '/hqdefault.jpg'"
                                class="w-full max-w-sm rounded-lg border border-gray-200 dark:border-gray-600"
                                alt="Video thumbnail preview">
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
                        <button @click="closeModal()"
                            class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-medium">
                            Cancel
                        </button>
                        <template x-if="editingId === null">
                            <button @click="formData.is_active = false; saveVideo()" :disabled="isSubmitting"
                                class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm font-medium disabled:opacity-50">
                                <i class="fa-solid fa-file-pen mr-2"></i>Save as Draft
                            </button>
                        </template>
                        <button @click="formData.is_active = (editingId !== null ? formData.is_active : true); saveVideo()" :disabled="isSubmitting"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium disabled:opacity-50">
                            <i class="fa-solid fa-save mr-2"></i>
                            <span x-text="editingId !== null ? 'Update' : 'Create'"></span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Success Dialog -->
            <x-employer-components.success-dialog title="Success" message="" buttonText="Back to Training" />

        </div>

    </section>
    </x-skeleton-page>

    @push('scripts')
        <script>
            function trainingVideosData() {
                return {
                    showModal: false,
                    showSuccess: false,
                    successTitle: '',
                    successMessage: '',
                    successButtonText: 'Back to Training',
                    editingId: null,
                    isSubmitting: false,
                    filterCategory: 'all',
                    filterStatus: 'all',
                    selectedIds: [],
                    currentPage: 1,
                    perPage: 5,
                    categories: @json($categories),
                    fetchingDuration: false,
                    trainingVideos: @json($trainingVideos).map(v => ({
                        id: v.id,
                        title: v.title,
                        description: v.description,
                        video_id: v.video_id,
                        video_path: v.video_path,
                        platform: v.platform || 'youtube',
                        category: v.category,
                        duration: v.duration,
                        required: !!v.required,
                        sort_order: v.sort_order || 0,
                        is_active: !!v.is_active
                    })),
                    formData: {
                        title: '',
                        description: '',
                        video_id: '',
                        video_path: '',
                        video_file: null,
                        video_file_name: '',
                        platform: 'youtube',
                        category: 'cleaning_techniques',
                        duration: '',
                        required: false,
                        sort_order: 0,
                        is_active: true
                    },

                    filteredVideos() {
                        let videos = this.trainingVideos;
                        if (this.filterCategory !== 'all') {
                            videos = videos.filter(v => v.category === this.filterCategory);
                        }
                        if (this.filterStatus === 'active') {
                            videos = videos.filter(v => v.is_active);
                        } else if (this.filterStatus === 'draft') {
                            videos = videos.filter(v => !v.is_active);
                        }
                        return videos;
                    },

                    paginatedVideos() {
                        const filtered = this.filteredVideos();
                        const start = (this.currentPage - 1) * this.perPage;
                        return filtered.slice(start, start + this.perPage);
                    },

                    get totalPages() {
                        return Math.ceil(this.filteredVideos().length / this.perPage);
                    },

                    get pageNumbers() {
                        const pages = [];
                        const total = this.totalPages;
                        const current = this.currentPage;

                        pages.push(1);

                        const rangeStart = Math.max(2, current - 1);
                        const rangeEnd = Math.min(total - 1, current + 1);

                        if (rangeStart > 2) pages.push('...');
                        for (let i = rangeStart; i <= rangeEnd; i++) pages.push(i);
                        if (rangeEnd < total - 1) pages.push('...');

                        if (total > 1) pages.push(total);
                        return pages;
                    },

                    goToPage(p) {
                        if (p >= 1 && p <= this.totalPages) this.currentPage = p;
                    },

                    toggleSelect(id) {
                        const idx = this.selectedIds.indexOf(id);
                        if (idx >= 0) {
                            this.selectedIds.splice(idx, 1);
                        } else {
                            this.selectedIds.push(id);
                        }
                    },

                    toggleSelectAll() {
                        const pageIds = this.paginatedVideos().map(v => v.id);
                        const allSelected = pageIds.every(id => this.selectedIds.includes(id));
                        if (allSelected) {
                            this.selectedIds = this.selectedIds.filter(id => !pageIds.includes(id));
                        } else {
                            pageIds.forEach(id => { if (!this.selectedIds.includes(id)) this.selectedIds.push(id); });
                        }
                    },

                    async confirmBulkDelete() {
                        const count = this.selectedIds.length;

                        try {
                            await window.showConfirmDialog(
                                'Delete Training Videos',
                                `Are you sure you want to delete ${count} training video(s)? This action cannot be undone.`,
                                'Delete All',
                                'Cancel'
                            );
                        } catch (e) {
                            return;
                        }

                        try {
                            const results = await Promise.all(
                                this.selectedIds.map(id =>
                                    fetch(`/admin/training-videos/${id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        }
                                    }).then(r => r.json())
                                )
                            );

                            const deletedIds = [...this.selectedIds];
                            this.trainingVideos = this.trainingVideos.filter(v => !deletedIds.includes(v.id));
                            this.selectedIds = [];
                            if (this.currentPage > this.totalPages) this.currentPage = Math.max(1, this.totalPages);
                            setTimeout(() => window.showSuccessDialog(
                                'Videos Deleted',
                                `${deletedIds.length} training video(s) have been removed successfully.`
                            ), 350);
                        } catch (error) {
                            window.showErrorDialog('Delete Failed', 'An error occurred while deleting the selected videos.');
                        }
                    },

                    openModal() {
                        this.showModal = true;
                        document.body.style.overflow = 'hidden';
                    },

                    closeModal() {
                        this.showModal = false;
                        this.editingId = null;
                        this.resetForm();
                        document.body.style.overflow = 'auto';
                    },

                    resetForm() {
                        this.formData = {
                            title: '',
                            description: '',
                            video_id: '',
                            video_path: '',
                            video_file: null,
                            video_file_name: '',
                            platform: 'youtube',
                            category: 'cleaning_techniques',
                            duration: '',
                            required: false,
                            sort_order: 0,
                            is_active: true
                        };
                        this.fetchingDuration = false;
                        if (this.$refs.videoFile) this.$refs.videoFile.value = '';
                    },

                    editVideo(video) {
                        this.editingId = video.id;
                        this.formData = {
                            title: video.title,
                            description: video.description,
                            video_id: video.video_id || '',
                            video_path: video.video_path || '',
                            video_file: null,
                            video_file_name: '',
                            platform: video.platform || 'youtube',
                            category: video.category,
                            duration: video.duration,
                            required: video.required,
                            sort_order: video.sort_order,
                            is_active: video.is_active
                        };
                        this.openModal();
                    },

                    handleFileSelect(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.formData.video_file = file;
                            this.formData.video_file_name = file.name;
                        }
                    },

                    parseAndFetchDuration() {
                        const input = this.formData.video_id?.trim();
                        if (input) {
                            const match = input.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/);
                            if (match) {
                                this.formData.video_id = match[1];
                            }
                        }
                        this.fetchDuration();
                    },

                    async fetchDuration() {
                        const videoId = this.formData.video_id?.trim();
                        if (!videoId) {
                            this.formData.duration = '';
                            return;
                        }

                        this.fetchingDuration = true;
                        try {
                            const response = await fetch(
                                `https://noembed.com/embed?url=https://www.youtube.com/watch?v=${videoId}`);
                            const data = await response.json();
                            if (data.error) {
                                this.formData.duration = '';
                                return;
                            }

                            // noembed doesn't return duration, so use YouTube iframe API
                            // Create a hidden player to get duration
                            const tempDiv = document.createElement('div');
                            tempDiv.id = 'yt-temp-player';
                            tempDiv.style.display = 'none';
                            document.body.appendChild(tempDiv);

                            // Clean up any existing temp player
                            if (window.ytTempPlayer) {
                                window.ytTempPlayer.destroy();
                            }

                            await new Promise((resolve) => {
                                const loadAPI = () => {
                                    window.ytTempPlayer = new YT.Player('yt-temp-player', {
                                        videoId: videoId,
                                        events: {
                                            'onReady': (event) => {
                                                const seconds = event.target.getDuration();
                                                if (seconds > 0) {
                                                    const mins = Math.floor(seconds / 60);
                                                    const secs = Math.floor(seconds % 60);
                                                    this.formData.duration =
                                                        `${mins}:${secs.toString().padStart(2, '0')}`;
                                                } else {
                                                    this.formData.duration = '';
                                                }
                                                event.target.destroy();
                                                tempDiv.remove();
                                                resolve();
                                            }
                                        }
                                    });
                                };

                                if (window.YT && window.YT.Player) {
                                    loadAPI();
                                } else {
                                    // Load YouTube IFrame API
                                    const tag = document.createElement('script');
                                    tag.src = 'https://www.youtube.com/iframe_api';
                                    document.head.appendChild(tag);
                                    window.onYouTubeIframeAPIReady = () => loadAPI();
                                }
                            });
                        } catch (error) {
                            console.error('Error fetching duration:', error);
                            this.formData.duration = '';
                        } finally {
                            this.fetchingDuration = false;
                        }
                    },

                    async deleteVideo(video) {
                        try {
                            await window.showConfirmDialog(
                                'Delete Training Video',
                                `Are you sure you want to delete "${video.title}"? This action cannot be undone.`,
                                'Delete',
                                'Cancel'
                            );
                        } catch (e) {
                            return;
                        }

                        try {
                            const response = await fetch(`/admin/training-videos/${video.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await response.json();
                            if (data.success) {
                                this.trainingVideos = this.trainingVideos.filter(v => v.id !== video.id);
                                if (this.currentPage > this.totalPages) this.currentPage = Math.max(1, this.totalPages);
                                setTimeout(() => window.showSuccessDialog(
                                    'Video Deleted',
                                    `The training video "${video.title}" has been removed and is no longer visible to employees.`
                                ), 350);
                            } else {
                                window.showErrorDialog('Delete Failed', data.message || 'Failed to delete training video.');
                            }
                        } catch (error) {
                            window.showErrorDialog('Delete Failed', 'An error occurred while deleting the training video.');
                        }
                    },

                    getUniqueVideoTitle(baseName, excludeId = null) {
                        const existing = this.trainingVideos
                            .filter(v => excludeId ? v.id !== excludeId : true)
                            .map(v => v.title.toLowerCase());

                        if (!existing.includes(baseName.toLowerCase())) {
                            return baseName;
                        }

                        let counter = 1;
                        let candidate;
                        do {
                            candidate = `${baseName} (${counter})`;
                            counter++;
                        } while (existing.includes(candidate.toLowerCase()));

                        return candidate;
                    },

                    async saveVideo() {
                        const isDraft = this.formData.is_active === false || this.formData.is_active === 'false';

                        if (!this.formData.title) {
                            window.showErrorDialog('Validation Error', 'Please enter a title.');
                            return;
                        }
                        if (!isDraft) {
                            if (!this.formData.description || !this.formData.category) {
                                window.showErrorDialog('Validation Error', 'Please fill in all required fields.');
                                return;
                            }
                            if (this.formData.description.length < 180) {
                                window.showErrorDialog('Validation Error', 'Description must be at least 180 characters.');
                                return;
                            }
                            if (this.formData.platform === 'youtube' && !this.formData.video_id) {
                                window.showErrorDialog('Validation Error', 'Please enter a YouTube Video ID.');
                                return;
                            }
                        }
                        if (!isDraft && this.formData.platform === 'upload' && !this.formData.video_file && !this.formData.video_path) {
                            window.showErrorDialog('Validation Error', 'Please select a video file to upload.');
                            return;
                        }

                        const wasEditing = this.editingId !== null;
                        const originalTitle = this.formData.title.trim();
                        const finalTitle = wasEditing
                            ? this.getUniqueVideoTitle(originalTitle, this.editingId)
                            : this.getUniqueVideoTitle(originalTitle);
                        const wasRenamed = finalTitle !== originalTitle;

                        let confirmMessage;
                        if (wasEditing) {
                            confirmMessage = wasRenamed
                                ? `A training video named "${originalTitle}" already exists. It will be saved as "${finalTitle}" instead.\n\nDo you want to proceed?`
                                : `Are you sure you want to update "${finalTitle}"?`;
                        } else {
                            confirmMessage = wasRenamed
                                ? `A training video named "${originalTitle}" already exists. It will be created as "${finalTitle}" instead.\n\nDo you want to proceed?`
                                : `Are you sure you want to create the training video "${finalTitle}"?`;
                        }

                        try {
                            await window.showConfirmDialog(
                                wasEditing ? 'Update Training Video' : 'Create Training Video',
                                confirmMessage,
                                wasEditing ? 'Update' : 'Create',
                                'Cancel'
                            );
                        } catch (e) {
                            return;
                        }

                        this.formData.title = finalTitle;
                        this.isSubmitting = true;

                        const formData = new FormData();
                        formData.append('title', this.formData.title);
                        formData.append('description', this.formData.description);
                        formData.append('platform', this.formData.platform);
                        formData.append('category', this.formData.category);
                        formData.append('duration', this.formData.duration || '');
                        formData.append('required', this.formData.required ? '1' : '0');
                        formData.append('sort_order', parseInt(this.formData.sort_order) || 0);
                        formData.append('is_active', (this.formData.is_active === false || this.formData.is_active === 'false') ? '0' : '1');

                        if (this.formData.platform === 'youtube') {
                            formData.append('video_id', this.formData.video_id);
                        }
                        if (this.formData.video_file) {
                            formData.append('video_file', this.formData.video_file);
                        }

                        // For PUT requests, Laravel needs _method spoofing with FormData
                        if (this.editingId !== null) {
                            formData.append('_method', 'PUT');
                        }

                        try {
                            const url = this.editingId !== null
                                ? `/admin/training-videos/${this.editingId}`
                                : '/admin/training-videos';

                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            const data = await response.json();
                            if (data.success) {
                                const savedVideo = {
                                    id: data.data.id,
                                    title: data.data.title,
                                    description: data.data.description,
                                    video_id: data.data.video_id,
                                    video_path: data.data.video_path,
                                    platform: data.data.platform,
                                    category: data.data.category,
                                    duration: data.data.duration,
                                    required: !!data.data.required,
                                    sort_order: data.data.sort_order || 0,
                                    is_active: !!data.data.is_active
                                };

                                const wasEditing = this.editingId !== null;
                                if (wasEditing) {
                                    const idx = this.trainingVideos.findIndex(v => v.id === this.editingId);
                                    if (idx !== -1) this.trainingVideos[idx] = savedVideo;
                                } else {
                                    this.trainingVideos.push(savedVideo);
                                }
                                this.closeModal();
                                setTimeout(() => window.showSuccessDialog(
                                    wasEditing ? 'Video Updated Successfully' : 'Video Created Successfully',
                                    wasEditing
                                        ? `The training video "${savedVideo.title}" has been updated and changes are now live.`
                                        : `The training video "${savedVideo.title}" has been added and is now available to employees.`
                                ), 350);
                            } else {
                                window.showErrorDialog('Save Failed', data.message || 'Failed to save training video.');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            window.showErrorDialog('Save Failed', 'An error occurred while saving the training video.');
                        } finally {
                            this.isSubmitting = false;
                        }
                    },

                    getCategoryBadgeClass(category) {
                        const classes = {
                            'cleaning_techniques': 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
                            'body_safety': 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
                            'hazard_prevention': 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
                            'chemical_safety': 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400'
                        };
                        return classes[category] || 'bg-gray-100 text-gray-700';
                    },

                    getCategoryLabel(category) {
                        return this.categories[category]?.title || category;
                    }
                };
            }
        </script>
    @endpush
</x-layouts.general-employer>
