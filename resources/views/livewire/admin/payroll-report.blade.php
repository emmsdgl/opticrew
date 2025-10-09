<div>
    <header class="bg-white shadow-sm">
        <div class="px-8 py-4">
            <h2 class="text-2xl font-bold text-gray-800">Payroll Report</h2>
        </div>
    </header>

    <div class="p-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="font-bold text-lg mb-4">Generate Payroll for Pay Period</h3>
            <div class="flex items-center space-x-4">
                <div>
                    <label for="start_date">Start Date:</label>
                    <input type="date" wire:model="startDate" class="border p-2 rounded">
                </div>
                <div>
                    <label for="end_date">End Date:</label>
                    <input type="date" wire:model="endDate" class="border p-2 rounded">
                </div>
                <button wire:click="generatePayroll" wire:loading.attr="disabled" class="bg-indigo-600 text-white font-bold px-6 py-2 rounded-lg hover:bg-indigo-700">
                    <span wire:loading.remove>Generate</span>
                    <span wire:loading>Generating...</span>
                </button>
            </div>
        </div>

        @if (!empty($payrollData))
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-bold text-lg mb-4">Payroll Summary for {{ $startDate }} to {{ $endDate }}</h3>
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr class="text-left font-bold border-b-2">
                                <th class="px-6 py-3">Employee Name</th>
                                <th class="px-6 py-3">Regular Hours</th>
                                <th class="px-6 py-3">Overtime Hours</th> <!-- ADD THIS HEADER -->
                                <th class="px-6 py-3">Sunday/Holiday Hours</th>
                                <th class="px-6 py-3">Total Hours</th>
                                <th class="px-6 py-3">Total Pay</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($payrollData as $data)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $data['employee_name'] }}</td>
                                    <td class="px-6 py-4">{{ $data['regular_hours'] }}</td>
                                    <td class="px-6 py-4">{{ $data['overtime_hours'] }}</td> <!-- ADD THIS CELL -->
                                    <td class="px-6 py-4">{{ $data['sunday_holiday_hours'] }}</td>
                                    <td class="px-6 py-4 font-semibold">{{ $data['total_hours'] }}</td>
                                    <td class="px-6 py-4 font-bold text-green-600">€{{ number_format($data['total_pay'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>