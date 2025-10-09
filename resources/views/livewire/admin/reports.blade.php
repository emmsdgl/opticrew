<div>
    <header class="bg-white shadow-sm">
        <div class="px-8 py-4">
            <h2 class="text-2xl font-bold text-gray-800">Generate Reports</h2>
        </div>
    </header>

    <div class="p-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="font-bold text-lg mb-4">Select Date Range</h3>
            <div class="flex items-center space-x-4">
                <div>
                    <label for="start_date">Start Date:</label>
                    <input type="date" wire:model="startDate" class="border p-2 rounded">
                </div>
                <div>
                    <label for="end_date">End Date:</label>
                    <input type="date" wire:model="endDate" class="border p-2 rounded">
                </div>
                <button wire:click="generateReport" class="bg-indigo-600 text-white font-bold px-6 py-2 rounded-lg">
                    Generate Report
                </button>
            </div>
        </div>

        @if (!empty($reportData))
            <!-- Report 1: Task Summary Table -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h3 class="font-bold text-lg mb-4">Task Summary Report</h3>
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-2">Date</th>
                            <th class="text-left p-2">Task</th>
                            <th class="text-left p-2">Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['task_summary'] as $task)
                        <tr class="border-b">
                            <td class="p-2">{{ $task->scheduled_date }}</td>
                            <td class="p-2">{{ $task->task_description }}</td>
                            <td class="p-2">{{ $task->estimated_duration_minutes / 60 }} hrs</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Report 2: Daily Cleaning Breakdown -->
            <div class="bg-white rounded-lg shadow-md p-6">
                 <h3 class="font-bold text-lg mb-4">Daily Cleaning Summary</h3>
                 {{-- This is a more complex table to build, this is a simplified example --}}
                 @foreach($reportData['daily_summary'] as $date => $locations)
                    <div class="mb-4">
                        <p class="font-bold">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
                        <ul>
                        @foreach($locations as $loc)
                            <li>{{ $loc->location_type }}: {{ $loc->total_cleaned }} cleaned</li>
                        @endforeach
                        </ul>
                    </div>
                 @endforeach
            </div>
        @endif
    </div>
</div>