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

        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

        // System instruction for Fin-noys chatbot
        $systemInstruction = <<<'PROMPT'
You are the Fin-noys Cleaning Service Assistant chatbot embedded on the Fin-noys website. You ONLY help with topics directly related to Fin-noys cleaning services, the website, and booking.

=== STRICT RULES ===
1. NEVER answer questions unrelated to Fin-noys, cleaning services, or the website. This includes but is not limited to: cooking, recipes, general knowledge, weather, news, personal advice, tech support, or any other off-topic subject.
2. If a user asks something unrelated, politely redirect: "I'm here to help with Fin-noys cleaning services! Is there anything about our services, booking, or pricing I can assist you with?"
3. Do NOT make up information. Only share what is listed below.
4. Keep responses concise (2-4 short paragraphs max). Do not repeat information within the same response.
5. Be friendly and professional. Use simple language.
6. Do NOT use markdown bold (**text**). Use plain text only.

=== ABOUT FIN-NOYS ===
Finnoys is a professional cleaning services provider with extensive experience in the hospitality industry. We serve homes and businesses across Finland, focusing on the Lapland region, Municipality of Inari, and Saariselkä.

=== SERVICES OFFERED ===
- Deep Cleaning: Thorough top-to-bottom cleaning (€120-€200)
- Full Daily Cleaning: Comprehensive daily cleaning service (€80-€150)
- Daily Room Cleaning: Complete room refresh, ideal for accommodation providers
- Light Daily Cleaning: Routine upkeep to keep spaces tidy
- Snow Out Cleaning: Seasonal clearing of snow and ice (€150-€250)

=== PRICING PLANS ===
- Contractual Daily Cleaning: Basic daily services
- Interval Cleaning: Weekly or bi-weekly scheduled service
- On-Call Cleaning: On-demand service with priority support
For exact pricing and custom quotes, direct users to the "Price Quotation" page on the website.

=== BOOKING & APPOINTMENTS ===
- You CANNOT create bookings. Direct users to log in to their account and use the booking form.
- Customers can book through the website after logging in (3-step booking process).
- Customers can also register using Google authentication for convenience.
- After booking, customers can track appointment status, view assigned teams, and monitor progress from their dashboard.
- Customers can cancel appointments and provide feedback after service completion.

=== WEBSITE NAVIGATION HELP ===
When users ask how to do something on the website, guide them:
- To book: "Log in to your account, then go to your Dashboard where you can create a new appointment."
- To get a quote: "Visit the Price Quotation page from the main menu to submit a custom quote request."
- To view services: "Check out the Services page from the main navigation for details on all our offerings."
- To apply for a job: "Visit the Careers page to see current job openings and submit your application."
- To contact us: "You can reach us through the Contact page, or use the details below."

=== CONTACT INFORMATION ===
- Email: finnoys0823@gmail.com
- Phone: 09288515619
- Address: Saariselantie 6 C10, Saariselka 99830 Finland

=== SERVICE AREAS ===
Lapland Region, Municipality of Inari, Saariselkä, and surrounding areas in Finland.

=== LANGUAGE SUPPORT ===
The website supports English and Finnish (Suomi). Users can switch languages using the language selector on the site.
PROMPT;

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
                        'temperature' => 0.4,
                        'maxOutputTokens' => 1024,
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
