<x-layouts.general-employer :title="'Appointments'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]" x-data="appointmentDrawer()">
        <!-- Header -->
        <div class="flex flex-col gap-2">
            <h1 class="text-sm font-bold text-gray-900 dark:text-white">Client Appointments</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage and approve client appointment requests</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Pending appointments</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $counts['pending'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Approved appointments</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $counts['approved'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Rejected appointments</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $counts['rejected'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Success rate</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $counts['total'] > 0 ? number_format(($counts['approved'] / $counts['total']) * 100, 1) : '0.0' }}<span class="text-lg font-medium text-gray-400 dark:text-slate-400">%</span></p>
            </div>
        </div>

        <!-- Appointments to be Approved -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Appointments to be Approved</h2>
                <div class="flex flex-row gap-2">
                    <!-- Filter by Service -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-broom text-xs"></i>
                            <span class="text-xs">Filter by Service</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="filterPendingByService('all'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">All Services</button>
                                <button type="button" @click="filterPendingByService('Final Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Final Cleaning</button>
                                <button type="button" @click="filterPendingByService('Deep Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Deep Cleaning</button>
                                <button type="button" @click="filterPendingByService('Snowout Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Snowout Cleaning</button>
                                <button type="button" @click="filterPendingByService('Daily Room Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Daily Room Cleaning</button>
                                <button type="button" @click="filterPendingByService('Hotel Cleaning Service'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Hotel Cleaning Service</button>
                                <button type="button" @click="filterPendingByService('Student Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Student Cleaning</button>
                            </div>
                        </div>
                    </div>

                    <!-- Sort by Date -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sort-amount-down text-xs"></i>
                            <span class="text-xs">Sort by Order</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="sortPendingByDate('desc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-down text-xs w-4"></i> Newest First
                                </button>
                                <button type="button" @click="sortPendingByDate('asc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-up text-xs w-4"></i> Oldest First
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Client</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Service</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Cabin</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Amount</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingAppointments as $appointment)
                        <tr class="even:bg-gray-50 dark:even:bg-gray-800/50 pending-row" data-service="{{ $appointment->service_type }}" data-date="{{ $appointment->service_date->format('Y-m-d') }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            @if($appointment->is_company_inquiry && $appointment->client->company_name)
                                                {{ $appointment->client->company_name }}
                                            @else
                                                {{ $appointment->client->first_name }} {{ $appointment->client->last_name }}
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @if($appointment->client->user)
                                                {{ $appointment->client->user->email }}
                                            @else
                                                No account (inquiry only)
                                            @endif
                                        </div>
                                    </div>
                                    @if($appointment->is_company_inquiry)
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400">
                                            Company
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-200">{{ $appointment->service_type }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->number_of_units }} unit(s) • {{ $appointment->unit_size }} m²</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-200">{{ $appointment->service_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($appointment->service_time)->format('H:i') }}
                                    @if($appointment->is_sunday)
                                        <span class="ml-1 text-orange-600 dark:text-orange-400 font-semibold">(Sunday)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $appointment->cabin_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">€{{ number_format($appointment->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button @click="openDrawer({{ $appointment->id }})" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="fa-regular fa-eye mr-1 text-xs"></i> View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                No pending appointments.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Assigned Appointments -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Approved Appointments</h2>
                <div class="flex flex-row gap-2">
                    <!-- Filter by Service -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-broom text-xs"></i>
                            <span class="text-xs">Filter by Service</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="filterApprovedByService('all'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">All Services</button>
                                <button type="button" @click="filterApprovedByService('Final Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Final Cleaning</button>
                                <button type="button" @click="filterApprovedByService('Deep Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Deep Cleaning</button>
                                <button type="button" @click="filterApprovedByService('Snowout Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Snowout Cleaning</button>
                                <button type="button" @click="filterApprovedByService('Daily Room Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Daily Room Cleaning</button>
                                <button type="button" @click="filterApprovedByService('Hotel Cleaning Service'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Hotel Cleaning Service</button>
                                <button type="button" @click="filterApprovedByService('Student Cleaning'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Student Cleaning</button>
                            </div>
                        </div>
                    </div>

                    <!-- Sort by Date -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-sort-amount-down text-xs"></i>
                            <span class="text-xs">Sort by Order</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <button type="button" @click="sortApprovedByDate('desc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-down text-xs w-4"></i> Newest First
                                </button>
                                <button type="button" @click="sortApprovedByDate('asc'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <i class="fas fa-arrow-up text-xs w-4"></i> Oldest First
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Client</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Service</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Team</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignedAppointments as $appointment)
                        <tr class="even:bg-gray-50 dark:even:bg-gray-800/50 approved-row" data-service="{{ $appointment->service_type }}" data-date="{{ $appointment->service_date->format('Y-m-d') }}" data-status="{{ $appointment->status }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            @if($appointment->is_company_inquiry && $appointment->client->company_name)
                                                {{ $appointment->client->company_name }}
                                            @else
                                                {{ $appointment->client->first_name }} {{ $appointment->client->last_name }}
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @if($appointment->client->user)
                                                {{ $appointment->client->user->email }}
                                            @else
                                                No account (inquiry only)
                                            @endif
                                        </div>
                                    </div>
                                    @if($appointment->is_company_inquiry)
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400">
                                            Company
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-200">{{ $appointment->service_type }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->number_of_units }} unit(s) • {{ $appointment->unit_size }} m²</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-200">{{ $appointment->service_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($appointment->service_time)->format('H:i') }}
                                    @if($appointment->is_sunday)
                                        <span class="ml-1 text-orange-600 dark:text-orange-400 font-semibold">(Sunday)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($appointment->assignedTeam)
                                    <div class="text-sm text-gray-900 dark:text-gray-200">Team #{{ $appointment->assignedTeam->id }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $appointment->assignedTeam->employees->map(fn($e) => $e->first_name)->join(', ') }}
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">€{{ number_format($appointment->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($appointment->status === 'approved')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400">Approved</span>
                                @elseif($appointment->status === 'rejected')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button @click="openDrawer({{ $appointment->id }})" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="fa-regular fa-eye mr-1 text-xs"></i> View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                No assigned appointments yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($assignedAppointments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $assignedAppointments->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Appointment Details Slide-in Drawer -->
        <div x-show="showDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
            <!-- Backdrop -->
            <div x-show="showDrawer"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="closeDrawer()"
                 class="absolute inset-0 bg-black/50 dark:bg-black/70"></div>

            <!-- Drawer Panel -->
            <div class="fixed inset-y-0 right-0 flex max-w-full">
                <div x-show="showDrawer"
                     x-transition:enter="transform transition ease-in-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-200"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     @click.stop
                     class="relative w-screen max-w-xl">

                    <!-- Drawer Content -->
                    <div class="h-full flex flex-col bg-white dark:bg-slate-800 shadow-2xl border-l border-gray-200 dark:border-slate-700">
                        <!-- Drawer Header -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-slate-800/50">
                            <div class="flex items-center gap-3">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Appointment No. <span x-text="apt?.id"></span>
                                </h2>
                                <!-- Status Badge -->
                                <template x-if="apt?.status === 'pending'">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400">Pending</span>
                                </template>
                                <template x-if="apt?.status === 'approved'">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400">Approved</span>
                                </template>
                                <template x-if="apt?.status === 'rejected'">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400">Rejected</span>
                                </template>
                            </div>
                            <button type="button" @click="closeDrawer()"
                                class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Loading State -->
                        <template x-if="loading">
                            <div class="flex-1 flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fa-solid fa-spinner fa-spin text-2xl text-blue-500 mb-2"></i>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Loading appointment details...</p>
                                </div>
                            </div>
                        </template>

                        <!-- Drawer Body (Scrollable) -->
                        <template x-if="!loading && apt">
                            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                                <!-- Client Information -->
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <i class="fi fi-rr-user mr-2 text-gray-400"></i>
                                        <span x-text="apt.is_company_inquiry ? 'Company Information' : 'Client Information'"></span>
                                    </h3>
                                    <div class="space-y-0">
                                        <!-- Company fields -->
                                        <template x-if="apt.is_company_inquiry">
                                            <div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Company Name</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.client.company_name"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Contact Person</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.client.first_name + ' ' + apt.client.last_name"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Business ID</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.client.business_id || 'N/A'"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.client.email"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.client.phone || 'N/A'"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">E-Invoice</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.client.einvoice_number || 'N/A'"></span>
                                                </div>
                                                <div class="flex justify-between items-start py-2.5">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Address</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white text-right max-w-[60%]" x-text="apt.client.address || 'N/A'"></span>
                                                </div>
                                            </div>
                                        </template>
                                        <!-- Personal fields -->
                                        <template x-if="!apt.is_company_inquiry">
                                            <div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.client.first_name + ' ' + apt.client.last_name"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Booking Type</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white capitalize" x-text="apt.booking_type"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.client.user_email || apt.client.email"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.client.user_phone || apt.client.phone || 'N/A'"></span>
                                                </div>
                                                <div class="flex justify-between items-start py-2.5">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Address</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white text-right max-w-[60%]" x-text="apt.client.address || 'N/A'"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Service Details -->
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <i class="fi fi-rr-broom mr-2 text-gray-400"></i>
                                        <span x-text="apt.is_company_inquiry ? 'Service Inquiry Details' : 'Service Details'"></span>
                                    </h3>

                                    <!-- Company Service Inquiry -->
                                    <template x-if="apt.is_company_inquiry">
                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Requested Services</p>
                                                <div class="flex flex-wrap gap-1.5">
                                                    <template x-for="svc in apt.service.company_service_types" :key="svc">
                                                        <span class="px-2.5 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded-full text-xs font-medium" x-text="svc"></span>
                                                    </template>
                                                    <template x-if="!apt.service.company_service_types || apt.service.company_service_types.length === 0">
                                                        <span class="text-sm text-gray-500 dark:text-gray-400">No services specified</span>
                                                    </template>
                                                </div>
                                            </div>
                                            <template x-if="apt.service.other_concerns">
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Additional Information</p>
                                                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap" x-text="apt.service.other_concerns"></p>
                                                    </div>
                                                </div>
                                            </template>
                                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                                <p class="text-xs text-yellow-800 dark:text-yellow-400">
                                                    <i class="fi fi-rr-info-circle mr-1"></i>
                                                    This is a service inquiry. A custom quotation needs to be prepared.
                                                </p>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Personal Service Details -->
                                    <template x-if="!apt.is_company_inquiry">
                                        <div>
                                            <div class="space-y-0 mb-4">
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Service Type</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.service.service_type"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Units</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.service.number_of_units + ' unit(s)'"></span>
                                                </div>
                                                <div class="flex justify-between items-start py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Date</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white text-right">
                                                        <span x-text="apt.service.service_date"></span>
                                                        <template x-if="apt.service.is_sunday">
                                                            <span class="ml-1 text-xs text-orange-600 dark:text-orange-400 font-semibold">(Sunday)</span>
                                                        </template>
                                                        <template x-if="apt.service.is_holiday">
                                                            <span class="ml-1 text-xs text-orange-600 dark:text-orange-400 font-semibold">(Holiday)</span>
                                                        </template>
                                                    </span>
                                                </div>
                                                <div class="flex justify-between items-center py-2.5">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Time</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.service.service_time"></span>
                                                </div>
                                            </div>

                                            <!-- Unit Details -->
                                            <template x-if="apt.service.unit_details && apt.service.unit_details.length > 0">
                                                <div class="space-y-3">
                                                    <p class="text-sm font-semibold text-gray-700 dark:text-white flex items-center gap-2"><i class="fas fa-building text-gray-400"></i> Unit Details</p>
                                                    <template x-for="(unit, idx) in apt.service.unit_details" :key="idx">
                                                        <div class="border-b border-gray-200 dark:border-gray-600 pb-4">
                                                            <div class="flex justify-between items-start mb-2">
                                                                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400" x-text="'Unit ' + (idx + 1)"></span>
                                                                <template x-if="unit.price">
                                                                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400" x-text="'€' + parseFloat(unit.price).toFixed(2)"></span>
                                                                </template>
                                                            </div>
                                                            <div class="flex gap-8 text-sm">
                                                                <span class="text-gray-600 dark:text-gray-400">Name: <span class="font-medium text-gray-900 dark:text-white" x-text="unit.name || '-'"></span></span>
                                                                <span class="text-gray-600 dark:text-gray-400">Size: <span class="font-medium text-gray-900 dark:text-white" x-text="(unit.size || '-') + ' m²'"></span></span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="!apt.service.unit_details || apt.service.unit_details.length === 0">
                                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                    <div class="flex gap-4 text-xs">
                                                        <span class="text-gray-600 dark:text-gray-400">Cabin: <span class="font-medium text-gray-900 dark:text-white" x-text="apt.service.cabin_name"></span></span>
                                                        <span class="text-gray-600 dark:text-gray-400">Size: <span class="font-medium text-gray-900 dark:text-white" x-text="apt.service.unit_size + ' m²'"></span></span>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Special Requests -->
                                            <template x-if="apt.service.special_requests">
                                            <div class="flex flex-col mt-3 py-2.5 border-b border-gray-100 dark:border-gray-700">                                                                                        
                                                    <p class="text-sm text-gray-500 dark:text-white mb-3 flex items-center gap-2"><i class="fas fa-comment-dots text-gray-400"></i> Special Requests</p>
                                                        <p class="text-sm text-gray-900 dark:text-gray-400 mb-2" x-text="apt.service.special_requests"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                <!-- Pricing (Personal bookings only) -->
                                <template x-if="!apt.is_company_inquiry">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                            <i class="fi fi-rr-receipt mr-2 text-gray-400"></i> Pricing
                                        </h3>
                                        <div class="space-y-0">
                                            <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Subtotal</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="'€' + apt.pricing.subtotal"></span>
                                            </div>
                                            <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">VAT (24%)</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="'€' + apt.pricing.vat"></span>
                                            </div>
                                            <div class="flex justify-between items-center py-2.5">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Total</span>
                                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400" x-text="'€' + apt.pricing.total"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Quotation Notice (Company inquiries) -->
                                <template x-if="apt.is_company_inquiry">
                                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                                        <h3 class="text-sm font-semibold text-purple-900 dark:text-purple-400 mb-1 flex items-center">
                                            <i class="fi fi-rr-file-invoice mr-2"></i> Quotation Required
                                        </h3>
                                        <p class="text-xs text-gray-700 dark:text-gray-300 mb-2">
                                            Please prepare a custom quotation based on their requirements.
                                        </p>
                                        <div class="p-2 bg-white dark:bg-gray-800 rounded border border-purple-200 dark:border-purple-700">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Contact Email</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.client.email"></p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Service Checklist (Personal only) -->
                                <template x-if="!apt.is_company_inquiry && apt.checklist && apt.checklist.length > 0">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                            <i class="fi fi-rr-list-check mr-2 text-gray-400"></i> Service Checklist
                                        </h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Tasks to be completed by the assigned employee (read-only).</p>
                                        <div class="space-y-1.5">
                                            <template x-for="(item, idx) in apt.checklist" :key="idx">
                                                <div class="flex items-start gap-2 p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                                    <div class="w-4 h-4 mt-0.5 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center flex-shrink-0">
                                                        <i class="fas fa-circle text-gray-400 dark:text-gray-500 text-[5px]"></i>
                                                    </div>
                                                    <span class="text-sm text-gray-700 dark:text-gray-300" x-text="item"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Checklist Items</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="apt.checklist.length + ' items total'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Assigned Members -->
                                <template x-if="apt.team.assigned && apt.team.assigned.members && apt.team.assigned.members.length > 0">
                                    <div class="rounded-xl p-6 px-0 sshadow-sm">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                            <i class="fas fa-users text-gray-400"></i>
                                            Team Members
                                        </h3>
                                        <div class="space-y-3">
                                            <template x-for="(m, i) in apt.team.assigned.members" :key="i">
                                                <div class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                    <div class="w-7 h-7 bg-gradient-to-br from-blue-500 to-blue-500 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0"
                                                        x-text="m.name.charAt(0).toUpperCase()">
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="m.name"></p>
                                                        <template x-if="m.is_driver">
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Driver</p>
                                                        </template>
                                                        <template x-if="!m.is_driver">
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Team Member</p>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Rejection Reason -->
                                <template x-if="apt.status === 'rejected' && apt.rejection.reason">
                                    <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                        <h3 class="text-sm font-semibold text-red-800 dark:text-red-400 mb-1 flex items-center">
                                            <i class="fi fi-rr-cross-circle mr-2"></i> Rejection Reason
                                        </h3>
                                        <p class="text-sm text-gray-900 dark:text-white" x-text="apt.rejection.reason"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="'Rejected by ' + apt.rejection.by + ' on ' + apt.rejection.at"></p>
                                    </div>
                                </template>

                                <!-- Timeline -->
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <i class="fi fi-rr-time-past mr-2 text-gray-400"></i> Timeline
                                    </h3>
                                    <div class="space-y-0">
                                        <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Submitted</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.timeline.submitted"></span>
                                        </div>
                                        <template x-if="apt.timeline.approved">
                                            <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">Approved</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.timeline.approved"></span>
                                            </div>
                                        </template>
                                        <template x-if="apt.timeline.rejected">
                                            <div class="flex justify-between items-center py-2.5">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">Rejected</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="apt.timeline.rejected"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                            </div>
                        </template>

                        <!-- Drawer Footer -->
                        <template x-if="!loading && apt">
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                <!-- Pending Actions -->
                                <template x-if="apt.status === 'pending'">
                                    <div class="flex gap-3">
                                        <button @click="approveAppointment()" :disabled="approving"
                                            class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                            <i class="fi fi-rr-check"></i>
                                            <span x-show="!approving">Approve</span>
                                            <span x-show="approving">Approving...</span>
                                        </button>
                                        <button @click="showRejectModal = true"
                                            class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                                            <i class="fi fi-rr-cross"></i> Reject
                                        </button>
                                    </div>
                                </template>
                                <!-- Non-pending: Close button -->
                                <template x-if="apt.status !== 'pending'">
                                    <button @click="closeDrawer()"
                                        class="w-full px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                                        Close
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal (inside drawer context) -->
        <div x-show="showRejectModal" x-cloak
             class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50" @click="showRejectModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reject Appointment</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Please provide a reason for rejection. This will be sent to the client.
                    </p>
                    <textarea x-model="rejectionReason" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 text-sm"
                        placeholder="Enter rejection reason..."></textarea>
                    <div class="flex gap-3 mt-4">
                        <button @click="showRejectModal = false"
                            class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm">
                            Cancel
                        </button>
                        <button @click="rejectAppointment()" :disabled="!rejectionReason.trim() || rejecting"
                            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            <span x-show="!rejecting">Confirm Rejection</span>
                            <span x-show="rejecting">Rejecting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    function appointmentDrawer() {
        return {
            showDrawer: false,
            loading: false,
            apt: null,
            currentId: null,
            approving: false,
            rejecting: false,
            showRejectModal: false,
            rejectionReason: '',

            async openDrawer(id) {
                this.currentId = id;
                this.showDrawer = true;
                this.loading = true;
                this.apt = null;
                document.body.style.overflow = 'hidden';

                try {
                    const response = await fetch(`/admin/appointments/${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.apt = data.appointment;
                    }
                } catch (error) {
                    console.error('Error loading appointment:', error);
                } finally {
                    this.loading = false;
                }
            },

            closeDrawer() {
                this.showDrawer = false;
                this.showRejectModal = false;
                this.rejectionReason = '';
                document.body.style.overflow = 'auto';
            },

            async approveAppointment() {
                if (!confirm('Are you sure you want to approve this appointment?')) return;
                this.approving = true;

                try {
                    const response = await fetch(`/admin/appointments/${this.currentId}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to approve appointment');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while approving the appointment');
                } finally {
                    this.approving = false;
                }
            },

            async rejectAppointment() {
                if (!this.rejectionReason.trim()) return;
                this.rejecting = true;

                try {
                    const response = await fetch(`/admin/appointments/${this.currentId}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ rejection_reason: this.rejectionReason })
                    });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to reject appointment');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while rejecting the appointment');
                } finally {
                    this.rejecting = false;
                }
            },

            // Pending table filters
            filterPendingByService(service) {
                document.querySelectorAll('.pending-row').forEach(row => {
                    if (service === 'all') {
                        row.style.display = '';
                    } else {
                        row.style.display = row.getAttribute('data-service') === service ? '' : 'none';
                    }
                });
            },

            sortPendingByDate(direction) {
                const tbody = document.querySelector('.pending-row')?.closest('tbody');
                if (!tbody) return;
                const rows = Array.from(tbody.querySelectorAll('.pending-row'));
                rows.sort((a, b) => {
                    const dateA = new Date(a.getAttribute('data-date'));
                    const dateB = new Date(b.getAttribute('data-date'));
                    return direction === 'asc' ? dateA - dateB : dateB - dateA;
                });
                rows.forEach(row => tbody.appendChild(row));
            },

            // Approved table filters
            filterApprovedByService(service) {
                document.querySelectorAll('.approved-row').forEach(row => {
                    if (service === 'all') {
                        row.style.display = '';
                    } else {
                        row.style.display = row.getAttribute('data-service') === service ? '' : 'none';
                    }
                });
            },

            sortApprovedByDate(direction) {
                const tbody = document.querySelector('.approved-row')?.closest('tbody');
                if (!tbody) return;
                const rows = Array.from(tbody.querySelectorAll('.approved-row'));
                rows.sort((a, b) => {
                    const dateA = new Date(a.getAttribute('data-date'));
                    const dateB = new Date(b.getAttribute('data-date'));
                    return direction === 'asc' ? dateA - dateB : dateB - dateA;
                });
                rows.forEach(row => tbody.appendChild(row));
            }
        };
    }
    </script>
</x-layouts.general-employer>
