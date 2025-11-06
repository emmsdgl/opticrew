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

            // Get AI response using Gemini
            $botResponse = $this->getGeminiResponse($userMessage, $chatHistory);

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
                'message' => 'Sorry, I encountered an error. Please try again.'
            ], 500);
        }
    }

    /**
     * Get response from Google Gemini API
     */
    private function getGeminiResponse($message, $chatHistory)
    {
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            Log::error('GEMINI_API_KEY not configured');
            return 'Sorry, the chatbot is not properly configured. Please contact support.';
        }

        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

        // System instruction for Fin-noys chatbot
        $systemInstruction = "You are the Fin-noys Cleaning Service Assistant. Your primary goal is to answer questions about the company's services and help users navigate the website.\n\n" .
            "IMPORTANT RULES:\n" .
            "1. **About Fin-noys**: Fin-noys is a professional cleaning services provider with extensive experience in the hospitality industry. We serve homes and businesses across Finland, particularly in Lapland, Inari, and Saariselkä regions.\n\n" .
            "2. **Services Offered**:\n" .
            "   - Deep Cleaning: Thorough, top-to-bottom cleaning\n" .
            "   - Daily Room Cleaning: Complete room refresh for accommodations\n" .
            "   - Snowout Cleaning: Seasonal snow and ice clearing\n" .
            "   - Light Daily Cleaning: Routine upkeep\n" .
            "   - Full Daily Cleaning: Comprehensive cleaning service\n\n" .
            "3. **Price Quotations**: When users ask about pricing, rates, or cost estimates, direct them to visit the Price Quotation page for accurate pricing.\n\n" .
            "4. **Booking**: You CANNOT create actual bookings. Direct users to log in or contact directly.\n\n" .
            "5. **Service Areas**: Lapland Region, Municipality of Inari, Saariselkä, and surrounding areas in Finland.\n\n" .
            "6. **Contact Information**:\n" .
            "   - Email: finnoys0823@gmail.com\n" .
            "   - Phone: 09288515619\n" .
            "   - Address: Saariselantie 6 C10, Saariselka 99830 Finland\n\n" .
            "7. **Languages**: Fin-noys supports both English and Finnish (Suomi). Users can switch languages using the language selector.\n\n" .
            "Keep responses friendly, professional, and concise. Stay focused on cleaning services and Fin-noys information.";

        // Prepare contents with system instruction
        $contents = [];

        // Add chat history
        foreach ($chatHistory as $item) {
            $contents[] = [
                'role' => $item['role'] === 'model' ? 'model' : 'user',
                'parts' => $item['parts']
            ];
        }

        // Add current user message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]]
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $apiKey
                ])
                ->post($apiUrl, [
                    'contents' => $contents,
                    'systemInstruction' => [
                        'parts' => [['text' => $systemInstruction]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 500,
                        'topP' => 0.9,
                        'topK' => 40,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Gemini API response received', [
                    'status' => $response->status()
                ]);

                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not generate a response.';
            }

            Log::error('Gemini API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return 'Sorry, I am having trouble connecting to my services. Please try again in a moment.';

        } catch (\Exception $e) {
            Log::error('Gemini API exception', [
                'message' => $e->getMessage()
            ]);

            return 'Sorry, I encountered an error. Please try again.';
        }
    }
}
