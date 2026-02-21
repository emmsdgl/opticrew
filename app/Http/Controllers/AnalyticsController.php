<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Task;
use App\Models\Feedback;
use App\Models\ContractedClient;
use App\Models\Client;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // ==========================================
        // KPI CARDS DATA
        // ==========================================

        // Calculate active employees (employees who have clocked in within last 30 days)
        $activeEmployeesCount = Attendance::where('clock_in', '>=', Carbon::now()->subDays(30))
            ->distinct('employee_id')
            ->count('employee_id');

        // If no recent attendance, show total employees
        if ($activeEmployeesCount == 0) {
            $activeEmployeesCount = Employee::count();
        }

        // Calculate previous month active employees for percentage change
        $previousMonthActiveCount = Attendance::whereBetween('clock_in', [
                Carbon::now()->subDays(60),
                Carbon::now()->subDays(30)
            ])
            ->distinct('employee_id')
            ->count('employee_id');

        $activeEmployeesChange = $previousMonthActiveCount > 0
            ? (($activeEmployeesCount - $previousMonthActiveCount) / $previousMonthActiveCount) * 100
            : 0;

        // Total tasks (orders) in current month
        $totalTasksThisMonth = Task::whereNull('deleted_at')
            ->whereMonth('scheduled_date', Carbon::now()->month)
            ->whereYear('scheduled_date', Carbon::now()->year)
            ->count();

        // Total tasks last month for comparison
        $totalTasksLastMonth = Task::whereNull('deleted_at')
            ->whereMonth('scheduled_date', Carbon::now()->subMonth()->month)
            ->whereYear('scheduled_date', Carbon::now()->subMonth()->year)
            ->count();

        $tasksChange = $totalTasksLastMonth > 0
            ? (($totalTasksThisMonth - $totalTasksLastMonth) / $totalTasksLastMonth) * 100
            : 0;

        // Customer rating average (from client feedback submissions)
        // Uses COALESCE to handle both modal feedback (rating) and form feedback (overall_rating)
        $averageRating = Feedback::whereNotNull('client_id')
            ->selectRaw('AVG(COALESCE(rating, overall_rating)) as avg_rating')
            ->value('avg_rating');
        $averageRating = $averageRating ? round($averageRating, 1) : 0;

        // Previous month average rating
        $previousMonthRating = Feedback::whereNotNull('client_id')
            ->where('created_at', '<', Carbon::now()->startOfMonth())
            ->selectRaw('AVG(COALESCE(rating, overall_rating)) as avg_rating')
            ->value('avg_rating');
        $previousMonthRating = $previousMonthRating ? round($previousMonthRating, 1) : 0;

        $ratingChange = $previousMonthRating > 0
            ? round($averageRating - $previousMonthRating, 1)
            : 0;

        // Build KPI cards array
        $kpiCards = [
            [
                'icon' => '<i class="fi fi-rr-users-alt"></i>',
                'iconColor' => '#3b82f6',
                'label' => 'Active Employees',
                'amount' => number_format($activeEmployeesCount),
                'description' => 'Last 30 days',
                'percentage' => ($activeEmployeesChange >= 0 ? '+' : '') . number_format($activeEmployeesChange, 1) . '%',
                'percentageColor' => $activeEmployeesChange >= 0 ? '#10b981' : '#ef4444',
            ],
            [
                'icon' => '<i class="fi fi-rr-shopping-cart"></i>',
                'iconColor' => '#f59e0b',
                'label' => 'Total Tasks',
                'amount' => number_format($totalTasksThisMonth),
                'description' => 'This month',
                'percentage' => ($tasksChange >= 0 ? '+' : '') . number_format($tasksChange, 1) . '%',
                'percentageColor' => $tasksChange >= 0 ? '#10b981' : '#ef4444',
            ],
            [
                'icon' => '<i class="fi fi-rr-star"></i>',
                'iconColor' => '#8b5cf6',
                'label' => 'Customer Rating',
                'amount' => $averageRating > 0 ? $averageRating : 'N/A',
                'description' => $averageRating > 0 ? 'Average score' : 'No feedback yet',
                'percentage' => $averageRating > 0 ? ($ratingChange >= 0 ? '+' : '') . $ratingChange : '--',
                'percentageColor' => $ratingChange >= 0 ? '#10b981' : '#ef4444',
            ],
        ];

        // ==========================================
        // PRODUCTIVITY RATE (LINE CHART DATA)
        // ==========================================

        $performanceData = $this->getProductivityData();
        $defaultData = $performanceData['This Year'];

        // ==========================================
        // SERVICE DEMAND STATISTICS (PIE CHART)
        // ==========================================

        $serviceDemandData = $this->getServiceDemandData();
        $defaultServiceData = $serviceDemandData['This Year'];

        $serviceCategories = $defaultServiceData['categories'];
        $totalServiceDemand = $defaultServiceData['total'];
        $growthRate = $defaultServiceData['growthRate'];

        // ==========================================
        // CUSTOMER TRANSACTIONS TABLE
        // ==========================================

        // Get contracted clients with their order counts
        $contractedClients = ContractedClient::select('contracted_clients.name', DB::raw('COUNT(tasks.id) as orders'))
            ->leftJoin('locations', 'contracted_clients.id', '=', 'locations.contracted_client_id')
            ->leftJoin('tasks', function($join) {
                $join->on('locations.id', '=', 'tasks.location_id')
                     ->whereNull('tasks.deleted_at');
            })
            ->groupBy('contracted_clients.id', 'contracted_clients.name')
            ->having('orders', '>', 0)
            ->orderBy('orders', 'DESC')
            ->limit(5)
            ->get()
            ->map(function ($client) {
                return [
                    'name' => $client->name,
                    'status' => 'contracted',
                    'orders' => $client->orders
                ];
            });

        // Get external clients with their appointment counts
        // Group by name to combine duplicate client names
        $externalClients = Client::select(
                DB::raw("CONCAT(clients.first_name, ' ', clients.last_name) as name"),
                DB::raw('COUNT(client_appointments.id) as orders')
            )
            ->leftJoin('client_appointments', 'clients.id', '=', 'client_appointments.client_id')
            ->groupBy(DB::raw("CONCAT(clients.first_name, ' ', clients.last_name)"))
            ->having('orders', '>', 0)
            ->orderBy('orders', 'DESC')
            ->limit(5)
            ->get()
            ->map(function ($client) {
                return [
                    'name' => $client->name,
                    'status' => 'external',
                    'orders' => $client->orders
                ];
            });

        // Merge and sort by orders
        $tableData = $contractedClients->concat($externalClients)
            ->sortByDesc('orders')
            ->take(10)
            ->values()
            ->toArray();

        // If no customer data, show placeholder
        if (empty($tableData)) {
            $tableData = [
                [
                    'name' => 'No customers yet',
                    'status' => 'contracted',
                    'orders' => 0
                ]
            ];
        }

        // Define table columns (keep original structure)
        $columns = [
            [
                'label' => 'Name',
                'key' => 'name',
                'headerClass' => '',
                'cellClass' => 'font-medium'
            ],
            [
                'label' => 'Status',
                'key' => 'status',
                'type' => 'status',
                'statusConfig' => [
                    'contracted' => [
                        'label' => 'Contracted',
                        'bgColor' => 'bg-blue-50 dark:bg-blue-900/20',
                        'textColor' => 'text-blue-700 dark:text-blue-400',
                        'borderColor' => 'border-blue-200 dark:border-blue-800'
                    ],
                    'external' => [
                        'label' => 'External',
                        'bgColor' => 'bg-indigo-50 dark:bg-indigo-900/20',
                        'textColor' => 'text-indigo-700 dark:text-indigo-400',
                        'borderColor' => 'border-indigo-200 dark:border-indigo-800'
                    ]
                ]
            ],
            [
                'label' => 'Orders',
                'key' => 'orders',
                'headerClass' => '',
                'cellClass' => 'font-semibold'
            ]
        ];

        // ==========================================
        // RETURN VIEW WITH ALL DATA
        // ==========================================

        return view('admin.analytics', [
            'kpiCards' => $kpiCards,
            'performanceData' => $performanceData,
            'defaultData' => $defaultData,
            'serviceDemandData' => $serviceDemandData,
            'serviceCategories' => $serviceCategories,
            'totalServiceDemand' => $totalServiceDemand,
            'growthRate' => abs(round($growthRate, 2)),
            'growthTrend' => $growthRate >= 0 ? 'up' : 'down',
            'tableData' => $tableData,
            'columns' => $columns,
        ]);
    }

    /**
     * Get productivity data for different time periods
     */
    private function getProductivityData()
    {
        $data = [];

        // TODAY
        $todayHours = [];
        $todayLabels = [];
        for ($hour = 0; $hour <= 23; $hour += 4) {
            $todayLabels[] = sprintf('%02d:00', $hour);
            $startHour = Carbon::today()->addHours($hour);
            $endHour = Carbon::today()->addHours($hour + 4);

            $hours = Attendance::whereBetween('clock_in', [$startHour, $endHour])
                ->whereNotNull('clock_out')
                ->get()
                ->sum(function ($attendance) {
                    $clockIn = Carbon::parse($attendance->clock_in);
                    $clockOut = Carbon::parse($attendance->clock_out);
                    return $clockIn->diffInMinutes($clockOut) / 60;
                });
            $todayHours[] = round($hours, 0);
        }

        $currentToday = end($todayHours);
        $previousToday = $todayHours[count($todayHours) - 2] ?? 0;
        $changeToday = $currentToday - $previousToday;
        $changePercentToday = $previousToday > 0 ? (($changeToday / $previousToday) * 100) : 0;

        $data['Today'] = [
            'currentValue' => $currentToday,
            'changeValue' => abs($changeToday),
            'changePercent' => abs($changePercentToday),
            'values' => $todayHours,
            'labels' => $todayLabels,
            'dateRange' => $todayLabels[0] . ' - ' . $todayLabels[count($todayLabels) - 1]
        ];

        // THIS WEEK
        $weekHours = [];
        $weekLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $startOfWeek = Carbon::now()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $hours = Attendance::whereDate('clock_in', $day)
                ->whereNotNull('clock_out')
                ->get()
                ->sum(function ($attendance) {
                    $clockIn = Carbon::parse($attendance->clock_in);
                    $clockOut = Carbon::parse($attendance->clock_out);
                    return $clockIn->diffInMinutes($clockOut) / 60;
                });
            $weekHours[] = round($hours, 0);
        }

        $currentWeek = end($weekHours);
        $previousWeek = $weekHours[count($weekHours) - 2] ?? 0;
        $changeWeek = $currentWeek - $previousWeek;
        $changePercentWeek = $previousWeek > 0 ? (($changeWeek / $previousWeek) * 100) : 0;

        $data['This Week'] = [
            'currentValue' => $currentWeek,
            'changeValue' => abs($changeWeek),
            'changePercent' => abs($changePercentWeek),
            'values' => $weekHours,
            'labels' => $weekLabels,
            'dateRange' => 'Mon - Sun'
        ];

        // THIS MONTH
        $monthHours = [];
        $monthLabels = [];
        $daysInMonth = Carbon::now()->daysInMonth;
        $interval = max(1, floor($daysInMonth / 6));

        for ($day = 1; $day <= $daysInMonth; $day += $interval) {
            $date = Carbon::now()->startOfMonth()->addDays($day - 1);
            $monthLabels[] = $date->format('M d');

            $hours = Attendance::whereDate('clock_in', $date)
                ->whereNotNull('clock_out')
                ->get()
                ->sum(function ($attendance) {
                    $clockIn = Carbon::parse($attendance->clock_in);
                    $clockOut = Carbon::parse($attendance->clock_out);
                    return $clockIn->diffInMinutes($clockOut) / 60;
                });
            $monthHours[] = round($hours, 0);
        }

        $currentMonth = end($monthHours);
        $previousMonth = $monthHours[count($monthHours) - 2] ?? 0;
        $changeMonth = $currentMonth - $previousMonth;
        $changePercentMonth = $previousMonth > 0 ? (($changeMonth / $previousMonth) * 100) : 0;

        $data['This Month'] = [
            'currentValue' => $currentMonth,
            'changeValue' => abs($changeMonth),
            'changePercent' => abs($changePercentMonth),
            'values' => $monthHours,
            'labels' => $monthLabels,
            'dateRange' => $monthLabels[0] . ' - ' . $monthLabels[count($monthLabels) - 1]
        ];

        // THIS YEAR
        $yearHours = [];
        $yearLabels = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $yearLabels[] = $month->format('M');

            $hours = Attendance::whereYear('clock_in', $month->year)
                ->whereMonth('clock_in', $month->month)
                ->whereNotNull('clock_out')
                ->get()
                ->sum(function ($attendance) {
                    $clockIn = Carbon::parse($attendance->clock_in);
                    $clockOut = Carbon::parse($attendance->clock_out);
                    return $clockIn->diffInMinutes($clockOut) / 60;
                });
            $yearHours[] = round($hours, 0);
        }

        $currentYear = end($yearHours);
        $previousYear = $yearHours[count($yearHours) - 2] ?? 0;
        $changeYear = $currentYear - $previousYear;
        $changePercentYear = $previousYear > 0 ? (($changeYear / $previousYear) * 100) : 0;

        $data['This Year'] = [
            'currentValue' => $currentYear,
            'changeValue' => abs($changeYear),
            'changePercent' => abs($changePercentYear),
            'values' => $yearHours,
            'labels' => $yearLabels,
            'dateRange' => $yearLabels[0] . ' - ' . $yearLabels[count($yearLabels) - 1]
        ];

        return $data;
    }

    /**
     * Get service demand data for different time periods
     */
    private function getServiceDemandData()
    {
        $colors = ['#275BED', '#779FF4', '#CAD9F8', '#4CAF50', '#FF9800'];
        $data = [];

        // TODAY - Group by client name instead of task description
        $todayTasks = Task::whereNull('tasks.deleted_at')
            ->whereDate('tasks.scheduled_date', Carbon::today())
            ->leftJoin('locations', 'tasks.location_id', '=', 'locations.id')
            ->leftJoin('contracted_clients', 'locations.contracted_client_id', '=', 'contracted_clients.id')
            ->leftJoin('clients', 'tasks.client_id', '=', 'clients.id')
            ->select(
                DB::raw("COALESCE(
                    contracted_clients.name,
                    CONCAT(clients.first_name, ' ', clients.last_name),
                    'Unknown Client'
                ) as client_name"),
                DB::raw('COUNT(tasks.id) as count')
            )
            ->groupBy('client_name')
            ->orderBy('count', 'DESC')
            ->limit(3)
            ->get();

        $todayCategories = [];
        foreach ($todayTasks as $index => $task) {
            $todayCategories[] = [
                'name' => $task->client_name,
                'value' => $task->count,
                'color' => $colors[$index % count($colors)]
            ];
        }
        if (empty($todayCategories)) {
            $todayCategories = [['name' => 'No Tasks Today', 'value' => 1, 'color' => '#E0E0E0']];
        }

        $todayTotal = array_sum(array_column($todayCategories, 'value'));
        $yesterdayTotal = Task::whereNull('deleted_at')
            ->whereDate('scheduled_date', Carbon::yesterday())
            ->count();
        $todayGrowth = $yesterdayTotal > 0 ? ((($todayTotal - $yesterdayTotal) / $yesterdayTotal) * 100) : 0;

        $data['Today'] = [
            'categories' => $todayCategories,
            'total' => $todayTotal,
            'growthRate' => abs(round($todayGrowth, 2))
        ];

        // THIS WEEK - Group by client name
        $weekTasks = Task::whereNull('tasks.deleted_at')
            ->whereBetween('tasks.scheduled_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->leftJoin('locations', 'tasks.location_id', '=', 'locations.id')
            ->leftJoin('contracted_clients', 'locations.contracted_client_id', '=', 'contracted_clients.id')
            ->leftJoin('clients', 'tasks.client_id', '=', 'clients.id')
            ->select(
                DB::raw("COALESCE(
                    contracted_clients.name,
                    CONCAT(clients.first_name, ' ', clients.last_name),
                    'Unknown Client'
                ) as client_name"),
                DB::raw('COUNT(tasks.id) as count')
            )
            ->groupBy('client_name')
            ->orderBy('count', 'DESC')
            ->limit(3)
            ->get();

        $weekCategories = [];
        foreach ($weekTasks as $index => $task) {
            $weekCategories[] = [
                'name' => $task->client_name,
                'value' => $task->count,
                'color' => $colors[$index % count($colors)]
            ];
        }
        if (empty($weekCategories)) {
            $weekCategories = [['name' => 'No Tasks This Week', 'value' => 1, 'color' => '#E0E0E0']];
        }

        $weekTotal = array_sum(array_column($weekCategories, 'value'));
        $lastWeekTotal = Task::whereNull('deleted_at')
            ->whereBetween('scheduled_date', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ])
            ->count();
        $weekGrowth = $lastWeekTotal > 0 ? ((($weekTotal - $lastWeekTotal) / $lastWeekTotal) * 100) : 0;

        $data['This Week'] = [
            'categories' => $weekCategories,
            'total' => $weekTotal,
            'growthRate' => abs(round($weekGrowth, 2))
        ];

        // THIS MONTH - Group by client name
        $monthTasks = Task::whereNull('tasks.deleted_at')
            ->whereMonth('tasks.scheduled_date', Carbon::now()->month)
            ->whereYear('tasks.scheduled_date', Carbon::now()->year)
            ->leftJoin('locations', 'tasks.location_id', '=', 'locations.id')
            ->leftJoin('contracted_clients', 'locations.contracted_client_id', '=', 'contracted_clients.id')
            ->leftJoin('clients', 'tasks.client_id', '=', 'clients.id')
            ->select(
                DB::raw("COALESCE(
                    contracted_clients.name,
                    CONCAT(clients.first_name, ' ', clients.last_name),
                    'Unknown Client'
                ) as client_name"),
                DB::raw('COUNT(tasks.id) as count')
            )
            ->groupBy('client_name')
            ->orderBy('count', 'DESC')
            ->limit(3)
            ->get();

        $monthCategories = [];
        foreach ($monthTasks as $index => $task) {
            $monthCategories[] = [
                'name' => $task->client_name,
                'value' => $task->count,
                'color' => $colors[$index % count($colors)]
            ];
        }
        if (empty($monthCategories)) {
            $monthCategories = [['name' => 'No Tasks This Month', 'value' => 1, 'color' => '#E0E0E0']];
        }

        $monthTotal = array_sum(array_column($monthCategories, 'value'));
        $lastMonthTotal = Task::whereNull('deleted_at')
            ->whereMonth('scheduled_date', Carbon::now()->subMonth()->month)
            ->count();
        $monthGrowth = $lastMonthTotal > 0 ? ((($monthTotal - $lastMonthTotal) / $lastMonthTotal) * 100) : 0;

        $data['This Month'] = [
            'categories' => $monthCategories,
            'total' => $monthTotal,
            'growthRate' => abs(round($monthGrowth, 2))
        ];

        // THIS YEAR - Group by client name
        $yearTasks = Task::whereNull('tasks.deleted_at')
            ->whereYear('tasks.scheduled_date', Carbon::now()->year)
            ->leftJoin('locations', 'tasks.location_id', '=', 'locations.id')
            ->leftJoin('contracted_clients', 'locations.contracted_client_id', '=', 'contracted_clients.id')
            ->leftJoin('clients', 'tasks.client_id', '=', 'clients.id')
            ->select(
                DB::raw("COALESCE(
                    contracted_clients.name,
                    CONCAT(clients.first_name, ' ', clients.last_name),
                    'Unknown Client'
                ) as client_name"),
                DB::raw('COUNT(tasks.id) as count')
            )
            ->groupBy('client_name')
            ->orderBy('count', 'DESC')
            ->limit(3)
            ->get();

        $yearCategories = [];
        foreach ($yearTasks as $index => $task) {
            $yearCategories[] = [
                'name' => $task->client_name,
                'value' => $task->count,
                'color' => $colors[$index % count($colors)]
            ];
        }
        if (empty($yearCategories)) {
            $yearCategories = [['name' => 'No Tasks This Year', 'value' => 1, 'color' => '#E0E0E0']];
        }

        $yearTotal = array_sum(array_column($yearCategories, 'value'));
        $lastYearTotal = Task::whereNull('deleted_at')
            ->whereYear('scheduled_date', Carbon::now()->subYear()->year)
            ->count();
        $yearGrowth = $lastYearTotal > 0 ? ((($yearTotal - $lastYearTotal) / $lastYearTotal) * 100) : 0;

        $data['This Year'] = [
            'categories' => $yearCategories,
            'total' => $yearTotal,
            'growthRate' => abs(round($yearGrowth, 2))
        ];

        return $data;
    }
}
