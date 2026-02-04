<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\TaskReview;
use App\Models\TrainingVideo;
use App\Models\OptimizationTeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeStatsController extends Controller
{
    /**
     * Get comprehensive employee statistics
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $employee = $user->employee;

            if (!$employee) {
                return response()->json([
                    'message' => 'Employee profile not found',
                    'progress' => $this->getDefaultProgress(),
                    'performance' => $this->getDefaultPerformance(),
                    'satisfaction' => $this->getDefaultSatisfaction(),
                    'recentActivities' => [],
                    'chartData' => $this->getDefaultChartData(),
                ]);
            }

            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();
            $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
            $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

            // Get all data in parallel
            $progressData = $this->getProgressData($employee, $startOfMonth, $endOfMonth);
            $performanceData = $this->getPerformanceData($employee, $user, $startOfMonth, $endOfMonth, $startOfLastMonth, $endOfLastMonth);
            $satisfactionData = $this->getSatisfactionData($employee, $startOfMonth, $endOfMonth, $startOfLastMonth, $endOfLastMonth);
            $recentActivities = $this->getRecentActivities($employee);
            $chartData = $this->getChartData($employee);

            return response()->json([
                'progress' => $progressData,
                'performance' => $performanceData,
                'satisfaction' => $satisfactionData,
                'recentActivities' => $recentActivities,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $e) {
            // Return empty data with 200 so app doesn't crash
            // Log the error for debugging
            \Log::error('Employee stats error: ' . $e->getMessage());

            return response()->json([
                'progress' => $this->getDefaultProgress(),
                'performance' => $this->getDefaultPerformance(),
                'satisfaction' => $this->getDefaultSatisfaction(),
                'recentActivities' => [],
                'chartData' => $this->getDefaultChartData(),
            ]);
        }
    }

    private function getDefaultProgress()
    {
        return [
            'progressPercentage' => 0,
            'hoursRemaining' => 160,
            'totalHours' => 160,
            'workedHours' => 0,
            'workedToday' => '00:00:00',
            'idleTimeToday' => '0hr 0mins',
        ];
    }

    private function getDefaultPerformance()
    {
        return [
            'tasksCompleted' => 0,
            'totalTasks' => 0,
            'productivityChange' => '+0',
            'trainingProgress' => 0,
            'coursesCompleted' => '0 / 0',
            'avgQuizScore' => '0%',
            'attendanceRate' => 0,
            'absences' => '0 / 0',
            'tardiness' => '0 / 0',
        ];
    }

    private function getDefaultSatisfaction()
    {
        return [
            'rating' => 0,
            'change' => '+0',
            'totalReviews' => 0,
            'services' => [],
        ];
    }

    private function getDefaultChartData()
    {
        return [
            'months' => ['January', 'February', 'March'],
            'workedHours' => [0, 0, 0],
            'idleHours' => [0, 0, 0],
            'colors' => ['#22c55e', '#ef4444', '#3b82f6'],
        ];
    }

    /**
     * Get progress data (worked hours, idle time, etc.)
     */
    private function getProgressData($employee, $startOfMonth, $endOfMonth)
    {
        // Get attendance records for this month
        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->whereBetween('clock_in', [$startOfMonth, $endOfMonth])
            ->get();

        // Calculate total worked minutes
        $totalWorkedMinutes = $attendanceRecords->sum('total_minutes_worked');
        $totalWorkedHours = round($totalWorkedMinutes / 60, 2);

        // Assume standard monthly hours (e.g., 160 hours for full-time)
        $monthlyTargetHours = 160;
        $hoursRemaining = max(0, $monthlyTargetHours - $totalWorkedHours);
        $progressPercentage = min(100, round(($totalWorkedHours / $monthlyTargetHours) * 100));

        // Calculate today's worked time
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', Carbon::today())
            ->first();

        $workedToday = '00:00:00';
        $idleTimeToday = '0hr 0mins';

        if ($todayAttendance) {
            $clockIn = Carbon::parse($todayAttendance->clock_in);
            $clockOut = $todayAttendance->clock_out
                ? Carbon::parse($todayAttendance->clock_out)
                : Carbon::now();

            $totalSessionMinutes = $clockIn->diffInMinutes($clockOut);
            $actualWorkedMinutes = $todayAttendance->total_minutes_worked ?? $totalSessionMinutes;
            $idleMinutes = max(0, $totalSessionMinutes - $actualWorkedMinutes);

            $hours = floor($actualWorkedMinutes / 60);
            $minutes = $actualWorkedMinutes % 60;
            $seconds = 0;
            $workedToday = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $idleHours = floor($idleMinutes / 60);
            $idleMins = $idleMinutes % 60;
            $idleTimeToday = "{$idleHours}hr {$idleMins}mins";
        }

        return [
            'progressPercentage' => $progressPercentage,
            'hoursRemaining' => round($hoursRemaining, 1),
            'totalHours' => $monthlyTargetHours,
            'workedHours' => round($totalWorkedHours, 1),
            'workedToday' => $workedToday,
            'idleTimeToday' => $idleTimeToday,
        ];
    }

    /**
     * Get performance summary data
     */
    private function getPerformanceData($employee, $user, $startOfMonth, $endOfMonth, $startOfLastMonth, $endOfLastMonth)
    {
        // Get team IDs for this employee
        $teamIds = OptimizationTeamMember::where('employee_id', $employee->id)
            ->pluck('optimization_team_id')
            ->toArray();

        // Current month tasks
        $currentMonthTasks = Task::whereIn('assigned_team_id', $teamIds)
            ->whereBetween('scheduled_date', [$startOfMonth, $endOfMonth])
            ->get();

        $tasksCompleted = $currentMonthTasks->where('status', 'Completed')->count();
        $totalTasks = $currentMonthTasks->count();

        // Last month tasks for comparison
        $lastMonthTasks = Task::whereIn('assigned_team_id', $teamIds)
            ->whereBetween('scheduled_date', [$startOfLastMonth, $endOfLastMonth])
            ->get();

        $lastMonthCompleted = $lastMonthTasks->where('status', 'Completed')->count();
        $lastMonthTotal = $lastMonthTasks->count();

        // Calculate productivity change
        $currentProductivity = $totalTasks > 0 ? ($tasksCompleted / $totalTasks) * 100 : 0;
        $lastProductivity = $lastMonthTotal > 0 ? ($lastMonthCompleted / $lastMonthTotal) * 100 : 0;
        $productivityChange = round($currentProductivity - $lastProductivity, 1);

        // Training progress
        $totalVideos = TrainingVideo::active()->count();
        $watchedVideos = DB::table('employee_watched_videos')
            ->where('user_id', $user->id)
            ->count();

        $trainingProgress = $totalVideos > 0 ? round(($watchedVideos / $totalVideos) * 100) : 0;

        // Get categories watched
        $categories = TrainingVideo::getCategories();
        $categoryCount = count($categories);
        $categoriesWatched = 0;

        foreach ($categories as $key => $info) {
            $categoryVideos = TrainingVideo::active()->where('category', $key)->pluck('id');
            $watched = DB::table('employee_watched_videos')
                ->where('user_id', $user->id)
                ->whereIn('training_video_id', $categoryVideos)
                ->count();

            if ($watched >= $categoryVideos->count() && $categoryVideos->count() > 0) {
                $categoriesWatched++;
            }
        }

        // Attendance data
        $workDaysInMonth = $this->getWorkDaysInMonth($startOfMonth, $endOfMonth);
        $daysAttended = Attendance::where('employee_id', $employee->id)
            ->whereBetween('clock_in', [$startOfMonth, $endOfMonth])
            ->whereNotNull('clock_out')
            ->count();

        $attendanceRate = $workDaysInMonth > 0 ? round(($daysAttended / $workDaysInMonth) * 100) : 0;
        $absences = $workDaysInMonth - $daysAttended;

        // Tardiness (assuming late if clock in after 9 AM)
        $lateArrivals = Attendance::where('employee_id', $employee->id)
            ->whereBetween('clock_in', [$startOfMonth, $endOfMonth])
            ->whereTime('clock_in', '>', '09:00:00')
            ->count();

        return [
            'tasksCompleted' => $tasksCompleted,
            'totalTasks' => $totalTasks,
            'productivityChange' => ($productivityChange >= 0 ? '+' : '') . $productivityChange,
            'trainingProgress' => $trainingProgress,
            'coursesCompleted' => "{$categoriesWatched} / {$categoryCount}",
            'avgQuizScore' => $trainingProgress . '%', // Using training progress as proxy
            'attendanceRate' => $attendanceRate,
            'absences' => "{$absences} / {$workDaysInMonth}",
            'tardiness' => "{$lateArrivals} / {$daysAttended}",
        ];
    }

    /**
     * Get customer satisfaction data
     */
    private function getSatisfactionData($employee, $startOfMonth, $endOfMonth, $startOfLastMonth, $endOfLastMonth)
    {
        // Get team IDs for this employee
        $teamIds = OptimizationTeamMember::where('employee_id', $employee->id)
            ->pluck('optimization_team_id')
            ->toArray();

        // Get task IDs for this employee's teams
        $taskIds = Task::whereIn('assigned_team_id', $teamIds)->pluck('id')->toArray();

        // Current month reviews
        $currentMonthReviews = TaskReview::whereIn('task_id', $taskIds)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        // All-time reviews for this employee
        $allReviews = TaskReview::whereIn('task_id', $taskIds)->get();

        $avgRating = $allReviews->count() > 0 ? round($allReviews->avg('rating'), 1) : 0;

        // Last month reviews for comparison
        $lastMonthReviews = TaskReview::whereIn('task_id', $taskIds)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->get();

        $currentAvg = $currentMonthReviews->count() > 0 ? $currentMonthReviews->avg('rating') : $avgRating;
        $lastAvg = $lastMonthReviews->count() > 0 ? $lastMonthReviews->avg('rating') : $avgRating;
        $ratingChange = round($currentAvg - $lastAvg, 1);

        // Get ratings by service type (using task description keywords)
        $serviceRatings = $this->getServiceRatings($taskIds);

        return [
            'rating' => $avgRating,
            'change' => ($ratingChange >= 0 ? '+' : '') . $ratingChange,
            'totalReviews' => $allReviews->count(),
            'services' => $serviceRatings,
        ];
    }

    /**
     * Get service-specific ratings
     */
    private function getServiceRatings($taskIds)
    {
        $serviceTypes = [
            'Deep C.' => ['deep', 'thorough'],
            'Snow C.' => ['snow', 'winter'],
            'Daily C.' => ['daily', 'regular', 'routine'],
            'Student C.' => ['student'],
            'General' => [],
        ];

        $services = [];

        foreach ($serviceTypes as $name => $keywords) {
            $query = TaskReview::whereIn('task_id', $taskIds);

            if (!empty($keywords)) {
                $query->whereHas('task', function ($q) use ($keywords) {
                    $q->where(function ($subQ) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $subQ->orWhere('task_description', 'like', "%{$keyword}%");
                        }
                    });
                });
            }

            $reviews = $query->get();
            $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 0;

            $services[] = [
                'name' => $name,
                'rating' => $avgRating,
                'reviews' => $reviews->count() > 0 ? $reviews->count() : null,
                'highlighted' => $name === 'Daily C.',
            ];
        }

        return $services;
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($employee)
    {
        // Get team IDs for this employee
        $teamIds = OptimizationTeamMember::where('employee_id', $employee->id)
            ->pluck('optimization_team_id')
            ->toArray();

        $recentTasks = Task::whereIn('assigned_team_id', $teamIds)
            ->where('status', 'Completed')
            ->with(['location', 'location.contractedClient'])
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        return $recentTasks->map(function ($task) {
            $clientName = $task->location?->contractedClient?->name ?? 'Unknown Client';

            return [
                'id' => $task->id,
                'title' => $task->task_description ?? 'Cleaning Task',
                'date' => $task->completed_at
                    ? Carbon::parse($task->completed_at)->format('d M Y, g:i a')
                    : Carbon::parse($task->scheduled_date)->format('d M Y'),
                'client' => $clientName,
                'status' => $task->status,
                'location' => $task->location?->name,
            ];
        });
    }

    /**
     * Get chart data for the progress section
     */
    private function getChartData($employee)
    {
        $months = [];
        $workedHours = [];
        $idleHours = [];

        // Get data for last 3 months
        for ($i = 2; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $attendanceRecords = Attendance::where('employee_id', $employee->id)
                ->whereBetween('clock_in', [$startOfMonth, $endOfMonth])
                ->get();

            $totalWorkedMinutes = $attendanceRecords->sum('total_minutes_worked');
            $totalSessionMinutes = 0;

            foreach ($attendanceRecords as $record) {
                if ($record->clock_in && $record->clock_out) {
                    $clockIn = Carbon::parse($record->clock_in);
                    $clockOut = Carbon::parse($record->clock_out);
                    $totalSessionMinutes += $clockIn->diffInMinutes($clockOut);
                }
            }

            $months[] = $date->format('F');
            $workedHours[] = round($totalWorkedMinutes / 60, 1);
            $idleHours[] = round(max(0, $totalSessionMinutes - $totalWorkedMinutes) / 60, 1);
        }

        return [
            'months' => $months,
            'workedHours' => $workedHours,
            'idleHours' => $idleHours,
            'colors' => ['#22c55e', '#ef4444', '#3b82f6'],
        ];
    }

    /**
     * Get work days in a month (excluding weekends)
     */
    private function getWorkDaysInMonth($start, $end)
    {
        $workDays = 0;
        $current = $start->copy();

        while ($current <= $end && $current <= Carbon::now()) {
            if (!$current->isWeekend()) {
                $workDays++;
            }
            $current->addDay();
        }

        return $workDays;
    }
}
