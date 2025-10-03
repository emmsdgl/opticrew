<div>
    <header class="bg-white shadow-sm">
        <div class="flex items-center justify-between px-8 py-4">
            <h2 class="text-2xl font-bold text-gray-800">All Tasks</h2>
            <a href="{{ route('admin.dashboard') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>New Task
            </a>
        </div>
    </header>

    <div class="p-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <input wire:model="search" type="text" placeholder="Search by task or location..." class="w-full px-4 py-2 border rounded-lg">
                <select wire:model="statusFilter" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">All Statuses</option>
                    <option value="Scheduled">Scheduled</option>
                    <option value="In-Progress">In-Progress</option>
                    <option value="Completed">Completed</option>
                </select>
                <input wire:model="dateFilter" type="date" class="w-full px-4 py-2 border rounded-lg">
            </div>

            <!-- Task Table -->
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="text-left font-bold border-b-2">
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Task / Location</th>
                            <th class="px-6 py-3">Client</th>
                            <th class="px-6 py-3">Assigned Team</th>
                            <th class="px-6 py-3">Car</th> <!-- ADD THIS HEADER -->
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($tasks as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold">{{ $task->task_description }}</div>
                                    <div class="text-sm text-gray-500">{{ $task->location->location_name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $task->location->contractedClient->name ?? 'External' }}</td>
                                <td class="px-6 py-4">
                                    {{ $task->team ? $task->team->members->pluck('employee.full_name')->join(', ') : 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $task->team->car->car_name ?? 'N/A' }} <!-- ADD THIS CELL -->
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 font-semibold leading-tight rounded-full
                                        @if($task->status == 'Scheduled') bg-yellow-100 text-yellow-700 @endif
                                        @if($task->status == 'In-Progress') bg-blue-100 text-blue-700 @endif
                                        @if($task->status == 'Completed') bg-green-100 text-green-700 @endif
                                    ">
                                        {{ $task->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No tasks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-6">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>
</div>