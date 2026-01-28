<div x-data="{ open: false }" x-on:open-report.window="open = true; document.body.style.overflow = 'hidden'"
    x-on:keydown.escape.window="open = false; document.body.style.overflow = 'auto'">

    <!-- Modal Overlay -->
    <div x-show="open" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 px-8"
        @click.self="open = false; document.body.style.overflow = 'auto'">
        <!-- Modal Card -->
        <div x-show="open" x-transition
            class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-800">
            <!-- Modal Content -->
            <div class="p-6 sm:p-8">

                <!-- Header -->
                <div class="text-center mb-6">
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                        Service Report Form
                    </p>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-4">
                        Discuss Your Service<br>Concerns With Us
                    </h2>
                </div>

                <!-- Concern Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                        Type of Concern
                    </label>
                    <x-client-components.quotation-page.service-dropdown label=""
                        name="concern_type" :options="[
                        [
                            'title' => 'Late Arrival',
                            'description' => 'Service provider arrived later than scheduled',
                            'value' => 'late_arrival',
                        ],
                        [
                            'title' => 'Poor Service Quality',
                            'description' => 'Work did not meet expected standards',
                            'value' => 'poor_quality',
                            'placeholdericon' => ''
                        ],
                        [
                            'title' => 'Damaged Property',
                            'description' => 'Property was damaged during service',
                            'value' => 'damaged_property',
                            'placeholdericon' => ''
                        ],
                        [
                            'title' => 'Unprofessional Behavior',
                            'description' => 'Rude or inappropriate conduct',
                            'value' => 'unprofessional_behavior',
                            'placeholdericon' => ''
                        ],
                        [
                            'title' => 'Other',
                            'description' => 'Concern not listed above',
                            'value' => 'other',
                            'placeholdericon' => ''
                        ],
                    ]" />

                </div>

                <!-- Upload Box -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                        Add a Photo Report
                    </label>
                    <label
                        class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-6 text-center cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition">
                        <div class="mb-2 text-blue-600 dark:text-blue-400">
                            <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v9.75M3 16.5L7.5 12l4.5 4.5 4.5-6 4.5 6M3 16.5v.75A2.25 2.25 0 005.25 19.5h13.5A2.25 2.25 0 0021 17.25v-.75" />
                            </svg>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            Upload images or videos or
                            <span class="text-blue-600 dark:text-blue-400 font-semibold">browse</span>
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            JPG, PNG, JPEG · MAX 2MB
                        </p>
                        <input type="file" class="hidden" />
                    </label>
                </div>

                <!-- Detailed Report -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                        Detailed Report
                    </label>
                    <textarea rows="3" placeholder="Add a comment..."
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 focus:outline-none resize-none"></textarea>
                </div>

                <!-- Submit Button -->
                <button
                    class="w-full text-sm rounded-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-400 text-white font-semibold py-3 transition focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-400">
                    Submit Report
                </button>

            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>