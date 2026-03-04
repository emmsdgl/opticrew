<x-layouts.general-employer :title="'Courses'">

    <section class="w-full flex flex-col gap-6 p-4 md:p-6" x-data="courseManager()">

        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Training Courses</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Overview of all training videos for the employees</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Published Courses</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $publishedVideos->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Draft Courses</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $draftVideos->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Required</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $publishedVideos->where('required', true)->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Total Employees</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $totalEmployees }}</p>
            </div>
        </div>

        {{-- Course Lists --}}
        <div class="flex flex-col gap-6 w-full rounded-lg p-4">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">All Courses</h2>

            {{-- Published Courses Section --}}
            <div class="flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        Published Courses
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $publishedVideos->count() }})</span>
                    </h3>
                    <button @click="openModal()"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Add Course
                    </button>
                </div>

                <div class="max-h-96 overflow-y-auto border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
                    @if(count($publishedFormatted) > 0)
                        <x-employee-components.task-overview-list
                            :items="$publishedFormatted"
                            fixedHeight="24rem"
                            maxHeight="30rem"
                            emptyTitle="No published courses"
                            emptyMessage="Published courses will appear here." />
                    @else
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-book-open text-2xl mb-3 opacity-50"></i>
                            <p class="font-semibold text-sm">No published courses</p>
                            <p class="text-xs">Published courses will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Divider --}}
            <hr class="my-4 border-gray-300 dark:border-gray-700">

            {{-- Draft Courses Section --}}
            <div class="flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                        Drafts
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $draftVideos->count() }})</span>
                    </h3>
                </div>

                <div class="max-h-96 overflow-y-auto border border-dashed border-gray-400 dark:border-gray-700 rounded-lg">
                    @if(count($draftsFormatted) > 0)
                        <x-employee-components.task-overview-list
                            :items="$draftsFormatted"
                            fixedHeight="24rem"
                            maxHeight="30rem"
                            emptyTitle="No draft courses"
                            emptyMessage="Draft courses will appear here." />
                    @else
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-file-pen text-2xl mb-3 opacity-50"></i>
                            <p class="font-semibold text-sm">No draft courses</p>
                            <p class="text-xs">Draft courses will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Add Course Modal --}}
        <div x-show="showModal"
            @click="closeModal()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/80 p-4"
            style="display: none;">
            <div @click.stop
                class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700"
                x-show="showModal" x-transition>

                {{-- Modal Header --}}
                <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Add Course</h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-5">

                    {{-- Section Title --}}
                    <p class="text-base font-semibold text-gray-800 dark:text-gray-200 italic">What's the basic module information?</p>

                    {{-- Module Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Module Title</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">MT</span>
                            <input type="text" x-model="formData.title"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="Create a module title">
                        </div>
                    </div>

                    {{-- Module Category --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Module Category</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-sitemap text-sm"></i></span>
                            <select x-model="formData.category"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm appearance-none">
                                <option value="">Select a category</option>
                                <option value="cleaning_techniques">Cleaning Techniques</option>
                                <option value="body_safety">Body Safety</option>
                                <option value="hazard_prevention">Hazard Prevention</option>
                                <option value="chemical_safety">Chemical Safety</option>
                            </select>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-xs"></i></span>
                        </div>
                    </div>

                    {{-- Module Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Module Description</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400"><i class="fa-solid fa-info-circle text-sm"></i></span>
                            <textarea x-model="formData.description" rows="3"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="Create a short summary about the module"></textarea>
                        </div>
                    </div>

                    {{-- Difficulty Level --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty Level</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-sitemap text-sm"></i></span>
                            <select x-model="formData.difficulty"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm appearance-none">
                                <option value="">Select a level</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-xs"></i></span>
                        </div>
                    </div>

                    {{-- Estimated Duration --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estimated Duration</label>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 text-center">
                                <input type="number" x-model="formData.durationHours" min="0" max="99"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-center text-2xl font-bold"
                                    placeholder="00">
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Hours</span>
                            </div>
                            <span class="text-2xl font-bold text-gray-400 dark:text-gray-500 pb-5">:</span>
                            <div class="flex-1 text-center">
                                <input type="number" x-model="formData.durationMinutes" min="0" max="59"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-center text-2xl font-bold"
                                    placeholder="00">
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Minutes</span>
                            </div>
                            <span class="text-2xl font-bold text-gray-400 dark:text-gray-500 pb-5">:</span>
                            <div class="flex-1 text-center">
                                <input type="number" x-model="formData.durationSeconds" min="0" max="59"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-center text-2xl font-bold"
                                    placeholder="00">
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Seconds</span>
                            </div>
                        </div>
                    </div>

                    {{-- Module Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Module Status</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-signal text-sm"></i></span>
                            <select x-model="formData.status"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm appearance-none">
                                <option value="">Select a status</option>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-xs"></i></span>
                        </div>
                    </div>

                    {{-- Publish Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Publish Date</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-calendar text-sm"></i></span>
                            <input type="date" x-model="formData.publishDate"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                        </div>
                    </div>

                    {{-- Toggle Switches --}}
                    <div class="space-y-4 pt-2">
                        {{-- Required for all employees --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Required for all employees</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Requires the employees to complete this module upon onboarding</p>
                            </div>
                            <button type="button" @click="formData.required = !formData.required"
                                :class="formData.required ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out">
                                <span :class="formData.required ? 'translate-x-5' : 'translate-x-0'"
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                        </div>

                        {{-- Auto-restrict work if incomplete --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Auto-restrict work if incomplete</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Restricts task assignment to employees if this module is incomplete</p>
                            </div>
                            <button type="button" @click="formData.autoRestrict = !formData.autoRestrict"
                                :class="formData.autoRestrict ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out">
                                <span :class="formData.autoRestrict ? 'translate-x-5' : 'translate-x-0'"
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
                    <button @click="closeModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-medium">
                        Cancel
                    </button>
                    <button @click="saveCourse()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        <i class="fa-solid fa-save mr-2"></i>
                        <span>Create</span>
                    </button>
                </div>
            </div>
        </div>

    </section>

    @push('scripts')
    <script>
        function courseManager() {
            return {
                showModal: false,
                formData: {
                    title: '',
                    category: '',
                    description: '',
                    difficulty: '',
                    durationHours: '00',
                    durationMinutes: '00',
                    durationSeconds: '00',
                    status: '',
                    publishDate: '',
                    required: true,
                    autoRestrict: true,
                },

                openModal() {
                    this.resetForm();
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                },

                resetForm() {
                    this.formData = {
                        title: '',
                        category: '',
                        description: '',
                        difficulty: '',
                        durationHours: '00',
                        durationMinutes: '00',
                        durationSeconds: '00',
                        status: '',
                        publishDate: '',
                        required: true,
                        autoRestrict: true,
                    };
                },

                saveCourse() {
                    if (!this.formData.title.trim()) {
                        alert('Please enter a module title.');
                        return;
                    }
                    if (!this.formData.category) {
                        alert('Please select a category.');
                        return;
                    }

                    // TODO: wire up to backend POST route
                    console.log('Saving course:', this.formData);
                    this.closeModal();
                }
            }
        }
    </script>
    @endpush

</x-layouts.general-employer>
