<x-layouts.general-employer :title="'Client Detail Report'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col gap-2">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.clients') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <i class="fi fi-rr-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $client->company_name ?: $client->full_name }}
                </h1>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ ucfirst($client->client_type) }} client - Detailed report
            </p>
        </div>

        <!-- Client Info & Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Client Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Client Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $client->email ?: ($client->user ? $client->user->email : 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $client->phone_number ?: ($client->user ? $client->user->phone : 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Address</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $client->address ?: 'N/A' }}</p>
                    </div>
                    @if($client->business_id)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Business ID</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $client->business_id }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-green-50 dark:bg-green-900/10 rounded-lg p-4">
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Total Spent</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">€{{ number_format($stats['total_spent'], 2) }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/10 rounded-lg p-4">
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Appointments</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">{{ $stats['total_appointments'] }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/10 rounded-lg p-4">
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Completed</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/10 rounded-lg p-4">
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Approved</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">{{ $stats['approved'] }}</p>
                </div>
                <div class="bg-orange-50 dark:bg-orange-900/10 rounded-lg p-4">
                    <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">Pending</p>
                    <p class="text-2xl font-bold text-orange-700 dark:text-orange-300 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/10 rounded-lg p-4">
                    <p class="text-sm text-red-600 dark:text-red-400 font-medium">Cancelled</p>
                    <p class="text-2xl font-bold text-red-700 dark:text-red-300 mt-1">{{ $stats['cancelled'] }}</p>
                </div>
            </div>
        </div>

        <!-- Appointment History -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Appointment History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Service Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Units</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($appointment->service_date)->format('M d, Y') }}
                                    <span class="text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($appointment->service_time)->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $appointment->service_type }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $appointment->number_of_units }} {{ $appointment->unit_size }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($appointment->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                        @elseif($appointment->status === 'approved') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                                        @elseif($appointment->status === 'pending') bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400
                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                                        @endif">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 dark:text-green-400">
                                    €{{ number_format($appointment->total_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <i class="fi fi-rr-inbox text-4xl mb-2"></i>
                                        <p>No appointments found for this period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.general-employer>
