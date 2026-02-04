<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DayOff;
use App\Models\Employee;
use App\Models\Attendance;
use App\Services\PushNotificationService;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    protected $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }
    /**
     * Get all leave requests for employee (their own requests)
     */
    public function getEmployeeRequests(Request $request)
    {
        try {
            $user = $request->user();
            $employee = $user->employee;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found'
                ], 404);
            }

            $leaveRequests = DayOff::where('employee_id', $employee->id)
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($leave) {
                    return [
                        'id' => $leave->id,
                        'date' => $leave->date->format('Y-m-d'),
                        'end_date' => $leave->end_date ? $leave->end_date->format('Y-m-d') : null,
                        'reason' => $leave->reason,
                        'type' => $leave->type,
                        'status' => $leave->status,
                        'duration_days' => $leave->duration_days,
                        'admin_notes' => $leave->admin_notes,
                        'approved_at' => $leave->approved_at ? $leave->approved_at->format('Y-m-d H:i:s') : null,
                        'created_at' => $leave->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'leave_requests' => $leaveRequests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch leave requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit a new leave request (Employee)
     */
    public function submitRequest(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'end_date' => 'nullable|date|after_or_equal:date',
                'reason' => 'required|string|max:500',
                'type' => 'required|in:vacation,sick,personal,other',
            ]);

            $user = $request->user();
            $employee = $user->employee;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found'
                ], 404);
            }

            // Check for overlapping leave requests
            $startDate = Carbon::parse($request->date);
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : $startDate;

            $existingRequest = DayOff::where('employee_id', $employee->id)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('date', '<=', $startDate)
                              ->where(function ($qq) use ($endDate) {
                                  $qq->where('end_date', '>=', $endDate)
                                     ->orWhereNull('end_date');
                              });
                        });
                })
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a leave request for this date range'
                ], 400);
            }

            $leaveRequest = DayOff::create([
                'employee_id' => $employee->id,
                'date' => $request->date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'type' => $request->type,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave request submitted successfully',
                'leave_request' => [
                    'id' => $leaveRequest->id,
                    'date' => $leaveRequest->date->format('Y-m-d'),
                    'end_date' => $leaveRequest->end_date ? $leaveRequest->end_date->format('Y-m-d') : null,
                    'reason' => $leaveRequest->reason,
                    'type' => $leaveRequest->type,
                    'status' => $leaveRequest->status,
                    'duration_days' => $leaveRequest->duration_days,
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit leave request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a leave request (Employee - only pending requests)
     */
    public function cancelRequest(Request $request, $requestId)
    {
        try {
            $user = $request->user();
            $employee = $user->employee;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found'
                ], 404);
            }

            $leaveRequest = DayOff::where('id', $requestId)
                ->where('employee_id', $employee->id)
                ->first();

            if (!$leaveRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Leave request not found'
                ], 404);
            }

            if ($leaveRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be cancelled'
                ], 400);
            }

            $leaveRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Leave request cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel leave request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========================
    // ADMIN ENDPOINTS
    // ========================

    /**
     * Get all leave requests (Admin)
     */
    public function getAllRequests(Request $request)
    {
        try {
            $status = $request->query('status'); // pending, approved, rejected
            $employeeId = $request->query('employee_id');

            $query = DayOff::with(['employee.user']);

            if ($status) {
                $query->where('status', $status);
            }

            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            $leaveRequests = $query->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($leave) {
                    return [
                        'id' => $leave->id,
                        'employee_id' => $leave->employee_id,
                        'employee_name' => $leave->employee?->fullName ?? 'Unknown',
                        'date' => $leave->date->format('Y-m-d'),
                        'end_date' => $leave->end_date ? $leave->end_date->format('Y-m-d') : null,
                        'reason' => $leave->reason,
                        'type' => $leave->type,
                        'status' => $leave->status,
                        'duration_days' => $leave->duration_days,
                        'admin_notes' => $leave->admin_notes,
                        'approved_by' => $leave->approvedByUser?->name,
                        'approved_at' => $leave->approved_at ? $leave->approved_at->format('Y-m-d H:i:s') : null,
                        'created_at' => $leave->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            // Get summary counts
            $pendingCount = DayOff::pending()->count();
            $approvedCount = DayOff::approved()->count();
            $rejectedCount = DayOff::rejected()->count();

            return response()->json([
                'success' => true,
                'leave_requests' => $leaveRequests,
                'summary' => [
                    'pending' => $pendingCount,
                    'approved' => $approvedCount,
                    'rejected' => $rejectedCount,
                    'total' => $pendingCount + $approvedCount + $rejectedCount,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch leave requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a leave request (Admin)
     */
    public function approveRequest(Request $request, $requestId)
    {
        try {
            $request->validate([
                'admin_notes' => 'nullable|string|max:500',
            ]);

            $user = $request->user();
            $leaveRequest = DayOff::find($requestId);

            if (!$leaveRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Leave request not found'
                ], 404);
            }

            if ($leaveRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be approved'
                ], 400);
            }

            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Update employee's is_day_off flag for the approved dates
            $employee = $leaveRequest->employee;
            if ($employee && Carbon::parse($leaveRequest->date)->isToday()) {
                $employee->update(['is_day_off' => true]);
            }

            // Send notification to employee
            if ($employee && $employee->user) {
                $this->pushService->notifyLeaveApproved($employee->user, [
                    'leave_request_id' => $leaveRequest->id,
                    'date' => $leaveRequest->date->format('Y-m-d'),
                    'end_date' => $leaveRequest->end_date ? $leaveRequest->end_date->format('Y-m-d') : null,
                    'type' => $leaveRequest->type,
                    'admin_notes' => $request->admin_notes,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Leave request approved successfully',
                'leave_request' => [
                    'id' => $leaveRequest->id,
                    'status' => $leaveRequest->status,
                    'approved_at' => $leaveRequest->approved_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve leave request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a leave request (Admin)
     */
    public function rejectRequest(Request $request, $requestId)
    {
        try {
            $request->validate([
                'admin_notes' => 'required|string|max:500',
            ]);

            $user = $request->user();
            $leaveRequest = DayOff::find($requestId);

            if (!$leaveRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Leave request not found'
                ], 404);
            }

            if ($leaveRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be rejected'
                ], 400);
            }

            $leaveRequest->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Send notification to employee
            $employee = $leaveRequest->employee;
            if ($employee && $employee->user) {
                $this->pushService->notifyLeaveRejected($employee->user, [
                    'leave_request_id' => $leaveRequest->id,
                    'date' => $leaveRequest->date->format('Y-m-d'),
                    'end_date' => $leaveRequest->end_date ? $leaveRequest->end_date->format('Y-m-d') : null,
                    'type' => $leaveRequest->type,
                    'admin_notes' => $request->admin_notes,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Leave request rejected',
                'leave_request' => [
                    'id' => $leaveRequest->id,
                    'status' => $leaveRequest->status,
                    'admin_notes' => $leaveRequest->admin_notes,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject leave request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========================
    // ADMIN ATTENDANCE ENDPOINTS
    // ========================

    /**
     * Get all employee attendance records (Admin)
     */
    public function getAllAttendance(Request $request)
    {
        try {
            $date = $request->query('date', Carbon::today()->format('Y-m-d'));
            $employeeId = $request->query('employee_id');

            $query = Attendance::with(['employee.user'])
                ->whereDate('clock_in', $date);

            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            $attendances = $query->orderBy('clock_in', 'desc')
                ->get()
                ->map(function ($attendance) {
                    return [
                        'id' => $attendance->id,
                        'employee_id' => $attendance->employee_id,
                        'employee_name' => $attendance->employee?->fullName ?? 'Unknown',
                        'clock_in' => $attendance->clock_in,
                        'clock_out' => $attendance->clock_out,
                        'hours_worked' => $attendance->hours_worked,
                        'status' => $attendance->status,
                        'clock_in_location' => [
                            'latitude' => $attendance->clock_in_latitude,
                            'longitude' => $attendance->clock_in_longitude,
                        ],
                        'clock_out_location' => [
                            'latitude' => $attendance->clock_out_latitude,
                            'longitude' => $attendance->clock_out_longitude,
                        ],
                        'clock_in_photo' => $attendance->clock_in_photo,
                    ];
                });

            // Get all employees for comparison
            $allEmployees = Employee::with('user')
                ->where('is_active', true)
                ->get();

            $clockedInIds = $attendances->pluck('employee_id')->toArray();

            $summary = [
                'total_employees' => $allEmployees->count(),
                'present' => count($clockedInIds),
                'absent' => $allEmployees->count() - count($clockedInIds),
                'on_leave' => DayOff::where('date', '<=', $date)
                    ->where(function ($q) use ($date) {
                        $q->where('end_date', '>=', $date)
                          ->orWhereNull('end_date');
                    })
                    ->where('status', 'approved')
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'date' => $date,
                'attendances' => $attendances,
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attendance records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance details with geofence validation (Admin)
     */
    public function getAttendanceDetails(Request $request, $attendanceId)
    {
        try {
            $attendance = Attendance::with(['employee.user'])->find($attendanceId);

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance record not found'
                ], 404);
            }

            // Geofence validation (example: check if within 100m of office)
            // This would need actual office coordinates from settings
            $officeLatitude = 14.5995; // Example: Manila
            $officeLongitude = 120.9842;
            $geofenceRadius = 100; // meters

            $clockInDistance = $this->calculateDistance(
                $attendance->clock_in_latitude,
                $attendance->clock_in_longitude,
                $officeLatitude,
                $officeLongitude
            );

            $clockOutDistance = null;
            if ($attendance->clock_out_latitude && $attendance->clock_out_longitude) {
                $clockOutDistance = $this->calculateDistance(
                    $attendance->clock_out_latitude,
                    $attendance->clock_out_longitude,
                    $officeLatitude,
                    $officeLongitude
                );
            }

            return response()->json([
                'success' => true,
                'attendance' => [
                    'id' => $attendance->id,
                    'employee_name' => $attendance->employee?->fullName ?? 'Unknown',
                    'clock_in' => $attendance->clock_in,
                    'clock_out' => $attendance->clock_out,
                    'hours_worked' => $attendance->hours_worked,
                    'status' => $attendance->status,
                    'clock_in_photo' => $attendance->clock_in_photo
                        ? asset('storage/' . $attendance->clock_in_photo)
                        : null,
                ],
                'geofence_validation' => [
                    'clock_in_distance_meters' => round($clockInDistance),
                    'clock_in_within_geofence' => $clockInDistance <= $geofenceRadius,
                    'clock_out_distance_meters' => $clockOutDistance ? round($clockOutDistance) : null,
                    'clock_out_within_geofence' => $clockOutDistance ? $clockOutDistance <= $geofenceRadius : null,
                    'geofence_radius' => $geofenceRadius,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attendance details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
