{{-- Job Posting Create/Edit Modal --}}
{{-- This component must be placed inside an Alpine x-data scope that provides:
     showModal, editingIndex, editingHasApplicants, formData, jobCategories,
     stateOptions, cityOptions, closeModal(), onStateChange(), saveAsDraft(),
     saveJob(), publishDraft(), getIconBgClass(), getIconTextClass(), getCategoryLabel(), updateTypeBadge(), populateDefaults()
--}}

<div x-show="showModal" x-cloak @click="closeModal()"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/80 p-4" style="display: none;">
    <div @click.stop
        class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700"
        x-show="showModal" x-transition>

        <!-- Modal Header -->
        <div
            class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-12 py-6 z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white"
                    x-text="editingIndex !== null ? 'Edit Job Posting' : 'Create Job Posting'"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mt-1"
                x-text="editingIndex !== null ? 'Edit the following job posting details below.' : 'Fill in the following job posting details below.'">
            </p>
        </div>


        <!-- Modal Body -->
        <div class="px-12 py-2 space-y-4">
            <!-- Locked notice when job has applicants -->
            <div x-show="editingHasApplicants"
                class="flex items-center gap-3 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700">
                <i class="fa-solid fa-lock text-amber-500 dark:text-amber-400"></i>
                <p class="text-xs text-amber-700 dark:text-amber-300">This job posting has applicants and cannot be
                    edited. You may only view the details.</p>
            </div>

            <div :class="editingHasApplicants && 'pointer-events-none opacity-60'" class="space-y-6">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Title
                        *</label>
                    <input type="text" x-model="formData.title" required :disabled="editingHasApplicants"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        placeholder="e.g., Deep Cleaning Specialist">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description
                        *</label>
                    <textarea x-model="formData.description" rows="3" required :disabled="editingHasApplicants"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        placeholder="Atleast 180 characters"></textarea>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- State -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State / Region
                            *</label>
                        <div class="relative" x-data="{ stateOpen: false, stateSearch: '' }">
                            <button type="button"
                                @click="stateOpen = !stateOpen; $nextTick(() => { if(stateOpen) $refs.stateSearchInput.focus(); })"
                                class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm bg-white dark:bg-gray-700">
                                <span x-text="formData.state || 'Select State'"
                                    :class="!formData.state && 'text-gray-400'"></span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                                    :class="stateOpen && 'rotate-180'"></i>
                            </button>
                            <div x-show="stateOpen" @click.away="stateOpen = false" x-transition
                                class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-hidden flex flex-col">
                                <div class="p-2 border-b border-gray-200 dark:border-gray-600">
                                    <input type="text" x-model="stateSearch" x-ref="stateSearchInput"
                                        placeholder="Search..."
                                        class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-800 dark:text-white">
                                </div>
                                <div class="overflow-y-auto max-h-48">
                                    <template
                                        x-for="s in stateOptions.filter(s => s.name.toLowerCase().includes(stateSearch.toLowerCase()))"
                                        :key="s.iso2">
                                        <button type="button"
                                            @click="formData.state = s.name; stateOpen = false; stateSearch = ''; onStateChange();"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                            :class="formData.state === s.name ? 'bg-blue-50 dark:bg-blue-900/20 font-medium' :
                                                'text-gray-900 dark:text-white'">
                                            <span x-text="s.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City *</label>
                        <div class="relative" x-data="{ cityOpen: false, citySearch: '' }">
                            <button type="button"
                                @click="if(cityOptions.length > 0) { cityOpen = !cityOpen; $nextTick(() => { if(cityOpen) $refs.citySearchInput.focus(); }); }"
                                class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm bg-white dark:bg-gray-700"
                                :class="cityOptions.length === 0 && 'opacity-50 cursor-not-allowed'">
                                <span x-text="formData.city || 'Select City'"
                                    :class="!formData.city && 'text-gray-400'"></span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                                    :class="cityOpen && 'rotate-180'"></i>
                            </button>
                            <div x-show="cityOpen" @click.away="cityOpen = false" x-transition
                                class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-hidden flex flex-col">
                                <div class="p-2 border-b border-gray-200 dark:border-gray-600">
                                    <input type="text" x-model="citySearch" x-ref="citySearchInput"
                                        placeholder="Search..."
                                        class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-600 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-800 dark:text-white">
                                </div>
                                <div class="overflow-y-auto max-h-48">
                                    <template
                                        x-for="c in cityOptions.filter(c => c.name.toLowerCase().includes(citySearch.toLowerCase()))"
                                        :key="c.name">
                                        <button type="button"
                                            @click="formData.city = c.name; cityOpen = false; citySearch = '';"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                            :class="formData.city === c.name ? 'bg-blue-50 dark:bg-blue-900/20 font-medium' :
                                                'text-gray-900 dark:text-white'">
                                            <span x-text="c.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Salary
                            *</label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-500 dark:text-gray-400">&euro;</span>
                            <input type="text" x-model="formData.salary" required
                                class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="e.g., 30 - 40/hr">
                        </div>
                    </div>

                    <!-- Job Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Type
                            *</label>
                        <div class="relative" x-data="{ typeOpen: false }">
                            <button type="button" @click="typeOpen = !typeOpen"
                                class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm bg-white dark:bg-gray-700">
                                <span
                                    x-text="{'full-time':'Full-time','part-time':'Part-time','remote':'Remote'}[formData.type] || 'Select Type'"></span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                                    :class="typeOpen && 'rotate-180'"></i>
                            </button>
                            <div x-show="typeOpen" @click.away="typeOpen = false" x-transition
                                class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg overflow-hidden">
                                <template
                                    x-for="opt in [{v:'full-time',l:'Full-time'},{v:'part-time',l:'Part-time'},{v:'remote',l:'Remote'}]"
                                    :key="opt.v">
                                    <button type="button"
                                        @click="formData.type = opt.v; updateTypeBadge(); typeOpen = false;"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                        :class="formData.type === opt.v ? 'bg-blue-50 dark:bg-blue-900/20 font-medium' :
                                            'text-gray-900 dark:text-white'">
                                        <span x-text="opt.l"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Job Category -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Category
                            *</label>
                        <div class="relative" x-data="{ categoryOpen: false }">
                            <button type="button" @click="categoryOpen = !categoryOpen"
                                class="w-full flex items-center justify-between px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm bg-white dark:bg-gray-700">
                                <span class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded flex items-center justify-center flex-shrink-0"
                                        :class="getIconBgClass(formData.iconColor)">
                                        <i class="fas text-xs"
                                            :class="[formData.icon, getIconTextClass(formData.iconColor)]"></i>
                                    </span>
                                    <span x-text="getCategoryLabel(formData.category)"></span>
                                </span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                                    :class="categoryOpen && 'rotate-180'"></i>
                            </button>
                            <div x-show="categoryOpen" @click.away="categoryOpen = false" x-transition
                                class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="cat in jobCategories" :key="cat.value">
                                    <button type="button"
                                        @click="formData.category = cat.value; formData.icon = cat.icon; formData.iconColor = cat.color; categoryOpen = false; populateDefaults(cat.value);"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-left"
                                        :class="formData.category === cat.value ? 'bg-blue-50 dark:bg-blue-900/20' : ''">
                                        <span
                                            class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                                            :class="getIconBgClass(cat.color)">
                                            <i class="fas text-xs"
                                                :class="[cat.icon, getIconTextClass(cat.color)]"></i>
                                        </span>
                                        <div class="flex flex-col">
                                            <span class="text-gray-900 dark:text-white font-medium"
                                                x-text="cat.label"></span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 leading-tight"
                                                x-text="cat.description"></span>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">

                    <!-- Required Skills -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Required
                            Skills</label>
                        <div class="flex flex-wrap gap-2 mb-3" x-show="formData.requiredSkills.length > 0">
                            <template x-for="(skill, idx) in formData.requiredSkills" :key="'skill-' + idx">
                                <span
                                    class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1.5 rounded-full border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium">
                                    <span x-text="skill"></span>
                                    <button type="button" @click="formData.requiredSkills.splice(idx, 1)"
                                        class="w-4 h-4 inline-flex items-center justify-center rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 text-blue-400 hover:text-blue-600 dark:hover:text-blue-200 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" x-ref="skillInput"
                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="Type a skill and press Enter..."
                                @keydown.enter.prevent="if($refs.skillInput.value.trim()) { formData.requiredSkills.push($refs.skillInput.value.trim()); $refs.skillInput.value = ''; }">
                            <button type="button"
                                @click="if($refs.skillInput.value.trim()) { formData.requiredSkills.push($refs.skillInput.value.trim()); $refs.skillInput.value = ''; }"
                                class="px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg font-medium">
                                <i class="fa-solid fa-plus mr-1"></i>Add
                            </button>
                        </div>
                    </div>

                    <!-- Required Documents -->
                    <div x-data="{ docInput: '' }">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Required
                            Documents</label>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Accepted format: <span
                                class="font-semibold text-amber-600 dark:text-amber-400">DOCX</span></p>
                        <div class="flex flex-wrap gap-2 mb-3" x-show="formData.requiredDocs.length > 0">
                            <template x-for="(doc, idx) in formData.requiredDocs" :key="'doc-' + idx">
                                <span
                                    class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1.5 rounded-full border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-xs font-medium">
                                    <i
                                        class="fa-solid fa-file-lines text-[10px] text-amber-400 dark:text-amber-500"></i>
                                    <span x-text="typeof doc === 'object' ? doc.name : doc"></span>
                                    <button type="button" @click="formData.requiredDocs.splice(idx, 1)"
                                        class="w-4 h-4 inline-flex items-center justify-center rounded-full hover:bg-amber-200 dark:hover:bg-amber-800 text-amber-400 hover:text-amber-600 dark:hover:text-amber-200 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" x-model="docInput"
                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="e.g., Resume, Cover Letter, Certificate..."
                                @keydown.enter.prevent="if(docInput.trim()) { formData.requiredDocs.push({name: docInput.trim(), fileType: 'docx'}); docInput = ''; }">
                            <button type="button"
                                @click="if(docInput.trim()) { formData.requiredDocs.push({name: docInput.trim(), fileType: 'docx'}); docInput = ''; }"
                                class="px-4 py-2 text-sm text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-900/20 rounded-lg font-medium">
                                <i class="fa-solid fa-plus mr-1"></i>Add
                            </button>
                        </div>
                    </div>

                    <!-- Benefits -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Benefits</label>
                        <div class="flex flex-wrap gap-2 mb-3" x-show="formData.benefits.length > 0">
                            <template x-for="(benefit, idx) in formData.benefits" :key="'benefit-' + idx">
                                <span
                                    class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1.5 rounded-full border border-emerald-200 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-medium">
                                    <span x-text="benefit"></span>
                                    <button type="button" @click="formData.benefits.splice(idx, 1)"
                                        class="w-4 h-4 inline-flex items-center justify-center rounded-full hover:bg-emerald-200 dark:hover:bg-emerald-800 text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-200 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" x-ref="benefitInput"
                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                                placeholder="e.g., Health Insurance, Paid Time Off..."
                                @keydown.enter.prevent="if($refs.benefitInput.value.trim()) { formData.benefits.push($refs.benefitInput.value.trim()); $refs.benefitInput.value = ''; }">
                            <button type="button"
                                @click="if($refs.benefitInput.value.trim()) { formData.benefits.push($refs.benefitInput.value.trim()); $refs.benefitInput.value = ''; }"
                                class="px-4 py-2 text-sm text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20 rounded-lg font-medium">
                                <i class="fa-solid fa-plus mr-1"></i>Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>{{-- end of pointer-events wrapper --}}
        </div>

        <!-- Modal Footer -->
        <div
            class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end gap-3">
            <template x-if="editingIndex === null">
                <button @click="saveAsDraft()"
                    class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-normal">
                    <i class="fa-solid fa-file-pen mr-2"></i>Save as Draft
                </button>
            </template>
            {{-- <template x-if="editingIndex !== null && !editingHasApplicants">
                <button @click="closeModal()"
                    class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-normal">
                    Cancel
                </button>
            </template> --}}
            <button x-show="!editingHasApplicants" @click="saveJob()"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"
                    stroke-linejoin="round" class="inline-block mr-2">
                    <path
                        d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7" />
                    <path d="M7 3v4a1 1 0 0 0 1 1h7" />
                </svg>
                <span x-text="editingIndex !== null ? 'Update' : 'Post'"></span>
            </button>
            {{-- Post button — only visible when editing a draft --}}
            <template x-if="editingIndex !== null && formData.status === 'draft' && !editingHasApplicants">
                <button @click="publishDraft()"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                    <i class="fa-solid fa-paper-plane mr-2"></i>Post
                </button>
            </template>
            <button x-show="editingHasApplicants" @click="closeModal()"
                class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-normal">
                Close
            </button>
        </div>
    </div>
</div>
