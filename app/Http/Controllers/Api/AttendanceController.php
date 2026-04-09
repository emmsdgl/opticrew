<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CompanySettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\DayOff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Clock in
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|max:5120', // 5MB max
        ]);

        $user = $request->user();

        // Get employee record
        $employee = $user->employee;
        if (!$employee) {
            return response()->json([
                'message' => 'Employee record not found'
            ], 404);
        }

        // Check if already clocked in today (only one clock-in per day allowed)
        $today = Carbon::today();
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', $today)
            ->first();

        if ($existingAttendance) {
            $message = $existingAttendance->clock_out
                ? 'You have already clocked in and out today'
                : 'You are already clocked in';
            return response()->json([
                'message' => $message,
                'attendance' => $existingAttendance
            ], 400);
        }

        // SCENARIO #12: Clock-in blocked if employee has pending emergency leave for today
        // "Pending leave request requires manager review. Please contact Dispatch."
        $pendingEmergencyLeave = DayOff::where('employee_id', $employee->id)
            ->where('is_emergency', true)
            ->where('status', 'pending')
            ->where('date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->where('end_date', '>=', $today)
                  ->orWhereNull('end_date');
            })
            ->first();

        if ($pendingEmergencyLeave) {
            return response()->json([
                'message' => 'Clock-in blocked: Pending leave request requires manager review. Please contact Dispatch.',
                'error_code' => 'LEAVE_PENDING_REVIEW',
                'leave_request_id' => $pendingEmergencyLeave->id,
            ], 403);
        }

        // Also block if approved leave covers today
        $approvedLeave = DayOff::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->where('end_date', '>=', $today)
                  ->orWhereNull('end_date');
            })
            ->first();

        if ($approvedLeave) {
            return response()->json([
                'message' => 'Clock-in blocked: You have an approved leave for today.',
                'error_code' => 'ON_APPROVED_LEAVE',
                'leave_request_id' => $approvedLeave->id,
            ], 403);
        }

        // Server-side geofence enforcement (Dev Controls toggle: PH bypasses, FN enforces)
        $geoCheck = $this->enforceGeofence($employee->id, (float) $request->latitude, (float) $request->longitude);
        if ($geoCheck !== null) {
            return $geoCheck;
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance_photos', 'public');
        }

        // Create attendance record
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'clock_in' => now(),
            'clock_in_latitude' => $request->latitude,
            'clock_in_longitude' => $request->longitude,
            'clock_in_photo' => $photoPath,
            'status' => 'present',
        ]);

        return response()->json([
            'message' => 'Clocked in successfully',
            'attendance' => $attendance
        ], 201);
    }

    /**
     * Clock out
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'Employee record not found'
            ], 404);
        }

        // Find today's attendance record
        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', $today)
            ->whereNull('clock_out')
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'No active clock-in record found'
            ], 404);
        }

        // Server-side geofence enforcement (Dev Controls toggle: PH bypasses, FN enforces)
        $geoCheck = $this->enforceGeofence($employee->id, (float) $request->latitude, (float) $request->longitude);
        if ($geoCheck !== null) {
            return $geoCheck;
        }

        // Update attendance record
        $attendance->update([
            'clock_out' => now(),
            'clock_out_latitude' => $request->latitude,
            'clock_out_longitude' => $request->longitude,
        ]);

        // Calculate total hours and minutes worked
        $clockIn = Carbon::parse($attendance->clock_in);
        $clockOut = Carbon::parse($attendance->clock_out);
        $totalMinutes = $clockOut->diffInMinutes($clockIn, true);

        $attendance->total_minutes_worked = $totalMinutes;
        $attendance->hours_worked = round($totalMinutes / 60, 2);
        $attendance->save();

        return response()->json([
            'message' => 'Clocked out successfully',
            'attendance' => $attendance,
            'hours_worked' => $attendance->hours_worked
        ]);
    }

    /**
     * Get attendance history for current user
     */
    public function getHistory(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'Employee record not found'
            ], 404);
        }

        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('clock_in', 'desc')
            ->take(30) // Last 30 records
            ->get();

        return response()->json([
            'attendances' => $attendances
        ]);
    }

    /**
     * Get today's attendance status
     */
    public function getTodayStatus(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'Employee record not found',
                'is_clocked_in' => false
            ], 200);
        }

        $today = Carbon::today();

        // First check for active (not clocked out) record
        $activeAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', $today)
            ->whereNull('clock_out')
            ->first();

        if ($activeAttendance) {
            return response()->json([
                'is_clocked_in' => true,
                'attendance' => $activeAttendance
            ]);
        }

        // If no active record, get the most recent completed record for today
        $completedAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', $today)
            ->whereNotNull('clock_out')
            ->orderBy('clock_out', 'desc')
            ->first();

        return response()->json([
            'is_clocked_in' => false,
            'attendance' => $completedAttendance
        ]);
    }

    /**
     * Enforce geofence based on the global Dev Controls toggle.
     *
     * - geofence_test_mode = PH  → bypass entirely (returns null)
     * - geofence_test_mode = FN  → compute haversine distance from today's
     *   assigned task's contracted-client coordinates and reject if outside the radius.
     *
     * Returns null when the action should proceed, or a JsonResponse to return on failure.
     */
    private function enforceGeofence(int $employeeId, float $lat, float $lng)
    {
        $mode = strtoupper((string) CompanySettingService::get('geofence_test_mode', 'PH'));

        // PH = test/demo mode → never block server-side
        if ($mode !== 'FN') {
            return null;
        }

        $radiusSetting = DB::table('company_settings')->where('key', 'geofence_radius')->first();
        $radius = $radiusSetting ? (float) $radiusSetting->value : 100.0;

        $today = now()->format('Y-m-d');

        // Find today's task for this employee with contracted-client coordinates
        $task = DB::table('tasks')
            ->join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
            ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
            ->leftJoin('locations', 'tasks.location_id', '=', 'locations.id')
            ->leftJoin('contracted_clients', 'locations.contracted_client_id', '=', 'contracted_clients.id')
            ->where('optimization_team_members.employee_id', $employeeId)
            ->where('tasks.scheduled_date', $today)
            ->whereNull('tasks.deleted_at')
            ->whereNotNull('contracted_clients.latitude')
            ->whereNotNull('contracted_clients.longitude')
            ->select('contracted_clients.latitude', 'contracted_clients.longitude', 'contracted_clients.name as client_name')
            ->first();

        if (!$task) {
            return response()->json([
                'message' => 'Geofence (FN mode): No task with a registered location is assigned to you today.',
                'error_code' => 'GEOFENCE_NO_TASK_LOCATION',
            ], 403);
        }

        $distance = $this->haversineMeters($lat, $lng, (float) $task->latitude, (float) $task->longitude);

        if ($distance > $radius) {
            return response()->json([
                'message' => 'You are outside the allowed clock-in area for ' . $task->client_name . '.',
                'error_code' => 'OUT_OF_GEOFENCE',
                'distance_meters' => (int) round($distance),
                'allowed_radius_meters' => (int) $radius,
            ], 403);
        }

        return null;
    }

    /**
     * Haversine distance in meters between two lat/lng points.
     */
    private function haversineMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000.0; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
