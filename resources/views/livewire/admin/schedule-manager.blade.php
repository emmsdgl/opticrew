<div>
    <div class="p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Employee Schedule Manager</h2>

        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Month Navigation -->
            <div class="flex items-center justify-between mb-4">
                <button wire:click="previousMonth" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">&lt; Previous</button>
                <h3 class="text-xl font-bold">{{ $currentDate->format('F Y') }}</h3>
                <button wire:click="nextMonth" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Next &gt;</button>
            </div>

            <!-- Schedule Table -->
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="text-left font-bold">
                            <th class="px-4 py-3">Employee</th>
                            @for ($day = 1; $day <= $currentDate->daysInMonth; $day++)
                                <th class="text-center px-2 py-3">{{ $day }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($employees as $employee)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3 font-semibold">{{ $employee->full_name }}</td>
                                @for ($day = 1; $day <= $currentDate->daysInMonth; $day++)
                                    @php
                                        $date = $currentDate->copy()->setDay($day)->format('Y-m-d');
                                        $isDayOff = $schedules->has($employee->id . '-' . $date);
                                    @endphp
                                    <td class="text-center">
                                        <!-- THIS IS THE NEW, CORRECT BUTTON CODE -->
                                        <button wire:click="toggleDayOff({{ $employee->id }}, '{{ $date }}')" 
                                                class="w-8 h-8 rounded-full {{ $isDayOff ? 'bg-red-500 text-white' : 'bg-green-200 text-green-800' }}">
                                            {{ $isDayOff ? 'O' : 'W' }}
                                        </button>
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                 <div class="mt-4 flex items-center justify-center gap-6">
                    <div class="flex items-center gap-2"><div class="w-4 h-4 bg-green-200 rounded-full"></div><span class="text-sm text-gray-600">Work Day (W)</span></div>
                    <div class="flex items-center gap-2"><div class="w-4 h-4 bg-red-500 rounded-full"></div><span class="text-sm text-gray-600">Day Off (O)</span></div>
                </div>
            </div>
        </div>
    </div>
</div>