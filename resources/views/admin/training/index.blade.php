<x-layouts.general-employer :title="'Training Management'">
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
                                        <button @click="filterCategory = 'all'; open = false" type="button"
                                            class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                            :class="{ 'bg-gray-100 dark:bg-gray-600': filterCategory === 'all' }">
                                            All Categories
                                        </button>
                                    </li>
                                    <template x-for="(cat, key) in categories" :key="key">
                                        <li>
                                            <button @click="filterCategory = key; open = false" type="button"
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


            <!-- Data Table -->
            <div x-show="filteredVideos().length > 0"
                class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Title</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Category</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Source</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Duration</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Required</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(video, index) in filteredVideos()" :key="video.id">
                            <tr class="even:bg-gray-50 dark:even:bg-gray-800/50">
                                <!-- Title with thumbnail -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <template x-if="video.platform === 'youtube' && video.video_id">
                                            <img :src="'https://img.youtube.com/vi/' + video.video_id + '/default.jpg'"
                                                class="w-12 h-9 rounded object-cover flex-shrink-0" alt="">
                                        </template>
                                        <template x-if="video.platform === 'upload'">
                                            <div class="w-12 h-9 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                                <i class="fa-solid fa-file-video text-gray-400 text-sm"></i>
                                            </div>
                                        </template>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white"
                                                x-text="video.title"></div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate"
                                                x-text="video.description"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="getCategoryBadgeClass(video.category)"
                                        x-text="getCategoryLabel(video.category)"></span>
                                </td>

                                <!-- Source -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <template x-if="video.platform === 'youtube'">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-brands fa-youtube text-red-500 text-xs"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-300 font-mono" x-text="video.video_id"></span>
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-gray-200"
                                        x-text="video.duration || '-'"></span>
                                </td>

                                <!-- Required -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="video.required ?
                                            'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400' :
                                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
                                        x-text="video.required ? 'Required' : 'Optional'"></span>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                        :class="video.is_active ?
                                            'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' :
                                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
                                        x-text="video.is_active ? 'Active' : 'Inactive'"></span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="editVideo(video)"
                                            class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button @click="deleteVideo(video)"
                                            class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
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
                            <input type="text" x-model="formData.video_id" @input.debounce.500ms="fetchDuration()"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="e.g., dQw4w9WgXcQ">
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
                                    <option :value="false">Inactive</option>
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
                        <button @click="saveVideo()" :disabled="isSubmitting"
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
                        if (this.filterCategory === 'all') return this.trainingVideos;
                        return this.trainingVideos.filter(v => v.category === this.filterCategory);
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
                        if (!confirm('Are you sure you want to delete "' + video.title + '"?')) return;

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
                                this.successTitle = 'Video Deleted Successfully';
                                this.successMessage =
                                    'The training video has been removed and is no longer visible to employees.';
                                this.showSuccess = true;
                            } else {
                                alert(data.message || 'Failed to delete training video.');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the training video.');
                        }
                    },

                    async saveVideo() {
                        if (!this.formData.title || !this.formData.description || !this.formData.category) {
                            alert('Please fill in all required fields.');
                            return;
                        }
                        if (this.formData.description.length < 180) {
                            alert('Description must be at least 180 characters.');
                            return;
                        }
                        if (this.formData.platform === 'youtube' && !this.formData.video_id) {
                            alert('Please enter a YouTube Video ID.');
                            return;
                        }
                        if (this.formData.platform === 'upload' && !this.formData.video_file && !this.formData.video_path) {
                            alert('Please select a video file to upload.');
                            return;
                        }
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
                                this.successTitle = wasEditing ? 'Video Updated Successfully' :
                                'Video Created Successfully';
                                this.successMessage = wasEditing ?
                                    'The training video has been updated and changes are now live.' :
                                    'The training video has been added and is now available to employees.';
                                this.showSuccess = true;
                            } else {
                                alert(data.message || 'Failed to save training video.');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('An error occurred while saving the training video.');
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
