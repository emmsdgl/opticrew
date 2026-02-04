<x-layouts.general-employer :title="'Activity History'">
    <div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" x-data="{ activeTab: 'all' }">

        {{-- Main Content Area --}}
        <div class="flex-1">
            {{-- Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Left Column - Activity List --}}
                <div class="lg:col-span-3 space-y-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="fa-regular fa-clock-rotate-left text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                            Activity History
                        </h1>
                    </div>

                    {{-- Tabs Navigation --}}
                    <div class="">
                        <nav class="flex space-x-8">
                            <button @click="activeTab = 'all'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'all' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                All
                                <span x-show="activeTab === 'all'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'services'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'services' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Services
                                <span x-show="activeTab === 'services'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'to_rate'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'to_rate' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Ratings
                                <span x-show="activeTab === 'to_rate'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>

                            <button @click="activeTab = 'reports'"
                                class="relative pb-4 text-sm font-medium transition-colors duration-200"
                                :class="activeTab === 'reports' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                                Reports
                                <span x-show="activeTab === 'reports'" x-transition
                                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 dark:bg-blue-400"></span>
                            </button>
                        </nav>
                    </div>

                    {{-- Sort/Filter Dropdown --}}
                    <div class="flex justify-between items-center">
                        {{-- Filter for Ratings Tab --}}
                        <select x-show="activeTab === 'to_rate'"
                            class="px-4 py-2 bg-transparent dark:bg-transparent rounded-lg text-sm text-gray-900 dark:text-gray-100">
                            <option>All</option>
                            <option>Clients</option>
                            <option>Employees</option>
                        </select>

                        {{-- Sort for Other Tabs --}}
                        <select x-show="activeTab !== 'to_rate'"
                            class="px-4 py-2 bg-transparent dark:bg-transparent rounded-lg text-sm text-gray-900 dark:text-gray-100">
                            <option>Most Recent</option>
                            <option>Oldest First</option>
                            <option>Price: High to Low</option>
                            <option>Price: Low to High</option>
                        </select>
                    </div>

                    {{-- Activity Cards Container --}}
                    <div class="space-y-3">

                        {{-- All Tab Content --}}
                        <div x-show="activeTab === 'all'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <x-client-components.history-page.activity-card icon="🧹"
                                title="Booked a Deep Cleaning Service" date="14 Dec 2025, 8:50 pm" price="$ 120"
                                status="Completed" statusColor="text-green-600 dark:text-green-400">
                                <x-slot:actions>
                                    <a href="#"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                        Review →
                                    </a>

                                    <a href="{{ route('client.appointment.create') }}"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                        Rebook →
                                    </a>
                                </x-slot:actions>
                                </x-activity-card>


                                {{-- Activity Card 2 --}}
                                <x-client-components.history-page.activity-card icon="🧹"
                                    title="Booked a Deep Cleaning Service" date="14 Dec 2025, 8:50 pm" price="$ 120"
                                    status="Completed" statusColor="text-green-600 dark:text-green-400">
                                    <x-slot:actions>
                                        <a href="#"
                                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                            Review →
                                        </a>

                                        <a href="{{ route('client.appointment.create') }}"
                                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                            Rebook →
                                        </a>
                                    </x-slot:actions>
                                    </x-activity-card>
                        </div>

                        {{-- Services Tab Content --}}
                        <div x-show="activeTab === 'services'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">

                            <x-client-components.history-page.activity-card icon="🧹"
                                title="Booked a Deep Cleaning Service" date="14 Dec 2025, 8:50 pm" price="$ 120"
                                status="Completed" statusColor="text-green-600 dark:text-green-400">
                                <x-slot:actions>
                                    <a href="#"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Review
                                        →</a>
                                    <a href="{{ route('client.appointment.create') }}"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Rebook
                                        →</a>
                                    <a href="#" @click.prevent="$dispatch('open-rate')"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Rate
                                        →</a>
                                    <x-client-components.history-page.feedback-form />

                                </x-slot:actions>
                                </x-activity-card>
                        </div>

                        {{-- To Rate Tab Content --}}
                        <div x-show="activeTab === 'to_rate'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0">
                            <x-client-components.history-page.activity-card icon="🧹"
                                title="Booked a Deep Cleaning Service" date="14 Dec 2025, 8:50 pm" price="$ 120"
                                status="Completed" statusColor="text-green-600 dark:text-green-400">
                                <x-slot:actions>
                                    <a href="#"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Review
                                        →</a>
                                    <a href="{{ route('client.appointment.create') }}"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Rebook
                                        →</a>
                                    <a href="#" @click.prevent="$dispatch('open-rate')"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Rate
                                        →</a>
                                    <x-client-components.history-page.feedback-form />

                                </x-slot:actions>
                                </x-activity-card>
                        </div>

                        {{-- Reports Tab Content --}}
                        <div x-show="activeTab === 'reports'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0">
                            <div class="bg-none dark:bg-none rounded-lg p-12 text-center">
                                <div class="text-gray-400 dark:text-gray-500 mb-3">
                                    <i class="fa-regular fa-file-lines text-4xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">No reports available</p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Right Column - Service Details Summary --}}
                <div class="lg:col-span-2 h-[calc(100vh-4rem)]">
                    <div class="bg-none dark:bg-none rounded-lg p-8 overflow-y-auto h-full scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-200 dark:scrollbar-track-gray-800">

                        {{-- Service Details Title --}}
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-6">Service Details Summary
                        </h3>
                        {{-- Approval Status Alert --}}
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">
                            The service quotation is
                            <span class="font-semibold text-orange-600 dark:text-orange-400">not yet
                                approved</span>.
                            Kindly check the status of your request to track your status
                        </p>
                        {{-- Details List --}}
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Appointment ID</span>
                                <span class="font-semibold text-gray-900 dark:text-white">123456789</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Service Date</span>
                                <span class="font-semibold text-gray-900 dark:text-white">2025-11-27</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Service Time</span>
                                <span class="font-semibold text-gray-900 dark:text-white">9:20 PM</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Service Type</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Final Cleaning</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Service Location</span>
                                <span class="font-semibold text-gray-900 dark:text-white">101 S from, Finland</span>
                            </div>
                        </div>

                        {{-- Assigned Members Section --}}
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <label
                                class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-3">
                                <i class="fas fa-users"></i>
                                Assigned Members
                            </label>
                            <div class="flex items-center gap-2 flex-wrap">
                                @php
                                    // CHANGE: SHOULD BE FROM THE DATABASE
                                    $members = [
                                        ['name' => 'John Doe', 'initial' => 'J'],
                                        ['name' => 'Jane Smith', 'initial' => 'J'],
                                        ['name' => 'Bob Johnson', 'initial' => 'B'],
                                        ['name' => 'Alice Cooper', 'initial' => 'A'],
                                    ];
                                @endphp
                                @foreach(array_slice($members, 0, 3) as $member)
                                    <div class="relative group">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold cursor-pointer transition-transform hover:scale-110">
                                            {{ $member['initial'] }}
                                        </div>
                                        {{-- Tooltip --}}
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-10">
                                            {{ $member['name'] }}
                                            <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                        </div>
                                    </div>
                                @endforeach
                                @if(count($members) > 3)
                                    <button
                                        class="w-8 h-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-full flex items-center justify-center text-gray-400 hover:border-gray-400 hover:text-gray-600 dark:hover:border-gray-500 dark:hover:text-gray-300 transition-colors text-xs">
                                        +{{ count($members) - 3 }}
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Task Checklist Section --}}
                        <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="mb-4">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-6">
                                    Tasks Checklist
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Track the progress of this task
                                </p>
                            </div>

                            {{-- Checklist Items --}}
                            <div class="space-y-2 mb-4">
                                @php
                                    // CHANGE: SHOULD BE FROM THE DATABASE
                                    $checklistItems = [
                                        'Remove clutter and movable items',
                                        'Wipe walls, doors, door frames, and switches',
                                        'Vacuum sofas, chairs, and cushions',
                                        'Deep vacuum carpets / mop hard floors',
                                        'Clean shower area (tiles, glass, fixtures)',
                                        'Dust and Sanitize furniture surfaces',
                                        'Report damages or issues (if any)',
                                    ];
                                @endphp

                                @forelse($checklistItems as $index => $item)
                                    <label
                                        class="flex items-start gap-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer group">
                                        <div class="flex items-center h-5 mt-0.5">
                                            <input type="checkbox" id="admin-checklist-{{ $index }}"
                                                class="admin-checklist-item w-3.5 h-3.5 text-green-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-green-500 dark:focus:ring-green-600 focus:ring-2 cursor-pointer"
                                                onchange="updateAdminChecklistProgress()">
                                        </div>
                                        <div class="flex-1">
                                            <span
                                                class="text-xs text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors admin-checklist-text-{{ $index }}">
                                                {{ $item }}
                                            </span>
                                        </div>
                                    </label>
                                @empty
                                    <div class="text-center py-4">
                                        <p class="text-gray-400 dark:text-gray-500 text-xs">No checklist items</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- Progress Bar --}}
                            @if(count($checklistItems) > 0)
                                <div class="mt-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Progress</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            <span id="admin-checklist-completed">0</span> of
                                            <span id="admin-checklist-total">{{ count($checklistItems) }}</span> completed
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                        <div id="admin-checklist-progress-bar"
                                            class="bg-blue-600 h-1.5 rounded-full transition-all duration-300"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Pricing --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Amount</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">€280.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Payable Amount</span>
                                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">€120.00</span>
                            </div>
                        </div>

                        {{-- Pending Notice --}}
                        <div class="bg-none dark:bg-none rounded-lg p-4">
                            <p class="text-sm text-orange-400 dark:text-orange-500 text-center">
                                Your appointment is currently
                                <span class="font-semibold">pending</span> and have not started yet
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.general-employer>

@push('scripts')
<script>
// Checklist progress update functionality for admin history
function updateAdminChecklistProgress() {
    // Get all checklist items
    const checklistItems = document.querySelectorAll('.admin-checklist-item');
    const totalItems = checklistItems.length;

    // Count checked items
    let checkedItems = 0;
    checklistItems.forEach(item => {
        if (item.checked) {
            checkedItems++;
        }
    });

    // Calculate percentage
    const percentage = totalItems > 0 ? (checkedItems / totalItems) * 100 : 0;

    // Update progress bar
    const progressBar = document.getElementById('admin-checklist-progress-bar');
    if (progressBar) {
        progressBar.style.width = percentage + '%';
    }

    // Update counter text
    const completedCounter = document.getElementById('admin-checklist-completed');
    if (completedCounter) {
        completedCounter.textContent = checkedItems;
    }

    // Optional: Add visual feedback when all items are completed
    if (checkedItems === totalItems && totalItems > 0) {
        progressBar.classList.remove('bg-blue-600');
        progressBar.classList.add('bg-green-600');
    } else {
        progressBar.classList.remove('bg-green-600');
        progressBar.classList.add('bg-blue-600');
    }
}

// Initialize progress on page load
document.addEventListener('DOMContentLoaded', function () {
    updateAdminChecklistProgress();
});
</script>
@endpush
