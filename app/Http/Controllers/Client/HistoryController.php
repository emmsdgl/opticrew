<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientAppointment;
use App\Models\Feedback;
use App\Models\Client;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        // Get current client
        $user = Auth::user();
        $client = Client::where('user_id', $user->id)->first();

        if (!$client) {
            return view('client.history', [
                'activities' => [],
                'ratings' => []
            ]);
        }

        // Fetch client appointments
        $appointments = ClientAppointment::where('client_id', $client->id)
            ->with(['assignedTeam.members.employee.user'])
            ->orderBy('service_date', 'desc')
            ->orderBy('service_time', 'desc')
            ->get();

        // Transform appointments into activities format
        $activities = $appointments->map(function ($appointment) use ($client) {
            // Get assigned members
            $assignedMembers = [];
            if ($appointment->assignedTeam && $appointment->assignedTeam->members) {
                $assignedMembers = $appointment->assignedTeam->members->map(function ($member) {
                    $name = $member->employee->user->name ?? 'Unknown';
                    return [
                        'name' => $name,
                        'initial' => strtoupper(substr($name, 0, 1)),
                    ];
                })->toArray();
            }

            // Determine if needs rating
            $needsRating = $appointment->status === 'completed' && !Feedback::where('appointment_id', $appointment->id)
                ->where('user_type', 'client')
                ->where('client_id', $client->id)
                ->exists();

            // Format datetime properly
            $dateStr = Carbon::parse($appointment->service_date)->format('Y-m-d');
            $timeStr = $appointment->service_time ? Carbon::parse($appointment->service_time)->format('H:i:s') : '00:00:00';
            $scheduledDateTime = $dateStr . ' ' . $timeStr;

            // Load related task with checklist completions for progress tracking
            $checklistCompletions = [];
            $relatedTask = Task::with('checklistCompletions')
                ->where('client_id', $appointment->client_id)
                ->whereDate('scheduled_date', $appointment->service_date)
                ->where('task_description', 'like', '%' . $appointment->service_type . '%')
                ->first();

            if ($relatedTask) {
                $checklistCompletions = $relatedTask->checklistCompletions
                    ->where('is_completed', true)
                    ->pluck('checklist_item_id')
                    ->toArray();
            }

            return [
                'id' => $appointment->id,
                'type' => 'service',
                'icon' => $this->getServiceIcon($appointment->service_type),
                'title' => $appointment->service_type . ' - ' . ($appointment->cabin_name ?? 'Booking'),
                'date' => Carbon::parse($scheduledDateTime)->format('d M Y, g:i a'),
                'price' => '€ ' . number_format($appointment->total_amount ?? 0, 2),
                'status' => ucfirst($appointment->status),
                'needsRating' => $needsRating,
                'appointmentId' => 'APT-' . str_pad($appointment->id, 6, '0', STR_PAD_LEFT),
                'serviceDate' => Carbon::parse($appointment->service_date)->format('Y-m-d'),
                'serviceTime' => $appointment->service_time ? Carbon::parse($appointment->service_time)->format('g:i A') : 'TBD',
                'serviceType' => $appointment->service_type,
                'location' => $appointment->cabin_name ?? 'N/A',
                'totalAmount' => '€' . number_format($appointment->total_amount ?? 0, 2),
                'payableAmount' => '€' . number_format($appointment->quotation ?? 0, 2),
                'assignedMembers' => $assignedMembers,
                'checklist_completions' => $checklistCompletions,
            ];
        });

        // Fetch client's submitted feedback/ratings
        $feedbacks = Feedback::where('client_id', $client->id)
            ->where('user_type', 'client')
            ->with(['appointment'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ratings = $feedbacks->map(function ($feedback) {
            return [
                'id' => $feedback->id,
                'appointment_id' => $feedback->appointment_id,
                'serviceName' => $feedback->appointment ? $feedback->appointment->service_type : 'Unknown Service',
                'location' => $feedback->appointment ? $feedback->appointment->cabin_name : 'N/A',
                'rating' => $feedback->rating,
                'keywords' => $feedback->keywords ?? [],
                'feedback_text' => $feedback->feedback_text,
                'submitted_at' => Carbon::parse($feedback->created_at)->format('d M Y, g:i a'),
                'icon' => $this->getServiceIcon($feedback->appointment ? $feedback->appointment->service_type : ''),
            ];
        });

        return view('client.history', [
            'activities' => $activities->toArray(),
            'ratings' => $ratings->toArray()
        ]);
    }

    /**
     * Get an appropriate icon based on service type
     */
    private function getServiceIcon($serviceType)
    {
        $serviceType = strtolower($serviceType ?? '');

        if (str_contains($serviceType, 'deep')) {
            return asset('images/icons/cleaning/deep-cleaning-icon.svg');
        } elseif (str_contains($serviceType, 'daily') && str_contains($serviceType, 'room')) {
            return asset('images/icons/cleaning/daily-room-cleaning-icon.svg');
        } elseif (str_contains($serviceType, 'daily')) {
            return asset('images/icons/cleaning/daily-cleaning-icon.svg');
        } elseif (str_contains($serviceType, 'hotel')) {
            return asset('images/icons/cleaning/hotel-cleaning-icon.svg');
        } elseif (str_contains($serviceType, 'snow') || str_contains($serviceType, 'move') || str_contains($serviceType, 'out')) {
            return asset('images/icons/cleaning/snowout-cleaning-icon.svg');
        }

        return asset('images/icons/cleaning/daily-cleaning-icon.svg'); // Default icon
    }

    /**
     * Store client feedback for an appointment
     */
    public function storeFeedback(Request $request)
    {
        $user = Auth::user();
        $client = Client::where('user_id', $user->id)->first();

        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client not found'], 404);
        }

        $validated = $request->validate([
            'appointment_id' => 'required|exists:client_appointments,id',
            'rating' => 'required|integer|min:1|max:5',
            'keywords' => 'nullable|array',
            'feedback_text' => 'nullable|string',
        ]);

        // Check if feedback already exists
        $existingFeedback = Feedback::where('appointment_id', $validated['appointment_id'])
            ->where('client_id', $client->id)
            ->where('user_type', 'client')
            ->first();

        if ($existingFeedback) {
            return response()->json(['success' => false, 'message' => 'Feedback already submitted for this appointment'], 400);
        }

        // Get the appointment to retrieve service type
        $appointment = ClientAppointment::find($validated['appointment_id']);

        // Create new feedback
        $feedback = Feedback::create([
            'appointment_id' => $validated['appointment_id'],
            'client_id' => $client->id,
            'user_type' => 'client',
            'rating' => $validated['rating'],
            'keywords' => $validated['keywords'] ?? [],
            'feedback_text' => $validated['feedback_text'],
            'service_type' => $appointment ? $appointment->service_type : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'feedback' => $feedback
        ]);
    }
}
