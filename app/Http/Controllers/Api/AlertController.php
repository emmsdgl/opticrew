<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * AlertController
 *
 * Handles alert management for Admin Dashboard
 * Provides real-time notifications for delayed tasks
 */
class AlertController extends Controller
{
    /**
     * Get all unacknowledged alerts for admin dashboard
     *
     * GET /api/admin/alerts/unacknowledged
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnacknowledgedAlerts()
    {
        try {
            $alerts = Alert::whereNull('acknowledged_at')
                ->with(['task.location', 'task.optimizationTeam'])
                ->orderBy('triggered_at', 'desc')
                ->get()
                ->map(function($alert) {
                    return [
                        'alert_id' => $alert->id,
                        'task_id' => $alert->task_id,
                        'alert_type' => $alert->alert_type,
                        'delay_minutes' => $alert->delay_minutes,
                        'reason' => $alert->reason,
                        'task_description' => $alert->task->task_description ?? 'Unknown',
                        'location' => $alert->task->location->location_name ?? 'Unknown',
                        'assigned_team_id' => $alert->task->assigned_team_id,
                        'triggered_at' => $alert->triggered_at->toDateTimeString()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $alerts
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to get unacknowledged alerts", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve alerts'
            ], 500);
        }
    }

    /**
     * Acknowledge an alert
     *
     * POST /api/admin/alerts/{alertId}/acknowledge
     *
     * @param int $alertId
     * @return \Illuminate\Http\JsonResponse
     */
    public function acknowledgeAlert($alertId)
    {
        try {
            $alert = Alert::findOrFail($alertId);

            // Get authenticated user ID (admin)
            $userId = Auth::id();

            // Mark as acknowledged
            $alert->acknowledge($userId);

            Log::info("Alert acknowledged", [
                'alert_id' => $alert->id,
                'acknowledged_by' => $userId,
                'acknowledged_at' => $alert->acknowledged_at
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alert acknowledged'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Alert not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error("Failed to acknowledge alert", [
                'alert_id' => $alertId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to acknowledge alert: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all alerts (acknowledged and unacknowledged) for history view
     *
     * GET /api/admin/alerts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllAlerts(Request $request)
    {
        try {
            $query = Alert::with(['task.location', 'task.optimizationTeam', 'acknowledgedBy']);

            // Filter by date range if provided
            if ($request->has('start_date')) {
                $query->whereDate('triggered_at', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->whereDate('triggered_at', '<=', $request->end_date);
            }

            // Filter by alert type if provided
            if ($request->has('alert_type')) {
                $query->where('alert_type', $request->alert_type);
            }

            $alerts = $query->orderBy('triggered_at', 'desc')
                ->get()
                ->map(function($alert) {
                    return [
                        'alert_id' => $alert->id,
                        'task_id' => $alert->task_id,
                        'alert_type' => $alert->alert_type,
                        'delay_minutes' => $alert->delay_minutes,
                        'reason' => $alert->reason,
                        'task_description' => $alert->task->task_description ?? 'Unknown',
                        'location' => $alert->task->location->location_name ?? 'Unknown',
                        'assigned_team_id' => $alert->task->assigned_team_id,
                        'triggered_at' => $alert->triggered_at->toDateTimeString(),
                        'acknowledged_at' => $alert->acknowledged_at?->toDateTimeString(),
                        'acknowledged_by' => $alert->acknowledgedBy ? [
                            'id' => $alert->acknowledgedBy->id,
                            'name' => $alert->acknowledgedBy->name
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $alerts,
                'total' => $alerts->count()
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to get alerts", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve alerts'
            ], 500);
        }
    }
}
