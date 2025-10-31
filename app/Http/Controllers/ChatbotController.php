<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'message' => 'required|string|max:1000',
                'chat_history' => 'array'
            ]);

            $userMessage = $request->input('message');
            $chatHistory = $request->input('chat_history', []);

            // Log for debugging
            Log::info('Chatbot received message', [
                'message' => $userMessage,
                'history_count' => count($chatHistory)
            ]);

            // TODO: Replace with your actual AI API call
            // For now, returning a mock response
            $botResponse = $this->getMockResponse($userMessage);

            // Update chat history
            $chatHistory[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]]
            ];

            $chatHistory[] = [
                'role' => 'model',
                'parts' => [['text' => $botResponse]]
            ];

            return response()->json([
                'success' => true,
                'message' => $botResponse,
                'chat_history' => $chatHistory
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in chatbot', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid request: ' . $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Chatbot error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }
    }

    /**
     * Mock response generator - Replace this with your actual AI API call
     */
    private function getMockResponse($message)
    {
        $message = strtolower($message);

        // Simple mock responses based on keywords
        if (str_contains($message, 'hello') || str_contains($message, 'hi')) {
            return "Hello! How can I help you with cleaning services today?";
        }

        if (str_contains($message, 'service') || str_contains($message, 'clean')) {
            return "We offer various cleaning services including:\n- Regular house cleaning\n- Deep cleaning\n- Office cleaning\n- Move-in/move-out cleaning\n\nWhich service are you interested in?";
        }

        if (str_contains($message, 'price') || str_contains($message, 'cost')) {
            return "Our pricing varies based on the size of your space and type of service. For a detailed quote, please visit our Price Quotation page or tell me more about your needs!";
        }

        if (str_contains($message, 'book') || str_contains($message, 'schedule')) {
            return "Great! I'd love to help you schedule a cleaning. What date works best for you?";
        }

        // Default response
        return "Thanks for your message! I'm here to help with any questions about our cleaning services. Could you tell me more about what you need?";
    }

    /**
     * Example: Integration with Google Gemini API
     * Uncomment and configure when ready to use
     */
    /*
    private function getGeminiResponse($message, $chatHistory)
    {
        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$apiUrl}?key={$apiKey}", [
            'contents' => array_merge($chatHistory, [
                [
                    'role' => 'user',
                    'parts' => [['text' => $message]]
                ]
            ]),
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 256,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not generate a response.';
        }

        throw new \Exception('API request failed: ' . $response->status());
    }
    */
}