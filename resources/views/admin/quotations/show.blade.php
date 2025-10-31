<x-layouts.general-employer :title="'Quotation Details'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.quotations.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        <i class="fa-solid fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Quotation #{{ $quotation->id }}</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Submitted on {{ $quotation->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
            <div>
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
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ str_replace('_', ' ', ucwords($quotation->status)) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($quotation->company_name)
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Company Name</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->company_name }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $quotation->company_name ? 'Contact Person' : 'Client Name' }}</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->client_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone Number</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->phone_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Booking Type</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $quotation->booking_type === 'personal' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400' }}">
                                    {{ ucfirst($quotation->booking_type) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Service Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Service Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Cleaning Services</label>
                            @if($quotation->cleaning_services && is_array($quotation->cleaning_services))
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($quotation->cleaning_services as $service)
                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400 text-xs rounded-full">{{ $service }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-1 text-gray-900 dark:text-white">N/A</p>
                            @endif
                        </div>
                        @if($quotation->date_of_service)
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Service</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->date_of_service->format('F d, Y') }}</p>
                        </div>
                        @endif
                        @if($quotation->duration_of_service)
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->duration_of_service }} hour(s)</p>
                        </div>
                        @endif
                        @if($quotation->type_of_urgency)
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Type of Urgency</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ str_replace('_', ' ', ucwords($quotation->type_of_urgency)) }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Property Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Property Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Property Type</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->property_type }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Number of Floors</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->floors }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Number of Rooms</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->rooms }}</p>
                        </div>
                        @if($quotation->people_per_room)
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">People per Room</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->people_per_room }}</p>
                        </div>
                        @endif
                        @if($quotation->floor_area)
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Floor Area</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->floor_area }} {{ $quotation->area_unit }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Location Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Property Location</h2>
                    <div class="grid grid-cols-1 gap-4">
                        @if($quotation->street_address)
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Street Address</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->street_address }}</p>
                        </div>
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @if($quotation->postal_code)
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Postal Code</label>
                                <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->postal_code }}</p>
                            </div>
                            @endif
                            @if($quotation->city)
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">City</label>
                                <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->city }}</p>
                            </div>
                            @endif
                            @if($quotation->district)
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">District</label>
                                <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->district }}</p>
                            </div>
                            @endif
                        </div>
                        @if($quotation->latitude && $quotation->longitude)
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">GPS Coordinates</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $quotation->latitude }}, {{ $quotation->longitude }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Pricing Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pricing</h2>
                    @if($quotation->estimated_price)
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Estimated Price</span>
                            <span class="font-semibold text-gray-900 dark:text-white">€{{ number_format($quotation->estimated_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">VAT (24%)</span>
                            <span class="font-semibold text-gray-900 dark:text-white">€{{ number_format($quotation->vat_amount, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                                <span class="font-bold text-lg text-blue-600 dark:text-blue-400">€{{ number_format($quotation->total_price, 2) }}</span>
                            </div>
                        </div>
                        @if($quotation->pricing_notes)
                        <div class="mt-4">
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Pricing Notes</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quotation->pricing_notes }}</p>
                        </div>
                        @endif
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No pricing set yet</p>
                    @endif
                </div>

                <!-- Admin Actions Card -->
                @if($quotation->reviewed_by || $quotation->quoted_by || $quotation->converted_by)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activity Log</h2>
                    <div class="space-y-3 text-sm">
                        @if($quotation->reviewed_by)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Reviewed by</p>
                            <p class="text-gray-900 dark:text-white">{{ $quotation->reviewedBy->name }}</p>
                            <p class="text-xs text-gray-400">{{ $quotation->reviewed_at->format('M d, Y g:i A') }}</p>
                        </div>
                        @endif
                        @if($quotation->quoted_by)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Quoted by</p>
                            <p class="text-gray-900 dark:text-white">{{ $quotation->quotedBy->name }}</p>
                            <p class="text-xs text-gray-400">{{ $quotation->quoted_at->format('M d, Y g:i A') }}</p>
                        </div>
                        @endif
                        @if($quotation->converted_by)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Converted by</p>
                            <p class="text-gray-900 dark:text-white">{{ $quotation->convertedBy->name }}</p>
                            <p class="text-xs text-gray-400">{{ $quotation->converted_at->format('M d, Y g:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Admin Notes -->
                @if($quotation->admin_notes || $quotation->rejection_reason)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h2>
                    @if($quotation->admin_notes)
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Admin Notes</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quotation->admin_notes }}</p>
                    </div>
                    @endif
                    @if($quotation->rejection_reason)
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejection Reason</label>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $quotation->rejection_reason }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.general-employer>
