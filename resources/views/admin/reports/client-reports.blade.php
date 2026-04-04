<x-layouts.general-employer :title="'Client Reports'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header with Date Filter -->
        <div class="flex flex-col md:items-center md:justify-between gap-4">
            <div class ="breadcrumb-component my-4 items-start w-full">
                <x-employer-components.breadcrumb :items="[
                    ['label' => 'Analytics', 'url' => route('admin.analytics')],
                    ['label' => 'Reports', 'url' => route('admin.reports.index')],
                    ['label' => 'Client Reports'],
                ]" />
            </div>
            <div class ="flex flex-row items-center gap-2 w-full justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Client Reports</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Revenue and appointment analytics</p>
                </div>

                <!-- Date Range Filter -->
                <form method="GET" action="{{ route('admin.reports.clients') }}" class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="px-3 py-2 text-xs border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <span class="text-gray-600 dark:text-gray-400">to</span>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="px-3 py-2 text-xs border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <button type="submit"
                        class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="{{ route('admin.reports.clients.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                        class="px-4 py-2 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                        <i class="fi fi-rr-download"></i> Export CSV
                    </a>
                </form>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="py-6">
            <x-employer-components.stats-cards :stats="[
                ['label' => 'Total Revenue', 'value' => '€' . number_format($totalRevenue, 2)],
                ['label' => 'Total Appointments', 'value' => $totalAppointments],
                ['label' => 'Active Clients', 'value' => $totalClients],
            ]" />
        </div>

        <!-- Service Type Breakdown -->
        <div class="flex flex-col gap-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Service Type Breakdown</h2>

            @if ($serviceBreakdown->count() > 0)
                <div x-data="{ page: 1, perPage: 5, total: {{ $serviceBreakdown->count() }}, get totalPages() { return Math.ceil(this.total / this.perPage); } }">
                <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Service Type</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Bookings</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($serviceBreakdown as $service)
                                <tr class="even:bg-gray-50 dark:even:bg-gray-800/50" x-show="{{ $loop->index }} >= (page - 1) * perPage && {{ $loop->index }} < page * perPage">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $service->service_type }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">{{ $service->count }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                                            €{{ number_format($service->revenue, 2) }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @include('components.report-pagination')
                </div>
            @else
                <div
                    class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                    <i class="fa-solid fa-inbox text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No service data available</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Service type breakdown will appear here
                        once data is available for this period</p>
                </div>
            @endif
        </div>

        <!-- Client Details -->
        <div class="flex flex-col gap-4">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Client Details</h2>

            @if ($clients->count() > 0)
                <div x-data="{ page: 1, perPage: 5, total: {{ $clients->count() }}, get totalPages() { return Math.ceil(this.total / this.perPage); } }">
                <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Client</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Type</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Appointments</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Pending</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Completed</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Total Revenue</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                <tr class="even:bg-gray-50 dark:even:bg-gray-800/50" x-show="{{ $loop->index }} >= (page - 1) * perPage && {{ $loop->index }} < page * perPage">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $client->company_name ?: $client->full_name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            @if ($client->client_type === 'contracted')
                                                Contracted Client
                                            @else
                                                {{ $client->email ?: ($client->user ? $client->user->email : 'N/A') }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $typeClass = match ($client->client_type) {
                                                'contracted'
                                                    => 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
                                                'company'
                                                    => 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
                                                default
                                                    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
                                            };
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $typeClass }}">
                                            {{ $client->client_type === 'contracted' ? 'Contracted' : ucfirst($client->client_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200">
                                            {{ $client->total_appointments }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-orange-600 dark:text-orange-400">
                                            {{ $client->pending_appointments }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-green-600 dark:text-green-400">
                                            {{ $client->completed_appointments }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                                            €{{ number_format($client->total_revenue, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        @if ($client->client_type === 'contracted')
                                            <span class="text-sm text-green-600 dark:text-green-400">
                                                <i class="fa-regular fa-circle-check mr-1 text-xs"></i> Active
                                            </span>
                                        @else
                                            <a href="{{ route('admin.reports.client-detail', ['clientId' => $client->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                                class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                                <i class="fa-regular fa-eye mr-1 text-xs"></i> View
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @include('components.report-pagination')
                </div>
            @else
                <div
                    class="w-full rounded-lg border-1 border-dashed border-gray-200 dark:border-gray-700 px-6 py-24 text-center">
                    <i class="fa-solid fa-inbox text-3xl mb-3 block w-full text-gray-400 dark:text-gray-500"></i>
                    <p class="text-base font-medium text-gray-500 dark:text-gray-400">No client data available</p>
                    <p class="text-xs mt-2 text-gray-400 dark:text-gray-500">Client details will appear here once data
                        is available for this period</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.general-employer>
