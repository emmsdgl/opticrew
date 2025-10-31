<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'service_type' => 'required|string|in:Final Cleaning,Deep Cleaning',
            'overall_rating' => 'required|integer|min:1|max:5',
            'quality_rating' => 'required|integer|min:1|max:5',
            'cleanliness_rating' => 'required|integer|min:1|max:5',
            'punctuality_rating' => 'required|integer|min:1|max:5',
            'professionalism_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'comments' => 'required|string|min:10',
            'would_recommend' => 'boolean',
        ]);

        // Get the authenticated user and their client record
        $user = Auth::user();

        if (!$user || !$user->client) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in as a client to submit feedback.'
            ], 403);
        }

        // Create the feedback
        $feedback = Feedback::create([
            'client_id' => $user->client->id,
            'service_type' => $validated['service_type'],
            'overall_rating' => $validated['overall_rating'],
            'quality_rating' => $validated['quality_rating'],
            'cleanliness_rating' => $validated['cleanliness_rating'],
            'punctuality_rating' => $validated['punctuality_rating'],
            'professionalism_rating' => $validated['professionalism_rating'],
            'value_rating' => $validated['value_rating'],
            'comments' => $validated['comments'],
            'would_recommend' => $validated['would_recommend'] ?? false,
        ]);

        // Notify all admin users
        $adminUsers = User::where('role', 'admin')->get();

        if ($adminUsers->isNotEmpty()) {
            $this->notificationService->createMany(
                $adminUsers,
                'feedback_received',
                'New Feedback Received',
                "{$user->name} submitted feedback for {$validated['service_type']} with {$validated['overall_rating']} stars.",
                [
                    'feedback_id' => $feedback->id,
                    'client_id' => $user->client->id,
                    'client_name' => $user->name,
                    'service_type' => $validated['service_type'],
                    'rating' => $validated['overall_rating'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback! Your review has been submitted successfully.',
            'feedback' => $feedback
        ], 201);
    }
}
