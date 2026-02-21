<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientAppointment;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Feedback;
use App\Models\UserCourseProgress;
use App\Models\User;
use App\Models\Task;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Show client reports page
     */
    public function clientReports(Request $request)
    {
        // Get date range from request or default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // === PART 1: Get Personal/External Clients (from client_appointments) ===
        $personalClients = Client::select('clients.*')
            ->selectRaw('COUNT(DISTINCT client_appointments.id) as total_appointments')
            ->selectRaw('SUM(CASE WHEN client_appointments.status = "completed" THEN client_appointments.total_amount ELSE 0 END) as total_revenue')
            ->selectRaw('SUM(CASE WHEN client_appointments.status = "pending" THEN 1 ELSE 0 END) as pending_appointments')
            ->selectRaw('SUM(CASE WHEN client_appointments.status = "approved" THEN 1 ELSE 0 END) as approved_appointments')
            ->selectRaw('SUM(CASE WHEN client_appointments.status = "completed" THEN 1 ELSE 0 END) as completed_appointments')
            ->selectRaw('"personal" as data_source')
            ->leftJoin('client_appointments', 'clients.id', '=', 'client_appointments.client_id')
            ->whereBetween('client_appointments.service_date', [$startDate, $endDate])
            ->groupBy('clients.id', 'clients.user_id', 'clients.first_name', 'clients.last_name', 'clients.middle_initial',
                'clients.birthdate', 'clients.security_question_1', 'clients.security_answer_1',
                'clients.security_question_2', 'clients.security_answer_2', 'clients.created_at',
                'clients.updated_at', 'clients.client_type', 'clients.company_name', 'clients.email',
                'clients.phone_number', 'clients.business_id', 'clients.street_address', 'clients.postal_code',
                'clients.city', 'clients.district', 'clients.address', 'clients.billing_address',
                'clients.einvoice_number', 'clients.is_active', 'clients.deleted_at')
            ->get();

        // === PART 2: Get Contracted Clients (from tasks) ===
        // Get holidays first
        $holidays = DB::table('holidays')
            ->whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->toArray();

        $contractedClients = DB::table('contracted_clients as cc')
            ->select([
                'cc.id',
                'cc.name as company_name',
                DB::raw('"contracted" as client_type'),
                DB::raw('"contracted" as data_source'),
                DB::raw('NULL as user_id'),
                DB::raw('NULL as email'),
                DB::raw('NULL as phone_number'),
                DB::raw('COUNT(t.id) as total_appointments'),
                DB::raw('SUM(CASE WHEN t.status = "Pending" THEN 1 ELSE 0 END) as pending_appointments'),
                DB::raw('SUM(CASE WHEN t.status = "Scheduled" THEN 1 ELSE 0 END) as approved_appointments'),
                DB::raw('SUM(CASE WHEN t.status = "Completed" THEN 1 ELSE 0 END) as completed_appointments'),
                // Calculate revenue using rate type (Normal or Student) and day type - ONLY for Completed tasks
                DB::raw('ROUND(SUM(
                    CASE
                        -- Skip if no task or task not completed
                        WHEN t.id IS NULL OR t.status != "Completed" THEN 0
                        -- Student Rate on Weekdays
                        WHEN t.rate_type = "Student" AND DAYOFWEEK(t.scheduled_date) != 1 AND t.scheduled_date NOT IN (' .
                            (count($holidays) > 0 ? "'" . implode("','", $holidays) . "'" : "''") . ')
                        THEN l.student_rate
                        -- Student Rate on Sunday/Holiday
                        WHEN t.rate_type = "Student" AND (DAYOFWEEK(t.scheduled_date) = 1 OR t.scheduled_date IN (' .
                            (count($holidays) > 0 ? "'" . implode("','", $holidays) . "'" : "''") . '))
                        THEN l.student_sunday_holiday_rate
                        -- Normal Rate on Weekdays
                        WHEN DAYOFWEEK(t.scheduled_date) != 1 AND t.scheduled_date NOT IN (' .
                            (count($holidays) > 0 ? "'" . implode("','", $holidays) . "'" : "''") . ')
                        THEN l.normal_rate_per_hour
                        -- Normal Rate on Sunday/Holiday
                        ELSE l.sunday_holiday_rate
                    END
                ), 2) as total_revenue')
            ])
            ->leftJoin('locations as l', 'cc.id', '=', 'l.contracted_client_id')
            ->leftJoin('tasks as t', function($join) use ($startDate, $endDate) {
                $join->on('l.id', '=', 't.location_id')
                     ->whereBetween('t.scheduled_date', [$startDate, $endDate])
                     ->whereNull('t.deleted_at');
            })
            ->whereNull('cc.deleted_at')
            ->groupBy('cc.id', 'cc.name')
            ->having('total_appointments', '>', 0)
            ->get();

        // === PART 3: Combine both data sources ===
        $clients = $personalClients->concat($contractedClients)->sortByDesc('total_revenue');

        // Calculate totals
        $totalRevenue = $clients->sum('total_revenue');
        $totalAppointments = $clients->sum('total_appointments');
        $totalClients = $clients->count();

        // === PART 4: Get service breakdown (from both sources) ===
        // Service breakdown from appointments
        $appointmentServices = ClientAppointment::select('service_type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total_amount) as revenue')
            ->whereBetween('service_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->groupBy('service_type')
            ->get();

        // Task breakdown from contracted clients (using rate type and day type)
        $taskServices = DB::table('tasks as t')
            ->select([
                DB::raw('CONCAT("Task - ", cc.name) as service_type'),
                DB::raw('COUNT(t.id) as count'),
                DB::raw('ROUND(SUM(
                    CASE
                        -- Student Rate on Weekdays
                        WHEN t.rate_type = "Student" AND DAYOFWEEK(t.scheduled_date) != 1 AND t.scheduled_date NOT IN (' .
                            (count($holidays) > 0 ? "'" . implode("','", $holidays) . "'" : "''") . ')
                        THEN l.student_rate
                        -- Student Rate on Sunday/Holiday
                        WHEN t.rate_type = "Student" AND (DAYOFWEEK(t.scheduled_date) = 1 OR t.scheduled_date IN (' .
                            (count($holidays) > 0 ? "'" . implode("','", $holidays) . "'" : "''") . '))
                        THEN l.student_sunday_holiday_rate
                        -- Normal Rate on Weekdays
                        WHEN DAYOFWEEK(t.scheduled_date) != 1 AND t.scheduled_date NOT IN (' .
                            (count($holidays) > 0 ? "'" . implode("','", $holidays) . "'" : "''") . ')
                        THEN l.normal_rate_per_hour
                        -- Normal Rate on Sunday/Holiday
                        ELSE l.sunday_holiday_rate
                    END
                ), 2) as revenue')
            ])
            ->join('locations as l', 't.location_id', '=', 'l.id')
            ->join('contracted_clients as cc', 'l.contracted_client_id', '=', 'cc.id')
            ->whereBetween('t.scheduled_date', [$startDate, $endDate])
            ->where('t.status', 'Completed')
            ->whereNull('t.deleted_at')
            ->groupBy('cc.id', 'cc.name')
            ->get();

        $serviceBreakdown = $appointmentServices->concat($taskServices)->sortByDesc('revenue');

        return view('admin.reports.client-reports', compact(
            'clients',
            'totalRevenue',
            'totalAppointments',
            'totalClients',
            'serviceBreakdown',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show individual client report
     */
    public function clientDetail(Request $request, $clientId)
    {
        $client = Client::with('user')->findOrFail($clientId);

        // Get date range
        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Get all appointments for this client
        $appointments = ClientAppointment::where('client_id', $clientId)
            ->whereBetween('service_date', [$startDate, $endDate])
            ->orderBy('service_date', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_spent' => $appointments->where('status', 'completed')->sum('total_amount'),
            'total_appointments' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'approved' => $appointments->where('status', 'approved')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
        ];

        return view('admin.reports.client-detail', compact(
            'client',
            'appointments',
            'stats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show employee payroll reports page
     */
    public function employeePayroll(Request $request)
    {
        // Get date range from request or default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Get holidays in the date range
        $holidays = DB::table('holidays')
            ->whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->toArray();

        // Build holiday condition for SQL
        $holidayCondition = count($holidays) > 0
            ? "DATE(attendances.clock_in) IN ('" . implode("','", $holidays) . "')"
            : "0";

        // Get all employees with their attendance and salary calculations
        $employees = Employee::select('employees.*', 'users.name', 'users.email')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->selectRaw('
                (SELECT COUNT(*)
                 FROM attendances
                 WHERE attendances.employee_id = employees.id
                 AND DATE(attendances.clock_in) BETWEEN ? AND ?) as days_worked
            ', [$startDate, $endDate])
            // Calculate regular hours (not Sunday, not Holiday)
            ->selectRaw("
                (SELECT SUM(total_minutes_worked)
                 FROM attendances
                 WHERE attendances.employee_id = employees.id
                 AND DATE(attendances.clock_in) BETWEEN ? AND ?
                 AND DAYOFWEEK(attendances.clock_in) != 1
                 AND NOT ({$holidayCondition})) as regular_minutes
            ", [$startDate, $endDate])
            // Calculate premium hours (Sunday OR Holiday)
            ->selectRaw("
                (SELECT SUM(total_minutes_worked)
                 FROM attendances
                 WHERE attendances.employee_id = employees.id
                 AND DATE(attendances.clock_in) BETWEEN ? AND ?
                 AND (DAYOFWEEK(attendances.clock_in) = 1 OR {$holidayCondition})) as premium_minutes
            ", [$startDate, $endDate])
            ->whereNull('employees.deleted_at')
            ->get()
            ->map(function ($employee) {
                // Calculate regular hours and pay
                $employee->regular_hours = $employee->regular_minutes ? round($employee->regular_minutes / 60, 2) : 0;
                $employee->regular_pay = $employee->regular_hours * $employee->salary_per_hour;

                // Calculate premium hours and pay (double rate)
                $employee->premium_hours = $employee->premium_minutes ? round($employee->premium_minutes / 60, 2) : 0;
                $employee->premium_pay = $employee->premium_hours * ($employee->salary_per_hour * 2);

                // Calculate totals
                $employee->total_hours = $employee->regular_hours + $employee->premium_hours;
                $employee->gross_salary = $employee->regular_pay + $employee->premium_pay;

                return $employee;
            });

        // Calculate totals
        $totalHours = $employees->sum('total_hours');
        $totalRegularHours = $employees->sum('regular_hours');
        $totalPremiumHours = $employees->sum('premium_hours');
        $totalSalary = $employees->sum('gross_salary');
        $totalEmployees = $employees->count();
        $averageHoursPerEmployee = $totalEmployees > 0 ? round($totalHours / $totalEmployees, 2) : 0;

        return view('admin.reports.employee-payroll', compact(
            'employees',
            'totalHours',
            'totalRegularHours',
            'totalPremiumHours',
            'totalSalary',
            'totalEmployees',
            'averageHoursPerEmployee',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show individual employee payroll detail
     */
    public function employeeDetail(Request $request, $employeeId)
    {
        $employee = Employee::with('user')->findOrFail($employeeId);

        // Get date range
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Get holidays in the date range
        $holidays = DB::table('holidays')
            ->whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // Get all attendance records
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereDate('clock_in', '>=', $startDate)
            ->whereDate('clock_in', '<=', $endDate)
            ->orderBy('clock_in', 'desc')
            ->get()
            ->map(function ($attendance) use ($employee, $holidays) {
                $attendance->hours_worked = $attendance->total_minutes_worked ? round($attendance->total_minutes_worked / 60, 2) : 0;

                // Check if this day is Sunday (0) or Holiday
                $clockInDate = Carbon::parse($attendance->clock_in);
                $dateStr = $clockInDate->format('Y-m-d');
                $isSunday = $clockInDate->dayOfWeek === 0; // Carbon: 0 = Sunday
                $isHoliday = in_array($dateStr, $holidays);

                $attendance->is_premium_day = $isSunday || $isHoliday;
                $attendance->day_type = $isSunday ? 'Sunday' : ($isHoliday ? 'Holiday' : 'Regular');

                // Calculate pay with premium rate if applicable
                $hourlyRate = $attendance->is_premium_day
                    ? ($employee->salary_per_hour * 2)
                    : $employee->salary_per_hour;

                $attendance->hourly_rate = $hourlyRate;
                $attendance->daily_pay = $attendance->hours_worked * $hourlyRate;

                return $attendance;
            });

        // Calculate statistics with regular vs premium breakdown
        $regularAttendances = $attendances->where('is_premium_day', false);
        $premiumAttendances = $attendances->where('is_premium_day', true);

        $regularMinutes = $regularAttendances->sum('total_minutes_worked');
        $premiumMinutes = $premiumAttendances->sum('total_minutes_worked');

        $regularHours = round($regularMinutes / 60, 2);
        $premiumHours = round($premiumMinutes / 60, 2);

        $regularPay = $regularHours * $employee->salary_per_hour;
        $premiumPay = $premiumHours * ($employee->salary_per_hour * 2);

        $stats = [
            'total_days' => $attendances->count(),
            'regular_days' => $regularAttendances->count(),
            'premium_days' => $premiumAttendances->count(),
            'total_minutes' => $attendances->sum('total_minutes_worked'),
            'total_hours' => round($attendances->sum('total_minutes_worked') / 60, 2),
            'regular_hours' => $regularHours,
            'premium_hours' => $premiumHours,
            'regular_pay' => $regularPay,
            'premium_pay' => $premiumPay,
            'total_salary' => $attendances->sum('daily_pay'),
            'average_hours_per_day' => $attendances->count() > 0
                ? round($attendances->sum('total_minutes_worked') / 60 / $attendances->count(), 2)
                : 0,
        ];

        // Get daily breakdown by date
        $dailyBreakdown = $attendances->groupBy(function ($attendance) {
            return Carbon::parse($attendance->clock_in)->format('Y-m-d');
        })->map(function ($dayAttendances) use ($employee, $holidays) {
            $totalMinutes = $dayAttendances->sum('total_minutes_worked');
            $hours = round($totalMinutes / 60, 2);
            $firstAttendance = $dayAttendances->first();

            return [
                'date' => $firstAttendance->clock_in,
                'day_type' => $firstAttendance->day_type,
                'is_premium_day' => $firstAttendance->is_premium_day,
                'shifts' => $dayAttendances->count(),
                'total_hours' => $hours,
                'hourly_rate' => $firstAttendance->hourly_rate,
                'daily_pay' => $hours * $firstAttendance->hourly_rate,
            ];
        });

        // Course progress data
        $courseInfo = [
            1 => ['title' => 'Deep Cleaning Fundamentals', 'duration' => '8 lectures • 1.5 hours'],
            2 => ['title' => 'Professional Window Cleaning', 'duration' => '12 lectures • 2 hours'],
            3 => ['title' => 'Eco-Friendly Cleaning Solutions', 'duration' => '10 lectures • 1.5 hours'],
            4 => ['title' => 'Industrial Floor Care & Maintenance', 'duration' => '15 lectures • 3 hours'],
            5 => ['title' => 'Sanitization & Disinfection Protocols', 'duration' => '14 lectures • 2.5 hours'],
        ];

        $courseProgressRecords = UserCourseProgress::where('user_id', $employee->user->id)->get()->keyBy('course_id');

        $courses = collect($courseInfo)->map(function ($info, $courseId) use ($courseProgressRecords) {
            $progress = $courseProgressRecords->get($courseId);
            return [
                'id' => $courseId,
                'title' => $info['title'],
                'duration' => $info['duration'],
                'progress' => $progress ? $progress->progress : 0,
                'status' => $progress ? $progress->status : 'pending',
                'updated_at' => $progress ? $progress->updated_at : null,
            ];
        });

        return view('admin.reports.employee-detail', compact(
            'employee',
            'attendances',
            'stats',
            'dailyBreakdown',
            'startDate',
            'endDate',
            'courses'
        ));
    }

    /**
     * Export client report to CSV with tally format and proper pricing
     */
    public function exportClientReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $timestamp = now()->format('Ymd_His');
        $filename = "client_report_detailed_{$startDate}_to_{$endDate}_{$timestamp}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // === SECTION 1: SUMMARY HEADER ===
            fputcsv($file, ['CLIENT REVENUE REPORT']);
            fputcsv($file, ['Period:', $startDate . ' to ' . $endDate]);
            fputcsv($file, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []); // Empty line

            // Get holidays in the date range
            $holidays = DB::table('holidays')
                ->whereBetween('date', [$startDate, $endDate])
                ->pluck('date')
                ->toArray();

            // === SECTION 2: CONTRACTED CLIENTS WITH TALLY BY CABIN TYPE ===
            $contractedClients = DB::table('contracted_clients as cc')
                ->select('cc.id', 'cc.name')
                ->whereNull('cc.deleted_at')
                ->get();

            foreach ($contractedClients as $contractedClient) {
                // Get tasks grouped by cabin type with pricing (including student rates)
                $cabinTallies = DB::table('tasks as t')
                    ->select([
                        'l.location_type as cabin_type',
                        'l.base_cleaning_duration_minutes',
                        'l.normal_rate_per_hour',
                        'l.sunday_holiday_rate',
                        'l.student_rate',
                        'l.student_sunday_holiday_rate',
                        // Normal rate - Regular days
                        DB::raw('COUNT(CASE WHEN t.rate_type = "Normal" AND DAYOFWEEK(t.scheduled_date) != 1 AND t.scheduled_date NOT IN (' .
                            (count($holidays) > 0 ? "'" . implode("','", $holidays) . "'" : "''") .
                            ') THEN 1 END) as normal_regular_count'),
                        // Normal rate - Sunday/Holiday
                        DB::raw('COUNT(CASE WHEN t.rate_type = "Normal" AND (DAYOFWEEK(t.scheduled_date) = 1 OR t.scheduled_date IN (' .
                            (count($holidays) > 0 ? "'" . implode("','", $holidays) . "'" : "''") .
                            ')) THEN 1 END) as normal_sunday_holiday_count'),
                        // Student rate - Regular days
                        DB::raw('COUNT(CASE WHEN t.rate_type = "Student" AND DAYOFWEEK(t.scheduled_date) != 1 AND t.scheduled_date NOT IN (' .
                            (count($holidays) > 0 ? "'" . implode("','", $holidays) . "'" : "''") .
                            ') THEN 1 END) as student_regular_count'),
                        // Student rate - Sunday/Holiday
                        DB::raw('COUNT(CASE WHEN t.rate_type = "Student" AND (DAYOFWEEK(t.scheduled_date) = 1 OR t.scheduled_date IN (' .
                            (count($holidays) > 0 ? "'" . implode("','", $holidays) . "'" : "''") .
                            ')) THEN 1 END) as student_sunday_holiday_count')
                    ])
                    ->join('locations as l', 't.location_id', '=', 'l.id')
                    ->join('contracted_clients as cc', 'l.contracted_client_id', '=', 'cc.id')
                    ->where('cc.id', $contractedClient->id)
                    ->whereBetween('t.scheduled_date', [$startDate, $endDate])
                    ->whereNull('t.deleted_at')
                    ->groupBy('l.location_type', 'l.base_cleaning_duration_minutes', 'l.normal_rate_per_hour',
                              'l.sunday_holiday_rate', 'l.student_rate', 'l.student_sunday_holiday_rate')
                    ->get();

                if ($cabinTallies->count() > 0) {
                    // Company header
                    fputcsv($file, ['=== ' . strtoupper($contractedClient->name) . ' ===']);
                    fputcsv($file, []); // Empty line

                    // Tally table headers with student rate breakdown
                    fputcsv($file, [
                        'Cabin Type',
                        'Normal Regular Count',
                        'Normal Regular Revenue (€)',
                        'Normal Sunday/Holiday Count',
                        'Normal Sunday/Holiday Revenue (€)',
                        'Student Regular Count',
                        'Student Regular Revenue (€)',
                        'Student Sunday/Holiday Count',
                        'Student Sunday/Holiday Revenue (€)',
                        'Total Tasks',
                        'Total Revenue (€)'
                    ]);

                    $grandTotal = 0;
                    $totalNormalRegularCount = 0;
                    $totalNormalSundayCount = 0;
                    $totalStudentRegularCount = 0;
                    $totalStudentSundayCount = 0;

                    // Cabin type tallies
                    foreach ($cabinTallies as $tally) {
                        // Calculate revenue for each rate type
                        $normalRegularRevenue = $tally->normal_regular_count * $tally->normal_rate_per_hour;
                        $normalSundayRevenue = $tally->normal_sunday_holiday_count * $tally->sunday_holiday_rate;
                        $studentRegularRevenue = $tally->student_regular_count * $tally->student_rate;
                        $studentSundayRevenue = $tally->student_sunday_holiday_count * $tally->student_sunday_holiday_rate;

                        $totalRevenue = $normalRegularRevenue + $normalSundayRevenue + $studentRegularRevenue + $studentSundayRevenue;
                        $totalTasks = $tally->normal_regular_count + $tally->normal_sunday_holiday_count +
                                     $tally->student_regular_count + $tally->student_sunday_holiday_count;

                        $grandTotal += $totalRevenue;
                        $totalNormalRegularCount += $tally->normal_regular_count;
                        $totalNormalSundayCount += $tally->normal_sunday_holiday_count;
                        $totalStudentRegularCount += $tally->student_regular_count;
                        $totalStudentSundayCount += $tally->student_sunday_holiday_count;

                        fputcsv($file, [
                            $tally->cabin_type . ' - ' . $totalTasks,
                            $tally->normal_regular_count,
                            number_format($normalRegularRevenue, 2),
                            $tally->normal_sunday_holiday_count,
                            number_format($normalSundayRevenue, 2),
                            $tally->student_regular_count,
                            number_format($studentRegularRevenue, 2),
                            $tally->student_sunday_holiday_count,
                            number_format($studentSundayRevenue, 2),
                            $totalTasks,
                            number_format($totalRevenue, 2)
                        ]);
                    }

                    // Summary for this company
                    fputcsv($file, []); // Empty line
                    fputcsv($file, [
                        'TOTAL',
                        $totalNormalRegularCount,
                        '',
                        $totalNormalSundayCount,
                        '',
                        $totalStudentRegularCount,
                        '',
                        $totalStudentSundayCount,
                        '',
                        $totalNormalRegularCount + $totalNormalSundayCount + $totalStudentRegularCount + $totalStudentSundayCount,
                        '€' . number_format($grandTotal, 2)
                    ]);
                    fputcsv($file, []); // Empty line

                    // Rate information
                    fputcsv($file, ['Pricing Details:']);
                    foreach ($cabinTallies as $tally) {
                        fputcsv($file, [
                            $tally->cabin_type,
                            'Normal Regular Price: €' . number_format($tally->normal_rate_per_hour, 2) . ' per cleaning',
                            'Normal Sunday/Holiday Price: €' . number_format($tally->sunday_holiday_rate, 2) . ' per cleaning',
                            'Student Regular Price: €' . number_format($tally->student_rate, 2) . ' per cleaning',
                            'Student Sunday/Holiday Price: €' . number_format($tally->student_sunday_holiday_rate, 2) . ' per cleaning'
                        ]);
                    }

                    fputcsv($file, []); // Empty line
                    fputcsv($file, []); // Empty line
                }
            }

            // === SECTION 3: PERSONAL/EXTERNAL CLIENTS SUMMARY ===
            $personalClients = Client::select('clients.*')
                ->selectRaw('COUNT(DISTINCT client_appointments.id) as total_appointments')
                ->selectRaw('SUM(CASE WHEN client_appointments.status = "completed" THEN client_appointments.total_amount ELSE 0 END) as total_revenue')
                ->leftJoin('client_appointments', 'clients.id', '=', 'client_appointments.client_id')
                ->whereBetween('client_appointments.service_date', [$startDate, $endDate])
                ->groupBy('clients.id', 'clients.user_id', 'clients.first_name', 'clients.last_name', 'clients.middle_initial',
                    'clients.birthdate', 'clients.security_question_1', 'clients.security_answer_1',
                    'clients.security_question_2', 'clients.security_answer_2', 'clients.created_at',
                    'clients.updated_at', 'clients.client_type', 'clients.company_name', 'clients.email',
                    'clients.phone_number', 'clients.business_id', 'clients.street_address', 'clients.postal_code',
                    'clients.city', 'clients.district', 'clients.address', 'clients.billing_address',
                    'clients.einvoice_number', 'clients.is_active', 'clients.deleted_at')
                ->having('total_appointments', '>', 0)
                ->get();

            if ($personalClients->count() > 0) {
                fputcsv($file, ['=== PERSONAL/EXTERNAL CLIENTS ===']);
                fputcsv($file, []); // Empty line

                fputcsv($file, ['Client Name', 'Email', 'Phone', 'Type', 'Total Appointments', 'Total Revenue (€)']);

                foreach ($personalClients as $client) {
                    fputcsv($file, [
                        $client->company_name ?: $client->full_name,
                        $client->email ?: ($client->user ? $client->user->email : 'N/A'),
                        $client->phone_number ?: ($client->user ? $client->user->phone : 'N/A'),
                        ucfirst($client->client_type),
                        $client->total_appointments,
                        number_format($client->total_revenue, 2),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export employee payroll report to CSV
     */
    public function exportPayrollReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Get holidays in the date range
        $holidays = DB::table('holidays')
            ->whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->toArray();

        // Build holiday condition for SQL
        $holidayCondition = count($holidays) > 0
            ? "DATE(attendances.clock_in) IN ('" . implode("','", $holidays) . "')"
            : "0";

        $employees = Employee::select('employees.*', 'users.name', 'users.email')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->selectRaw('
                (SELECT COUNT(*)
                 FROM attendances
                 WHERE attendances.employee_id = employees.id
                 AND DATE(attendances.clock_in) BETWEEN ? AND ?) as days_worked
            ', [$startDate, $endDate])
            // Calculate regular hours (not Sunday, not Holiday)
            ->selectRaw("
                (SELECT SUM(total_minutes_worked)
                 FROM attendances
                 WHERE attendances.employee_id = employees.id
                 AND DATE(attendances.clock_in) BETWEEN ? AND ?
                 AND DAYOFWEEK(attendances.clock_in) != 1
                 AND NOT ({$holidayCondition})) as regular_minutes
            ", [$startDate, $endDate])
            // Calculate premium hours (Sunday OR Holiday)
            ->selectRaw("
                (SELECT SUM(total_minutes_worked)
                 FROM attendances
                 WHERE attendances.employee_id = employees.id
                 AND DATE(attendances.clock_in) BETWEEN ? AND ?
                 AND (DAYOFWEEK(attendances.clock_in) = 1 OR {$holidayCondition})) as premium_minutes
            ", [$startDate, $endDate])
            ->whereNull('employees.deleted_at')
            ->get()
            ->map(function ($employee) {
                // Calculate regular hours and pay
                $employee->regular_hours = $employee->regular_minutes ? round($employee->regular_minutes / 60, 2) : 0;
                $employee->regular_pay = $employee->regular_hours * $employee->salary_per_hour;

                // Calculate premium hours and pay (double rate)
                $employee->premium_hours = $employee->premium_minutes ? round($employee->premium_minutes / 60, 2) : 0;
                $employee->premium_rate = $employee->salary_per_hour * 2;
                $employee->premium_pay = $employee->premium_hours * $employee->premium_rate;

                // Calculate totals
                $employee->total_hours = $employee->regular_hours + $employee->premium_hours;
                $employee->gross_salary = $employee->regular_pay + $employee->premium_pay;

                return $employee;
            });

        $timestamp = now()->format('Ymd_His');
        $filename = "payroll_report_{$startDate}_to_{$endDate}_{$timestamp}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($employees, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Report header
            fputcsv($file, ['EMPLOYEE PAYROLL REPORT']);
            fputcsv($file, ['Period:', $startDate . ' to ' . $endDate]);
            fputcsv($file, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []); // Empty line

            // Column headers with regular and Sunday/Holiday breakdown
            fputcsv($file, [
                'Employee Name',
                'Email',
                'Regular Rate (€/hr)',
                'Sunday/Holiday Rate (€/hr)',
                'Days Worked',
                'Regular Hours',
                'Regular Pay (€)',
                'Sunday/Holiday Hours',
                'Sunday/Holiday Pay (€)',
                'Total Hours',
                'Gross Salary (€)'
            ]);

            // Add data
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->name,
                    $employee->email,
                    number_format($employee->salary_per_hour, 2),
                    number_format($employee->premium_rate, 2),
                    $employee->days_worked ?: 0,
                    number_format($employee->regular_hours, 2),
                    number_format($employee->regular_pay, 2),
                    number_format($employee->premium_hours, 2),
                    number_format($employee->premium_pay, 2),
                    number_format($employee->total_hours, 2),
                    number_format($employee->gross_salary, 2),
                ]);
            }

            // Add summary
            fputcsv($file, []); // Empty line
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Employees:', $employees->count()]);
            fputcsv($file, ['Total Regular Hours:', number_format($employees->sum('regular_hours'), 2)]);
            fputcsv($file, ['Total Sunday/Holiday Hours:', number_format($employees->sum('premium_hours'), 2)]);
            fputcsv($file, ['Total Hours:', number_format($employees->sum('total_hours'), 2)]);
            fputcsv($file, ['Total Payroll:', '€' . number_format($employees->sum('gross_salary'), 2)]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show service performance report with client and employee feedback
     */
    public function servicePerformance(Request $request)
    {
        // Date range filtering
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Client feedback (where client_id is set)
        $clientFeedback = Feedback::with(['client', 'appointment'])
            ->whereNotNull('client_id')
            ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();

        // Employee feedback (where employee_id is set)
        $employeeFeedback = Feedback::with(['employee', 'task'])
            ->whereNotNull('employee_id')
            ->whereBetween('created_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();

        // Summary stats
        $totalFeedback = $clientFeedback->count() + $employeeFeedback->count();

        $clientAvgRating = $clientFeedback->avg(fn ($f) => $f->rating ?? $f->overall_rating) ?: 0;
        $employeeAvgRating = $employeeFeedback->avg(fn ($f) => $f->rating ?? $f->overall_rating) ?: 0;
        $overallAvgRating = $totalFeedback > 0
            ? ($clientFeedback->merge($employeeFeedback)->avg(fn ($f) => $f->rating ?? $f->overall_rating) ?: 0)
            : 0;

        $recommendRate = $clientFeedback->count() > 0
            ? round(($clientFeedback->where('would_recommend', true)->count() / $clientFeedback->count()) * 100, 1)
            : 0;

        return view('admin.reports.service-performance', compact(
            'startDate',
            'endDate',
            'clientFeedback',
            'employeeFeedback',
            'totalFeedback',
            'clientAvgRating',
            'employeeAvgRating',
            'overallAvgRating',
            'recommendRate'
        ));
    }
}
