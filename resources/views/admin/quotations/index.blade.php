<x-layouts.general-employer :title="'Quotation Requests'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]" x-data="quotationDrawer()">
        <!-- Header -->
        <div class="flex flex-col gap-2">
            <h1 class="text-sm font-bold text-gray-900 dark:text-white">Quotation Requests</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">View and manage price quotation requests from clients</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden my-6">
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Pending review</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $quotations->where('status', 'pending_review')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Quoted</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $quotations->where('status', 'quoted')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Accepted</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $quotations->where('status', 'accepted')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 mb-2 ml-3">Total</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white ml-3">{{ $quotations->total() }}</p>
            </div>
        </div>

        <!-- Quotations Table -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">All Quotations</h2>
                <div class="flex flex-row gap-2">
                    <!-- Search -->
                    <form method="GET" action="{{ route('admin.quotations.index') }}" class="relative">
                        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                        @if(request('booking_type'))<input type="hidden" name="booking_type" value="{{ request('booking_type') }}">@endif
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                               class="w-40 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-400">
                    </form>

                    <!-- Filter by Status -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter text-xs"></i>
                            <span class="text-xs">{{ request('status') ? str_replace('_', ' ', ucfirst(request('status'))) : 'Filter by Status' }}</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <a href="{{ route('admin.quotations.index', array_merge(request()->except('status', 'page'))) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">All Statuses</a>
                                <a href="{{ route('admin.quotations.index', array_merge(request()->except('page'), ['status' => 'pending_review'])) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Pending Review</a>
                                <a href="{{ route('admin.quotations.index', array_merge(request()->except('page'), ['status' => 'under_review'])) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Under Review</a>
                                <a href="{{ route('admin.quotations.index', array_merge(request()->except('page'), ['status' => 'quoted'])) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Quoted</a>
                                <a href="{{ route('admin.quotations.index', array_merge(request()->except('page'), ['status' => 'accepted'])) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Accepted</a>
                                <a href="{{ route('admin.quotations.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Rejected</a>
                                <a href="{{ route('admin.quotations.index', array_merge(request()->except('page'), ['status' => 'converted'])) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Converted</a>
                            </div>
                        </div>
                    </div>

                    <!-- Filter by Type -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-building text-xs"></i>
                            <span class="text-xs">{{ request('booking_type') ? ucfirst(request('booking_type')) : 'Filter by Type' }}</span>
                            <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-10">
                            <div class="py-1">
                                <a href="{{ route('admin.quotations.index', array_merge(request()->except('booking_type', 'page'))) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">All Types</a>
                                <a href="{{ route('admin.quotations.index', array_merge(request()->except('page'), ['booking_type' => 'personal'])) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Personal</a>
                                <a href="{{ route('admin.quotations.index', array_merge(request()->except('page'), ['booking_type' => 'company'])) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Company</a>
                            </div>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    @if(request('search') || request('status') || request('booking_type'))
                    <a href="{{ route('admin.quotations.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                        <span class="text-xs">Clear</span>
                    </a>
                    @endif
                </div>
            </div>
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Client</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Property</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quotation)
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800/50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#{{ $quotation->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $quotation->client_name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $quotation->email }}</div>
                            @if($quotation->company_name)
                            <div class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $quotation->company_name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $quotation->booking_type === 'personal' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400' }}">
                                {{ ucfirst($quotation->booking_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-200">{{ $quotation->property_type }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->rooms }} rooms, {{ $quotation->floors }} floor(s)</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-200">{{ $quotation->city ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->district ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending_review' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
                                    'under_review' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400',
                                    'quoted' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
                                    'accepted' => 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
                                    'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400',
                                    'converted' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ str_replace('_', ' ', ucfirst($quotation->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-200">{{ $quotation->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button @click="openDrawer({{ $quotation->id }})" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                <i class="fa-regular fa-eye mr-1 text-xs"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            No quotation requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($quotations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $quotations->links() }}
            </div>
            @endif
        </div>
        </div>

        <!-- Quotation Details Slide-in Drawer -->
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
                                    Quotation No. <span x-text="q?.id"></span>
                                </h2>
                                <!-- Status Badge -->
                                <template x-if="q">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                          :class="{
                                              'bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400': q.status === 'pending_review',
                                              'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400': q.status === 'under_review',
                                              'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400': q.status === 'quoted',
                                              'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400': q.status === 'accepted',
                                              'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400': q.status === 'rejected',
                                              'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400': q.status === 'converted',
                                          }"
                                          x-text="q.status_label">
                                    </span>
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
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Loading quotation details...</p>
                                </div>
                            </div>
                        </template>

                        <!-- Drawer Body (Scrollable) -->
                        <template x-if="!loading && q">
                            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                                <!-- Contact Information -->
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <i class="fi fi-rr-user mr-2 text-gray-400"></i>
                                        <span x-text="q.contact.company_name ? 'Company Information' : 'Client Information'"></span>
                                    </h3>
                                    <div class="space-y-0">
                                        <template x-if="q.contact.company_name">
                                            <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Company Name</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.contact.company_name"></span>
                                            </div>
                                        </template>
                                        <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400" x-text="q.contact.company_name ? 'Contact Person' : 'Client Name'"></span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.contact.client_name"></span>
                                        </div>
                                        <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.contact.email"></span>
                                        </div>
                                        <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.contact.phone_number"></span>
                                        </div>
                                        <div class="flex justify-between items-center py-2.5">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Booking Type</span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize"
                                                  :class="q.contact.booking_type === 'personal' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400'"
                                                  x-text="q.contact.booking_type"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Service Information -->
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <i class="fi fi-rr-broom mr-2 text-gray-400"></i> Service Information
                                    </h3>
                                    <div class="space-y-0">
                                        <template x-if="q.service.cleaning_services && q.service.cleaning_services.length > 0">
                                            <div class="py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Cleaning Services</p>
                                                <div class="flex flex-wrap gap-1.5">
                                                    <template x-for="svc in q.service.cleaning_services" :key="svc">
                                                        <span class="px-2.5 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded-full text-xs font-medium" x-text="svc"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="q.service.date_of_service">
                                            <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Date of Service</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.service.date_of_service"></span>
                                            </div>
                                        </template>
                                        <template x-if="q.service.duration_of_service">
                                            <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Duration</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.service.duration_of_service + ' hour(s)'"></span>
                                            </div>
                                        </template>
                                        <template x-if="q.service.type_of_urgency">
                                            <div class="flex justify-between items-center py-2.5">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Urgency</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.service.type_of_urgency"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Property Information -->
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <i class="fas fa-building mr-2 text-gray-400"></i> Property Information
                                    </h3>
                                    <div class="space-y-0">
                                        <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Property Type</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.property.property_type"></span>
                                        </div>
                                        <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Floors</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.property.floors"></span>
                                        </div>
                                        <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Rooms</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.property.rooms"></span>
                                        </div>
                                        <template x-if="q.property.people_per_room">
                                            <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">People per Room</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.property.people_per_room"></span>
                                            </div>
                                        </template>
                                        <template x-if="q.property.floor_area">
                                            <div class="flex justify-between items-center py-2.5">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Floor Area</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.property.floor_area + ' ' + (q.property.area_unit || '')"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Location Information -->
                                <template x-if="q.location.street_address || q.location.city || q.location.postal_code">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i> Property Location
                                        </h3>
                                        <div class="space-y-0">
                                            <template x-if="q.location.street_address">
                                                <div class="flex justify-between items-start py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Street Address</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white text-right max-w-[60%]" x-text="q.location.street_address"></span>
                                                </div>
                                            </template>
                                            <template x-if="q.location.postal_code">
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Postal Code</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.location.postal_code"></span>
                                                </div>
                                            </template>
                                            <template x-if="q.location.city">
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">City</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.location.city"></span>
                                                </div>
                                            </template>
                                            <template x-if="q.location.district">
                                                <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">District</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.location.district"></span>
                                                </div>
                                            </template>
                                            <template x-if="q.location.latitude && q.location.longitude">
                                                <div class="flex justify-between items-center py-2.5">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">GPS Coordinates</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.location.latitude + ', ' + q.location.longitude"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Pricing -->
                                <template x-if="q.pricing.estimated_price">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                            <i class="fi fi-rr-receipt mr-2 text-gray-400"></i> Pricing
                                        </h3>
                                        <div class="space-y-0">
                                            <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Estimated Price</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="'€' + q.pricing.estimated_price"></span>
                                            </div>
                                            <div class="flex justify-between items-center py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">VAT (24%)</span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="'€' + q.pricing.vat_amount"></span>
                                            </div>
                                            <div class="flex justify-between items-center py-2.5">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Total</span>
                                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400" x-text="'€' + q.pricing.total_price"></span>
                                            </div>
                                            <template x-if="q.pricing.pricing_notes">
                                                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pricing Notes</p>
                                                    <p class="text-sm text-gray-900 dark:text-white" x-text="q.pricing.pricing_notes"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Activity Log -->
                                <template x-if="q.activity.reviewed_by || q.activity.quoted_by || q.activity.converted_by">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                            <i class="fi fi-rr-time-past mr-2 text-gray-400"></i> Activity Log
                                        </h3>
                                        <div class="space-y-0">
                                            <template x-if="q.activity.reviewed_by">
                                                <div class="flex justify-between items-start py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Reviewed by</span>
                                                    <div class="text-right">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.activity.reviewed_by"></span>
                                                        <p class="text-xs text-gray-400" x-text="q.activity.reviewed_at"></p>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="q.activity.quoted_by">
                                                <div class="flex justify-between items-start py-2.5 border-b border-gray-100 dark:border-gray-700">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Quoted by</span>
                                                    <div class="text-right">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.activity.quoted_by"></span>
                                                        <p class="text-xs text-gray-400" x-text="q.activity.quoted_at"></p>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="q.activity.converted_by">
                                                <div class="flex justify-between items-start py-2.5">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Converted by</span>
                                                    <div class="text-right">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.activity.converted_by"></span>
                                                        <p class="text-xs text-gray-400" x-text="q.activity.converted_at"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Notes -->
                                <template x-if="q.notes.admin_notes || q.notes.rejection_reason">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                            <i class="fas fa-sticky-note mr-2 text-gray-400"></i> Notes
                                        </h3>
                                        <template x-if="q.notes.admin_notes">
                                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg mb-3">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Admin Notes</p>
                                                <p class="text-sm text-gray-900 dark:text-white" x-text="q.notes.admin_notes"></p>
                                            </div>
                                        </template>
                                        <template x-if="q.notes.rejection_reason">
                                            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                                <h4 class="text-sm font-semibold text-red-800 dark:text-red-400 mb-1 flex items-center">
                                                    <i class="fi fi-rr-cross-circle mr-2"></i> Rejection Reason
                                                </h4>
                                                <p class="text-sm text-gray-900 dark:text-white" x-text="q.notes.rejection_reason"></p>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Timeline -->
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                        <i class="fi fi-rr-time-past mr-2 text-gray-400"></i> Submitted
                                    </h3>
                                    <div class="flex justify-between items-center py-2.5">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Date</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="q.created_at"></span>
                                    </div>
                                </div>

                            </div>
                        </template>

                        <!-- Drawer Footer -->
                        <template x-if="!loading && q">
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50">
                                <button @click="closeDrawer()"
                                    class="w-full px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                                    Close
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    function quotationDrawer() {
        return {
            showDrawer: false,
            loading: false,
            q: null,
            currentId: null,

            async openDrawer(id) {
                this.currentId = id;
                this.showDrawer = true;
                this.loading = true;
                this.q = null;
                document.body.style.overflow = 'hidden';

                try {
                    const response = await fetch(`/admin/quotations/${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.q = data.quotation;
                    }
                } catch (error) {
                    console.error('Error loading quotation:', error);
                } finally {
                    this.loading = false;
                }
            },

            closeDrawer() {
                this.showDrawer = false;
                document.body.style.overflow = 'auto';
            }
        };
    }
    </script>
</x-layouts.general-employer>
