<x-layouts.general-employer :title="'Appointments'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Client Appointments</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage and approve client appointment requests</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $appointments->where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-full">
                        <i class="fi fi-rr-clock text-orange-600 dark:text-orange-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approved</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $appointments->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                        <i class="fi fi-rr-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rejected</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $appointments->where('status', 'rejected')->count() }}</p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-full">
                        <i class="fi fi-rr-cross-circle text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $appointments->total() }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                        <i class="fi fi-rr-calendar text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cabin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">#{{ $appointment->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->first_name }} {{ $appointment->client->last_name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $appointment->client->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $appointment->service_type }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $appointment->number_of_units }} unit(s) • {{ $appointment->unit_size }} m²</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $appointment->service_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($appointment->service_time)->format('H:i') }}
                                    @if($appointment->is_sunday)
                                        <span class="ml-1 text-orange-600 dark:text-orange-400 font-semibold">(Sunday)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $appointment->cabin_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">€{{ number_format($appointment->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($appointment->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400">Pending</span>
                                @elseif($appointment->status === 'approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">Approved</span>
                                @elseif($appointment->status === 'rejected')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No appointments found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                {{ $appointments->links() }}
            </div>
        </div>
    </section>
</x-layouts.general-employer>
