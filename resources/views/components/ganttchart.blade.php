@props(['tasks' => [], 'startDate' => null, 'endDate' => null, 'dateFormat' => 'd'])

@php
    use Carbon\Carbon;
    
    // Set default dates if not provided
    $startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
    $endDate = $endDate ?? now()->endOfMonth()->format('Y-m-d');
    
    // Calculate date headers
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);
    $dateHeaders = [];
    $current = $start->copy();
    
    while ($current <= $end) {
        $dateHeaders[] = $current->format($dateFormat);
        $current->addDay();
    }
    
    // Calculate total days
    $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
@endphp

<div class="w-full bg-gray-50 dark:bg-gray-800 rounded-lg md:p-6">
    <div class="flex flex-col lg:flex-row gap-4">
        <!-- Task List Section -->
        <div class="w-full lg:w-64 flex-shrink-0 space-y-3">
            @foreach($tasks as $index => $task)
                <div class="flex items-center gap-3 bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0"
                         style="background: {{ $task['color'] ?? '#3B82F6' }}">
                        {{ $task['label'] ?? chr(65 + $index) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm truncate">{{ $task['name'] }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $task['subtitle'] ?? '' }}</p>
                    </div>
                    <button class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 flex-shrink-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>

        <!-- Gantt Chart Section -->
        <div class="flex-1 overflow-x-auto">
            <div class="min-w-max">
                <!-- Date Headers -->
                <div class="flex mb-4 gap-1">
                    @foreach($dateHeaders as $date)
                        <div class="flex-1 text-center text-xs font-medium text-gray-500 dark:text-gray-400 px-2 py-3">
                            {{ $date }}
                        </div>
                    @endforeach
                </div>

                <!-- Gantt Bars -->
                <div class="space-y-3">
                    @foreach($tasks as $task)
                        <div class="relative h-12 flex items-center" 
                             data-task="{{ json_encode($task) }}">
                            <div class="w-full flex gap-1">
                                @for($i = 0; $i < $totalDays; $i++)
                                    <div class="flex-1"></div>
                                @endfor
                            </div>
                            
                            <div class="absolute inset-y-0 flex items-center gantt-bar"
                                 data-start="{{ $task['start'] }}"
                                 data-end="{{ $task['end'] }}"
                                 data-color="{{ $task['color'] ?? '#3B82F6' }}"
                                 data-percentage="{{ $task['percentage'] ?? 0 }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gantt-bar-container {
    border-radius: 9999px;
    padding: 8px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.gantt-bar-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.gantt-bar-container::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 30%;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 9999px;
}

.gantt-progress-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: 2px solid rgba(255, 255, 255, 0.5);
    flex-shrink: 0;
}

.gantt-task-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
    min-width: 0;
}

.gantt-task-title {
    font-size: 11px;
    font-weight: 600;
    color: white;
    opacity: 0.95;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.gantt-task-dates {
    font-size: 9px;
    color: white;
    opacity: 0.8;
    white-space: nowrap;
}

.gantt-percentage {
    font-size: 18px;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
    margin-left: auto;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ganttBars = document.querySelectorAll('.gantt-bar');
    const startDate = new Date('{{ $startDate }}');
    const totalDays = {{ $totalDays }};
    
    ganttBars.forEach(bar => {
        const taskStart = new Date(bar.dataset.start);
        const taskEnd = new Date(bar.dataset.end);
        const color = bar.dataset.color;
        const percentage = bar.dataset.percentage;
        
        const startOffset = Math.floor((taskStart - startDate) / (1000 * 60 * 60 * 24));
        const duration = Math.floor((taskEnd - taskStart) / (1000 * 60 * 60 * 24)) + 1;
        
        const leftPercent = (startOffset / totalDays) * 100;
        const widthPercent = (duration / totalDays) * 100;
        
        bar.style.left = leftPercent + '%';
        bar.style.width = widthPercent + '%';
        
        // Create gradient background
        const lightColor = adjustColorBrightness(color, 40);
        bar.innerHTML = `
            <div class="gantt-bar-container" style="background: linear-gradient(90deg, ${color} 0%, ${lightColor} 100%); width: 100%;">
                <div class="gantt-progress-dot"></div>
                <div class="gantt-task-info">
                    <div class="gantt-task-title">${bar.closest('[data-task]').dataset.task ? JSON.parse(bar.closest('[data-task]').dataset.task).name : ''}</div>
                    <div class="gantt-task-dates">${formatDate(taskStart)} - ${formatDate(taskEnd)}</div>
                </div>
                <div class="gantt-percentage">${percentage}%</div>
            </div>
        `;
    });
    
    function adjustColorBrightness(color, amount) {
        const num = parseInt(color.replace('#', ''), 16);
        const r = Math.min(255, ((num >> 16) & 255) + amount);
        const g = Math.min(255, ((num >> 8) & 255) + amount);
        const b = Math.min(255, (num & 255) + amount);
        return '#' + ((r << 16) | (g << 8) | b).toString(16).padStart(6, '0');
    }
    
    function formatDate(date) {
        const month = date.toLocaleString('default', { month: 'short' });
        const day = date.getDate();
        return `${month} ${day}`;
    }
});
</script>