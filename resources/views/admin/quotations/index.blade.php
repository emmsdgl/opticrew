<x-layouts.general-employer :title="'Quotation Requests'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Quotation Requests</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">View and manage price quotation requests from clients</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Review</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $quotations->where('status', 'pending_review')->count() }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-full">
                        <i class="fa-solid fa-clock text-orange-600 dark:text-orange-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Quoted</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $quotations->where('status', 'quoted')->count() }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                        <i class="fa-solid fa-file-invoice text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Accepted</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $quotations->where('status', 'accepted')->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                        <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $quotations->total() }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <i class="fa-solid fa-list text-gray-600 dark:text-gray-300 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <form method="GET" action="{{ route('admin.quotations.index') }}" class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Status Filter -->
                <div class="w-full md:w-48">
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="quoted" {{ request('status') == 'quoted' ? 'selected' : '' }}>Quoted</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                    </select>
                </div>

                <!-- Booking Type Filter -->
                <div class="w-full md:w-48">
                    <select name="booking_type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">All Types</option>
                        <option value="personal" {{ request('booking_type') == 'personal' ? 'selected' : '' }}>Personal</option>
                        <option value="company" {{ request('booking_type') == 'company' ? 'selected' : '' }}>Company</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fa-solid fa-filter mr-2"></i>Filter
                </button>

                <!-- Clear Button -->
                @if(request('search') || request('status') || request('booking_type'))
                <a href="{{ route('admin.quotations.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-center">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
                @endif
            </form>
        </div>

        <!-- Quotations Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($quotations as $quotation)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#{{ $quotation->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $quotation->client_name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $quotation->email }}</div>
                                @if($quotation->company_name)
                                <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">{{ $quotation->company_name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $quotation->booking_type === 'personal' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400' }}">
                                    {{ ucfirst($quotation->booking_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $quotation->property_type }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->rooms }} rooms, {{ $quotation->floors }} floor(s)</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $quotation->city ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $quotation->district ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending_review' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                                        'under_review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                                        'quoted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                                        'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                                        'converted' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ str_replace('_', ' ', ucfirst($quotation->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $quotation->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.quotations.show', $quotation->id) }}"
                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="fa-solid fa-eye mr-1"></i>View Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-inbox text-gray-400 text-5xl mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400 text-lg">No quotation requests found</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Quotations submitted from the landing page will appear here</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($quotations->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $quotations->links() }}
            </div>
            @endif
        </div>
    </section>
</x-layouts.general-employer>
