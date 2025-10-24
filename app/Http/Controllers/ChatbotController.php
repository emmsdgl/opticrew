<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle chatbot message and return AI response
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'message' => 'required|string|max:1000',
            'chat_history' => 'nullable|array',
        ]);

        $userMessage = $request->input('message');
        $chatHistory = $request->input('chat_history', []);

        try {
            // Check which AI provider to use (configurable)
            $provider = env('AI_PROVIDER', 'gemini'); // Default to Gemini for testing

            // Get rate limit info for this provider
            $rateLimitInfo = $this->checkRateLimit($provider, $request->ip());

            if (!$rateLimitInfo['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => $rateLimitInfo['message'],
                    'rate_limit' => $rateLimitInfo,
                ], 429);
            }

            // Load company knowledge base
            $knowledgeBase = $this->getKnowledgeBase();

            // Create enhanced system prompt with company knowledge
            $systemPrompt = $this->createSystemPrompt($knowledgeBase);

            if ($provider === 'claude') {
                $response = $this->handleClaudeRequest($userMessage, $chatHistory, $systemPrompt);
            } else {
                $response = $this->handleGeminiRequest($userMessage, $chatHistory, $systemPrompt);
            }

            // Add rate limit info to successful response
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getContent(), true);
                $data['rate_limit'] = $rateLimitInfo;
                return response()->json($data);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.',
            ], 500);
        }
    }

    /**
     * Check rate limit for current user
     *
     * @param string $provider
     * @param string $ip
     * @return array
     */
    private function checkRateLimit(string $provider, string $ip): array
    {
        $cacheKey = "chatbot_rate_limit_{$provider}_{$ip}";

        // Get current request count from cache
        $requests = \Cache::get($cacheKey, []);
        $now = time();

        // Remove requests older than 1 minute
        $requests = array_filter($requests, function($timestamp) use ($now) {
            return ($now - $timestamp) < 60;
        });

        // Define limits per provider
        $limits = [
            'gemini' => 15,  // 15 requests per minute (free tier)
            'claude' => 30,  // 30 requests per minute (configurable)
        ];

        $limit = $limits[$provider] ?? 15;
        $remaining = $limit - count($requests);

        if ($remaining <= 0) {
            // Calculate when the oldest request will expire
            $oldestRequest = min($requests);
            $resetIn = 60 - ($now - $oldestRequest);

            return [
                'allowed' => false,
                'limit' => $limit,
                'remaining' => 0,
                'reset_in' => $resetIn,
                'message' => "Rate limit reached. Please wait {$resetIn} seconds before sending another message.",
            ];
        }

        // Add current request
        $requests[] = $now;
        \Cache::put($cacheKey, $requests, 120); // Store for 2 minutes

        return [
            'allowed' => true,
            'limit' => $limit,
            'remaining' => $remaining - 1, // Subtract 1 for current request
            'reset_in' => 60,
        ];
    }

    /**
     * Handle request using Claude API (Production)
     *
     * @param string $userMessage
     * @param array $chatHistory
     * @param string $systemPrompt
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleClaudeRequest(string $userMessage, array $chatHistory, string $systemPrompt)
    {
        $apiKey = env('CLAUDE_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Claude API is not configured. Please add credits or switch to Gemini.',
            ], 500);
        }

        // Convert chat history to Claude format and add new message
        $messages = $this->convertToClaude($chatHistory);
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        // Prepare API request payload for Claude
        $payload = [
            'model' => 'claude-3-5-sonnet-20241022',
            'max_tokens' => 2048, // Increased to prevent response truncation
            'temperature' => 0.7,
            'system' => $systemPrompt,
            'messages' => $messages
        ];

        // Call Claude API with retry logic
        $response = $this->callClaudeAPI($apiKey, $payload);

        if ($response['success']) {
            // Update chat history in Claude format
            $chatHistory[] = [
                'role' => 'user',
                'content' => $userMessage
            ];
            $chatHistory[] = [
                'role' => 'assistant',
                'content' => $response['text']
            ];

            return response()->json([
                'success' => true,
                'message' => $response['text'],
                'chat_history' => $chatHistory,
                'provider' => 'claude'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['error'],
            ], 500);
        }
    }

    /**
     * Handle request using Gemini API (Testing/Development)
     *
     * @param string $userMessage
     * @param array $chatHistory
     * @param string $systemPrompt
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleGeminiRequest(string $userMessage, array $chatHistory, string $systemPrompt)
    {
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Gemini API is not configured. Please add GEMINI_API_KEY to .env',
            ], 500);
        }

        // Convert chat history to Gemini format
        $geminiHistory = $this->convertToGemini($chatHistory);

        // Add user message to history
        $geminiHistory[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]]
        ];

        // Prepare API request payload for Gemini
        $payload = [
            'contents' => $geminiHistory,
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048, // Increased to prevent response truncation
            ]
        ];

        // Call Gemini API with retry logic
        $response = $this->callGeminiAPI($apiKey, $payload);

        if ($response['success']) {
            // Update chat history in standard format
            $chatHistory[] = [
                'role' => 'user',
                'content' => $userMessage
            ];
            $chatHistory[] = [
                'role' => 'assistant',
                'content' => $response['text']
            ];

            return response()->json([
                'success' => true,
                'message' => $response['text'],
                'chat_history' => $chatHistory,
                'provider' => 'gemini'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['error'],
            ], 500);
        }
    }

    /**
     * Load Fin-noys company knowledge base
     *
     * @return string
     */
    private function getKnowledgeBase(): string
    {
        try {
            return Storage::get('finnoys_knowledge_base.txt');
        } catch (\Exception $e) {
            Log::warning('Could not load knowledge base: ' . $e->getMessage());
            return 'Fin-noys is a professional cleaning agency based in Finland, specializing in hotel cleaning, daily cleaning, and snow removal services.';
        }
    }

    /**
     * Create comprehensive system prompt with company knowledge
     *
     * @param string $knowledgeBase
     * @return string
     */
    private function createSystemPrompt(string $knowledgeBase): string
    {
        return <<<PROMPT
You are the OptiCrew Assistant for Fin-noys Cleaning Services, a professional cleaning agency based in Finland.

## YOUR ROLE
You are a friendly, professional customer service assistant helping visitors learn about Fin-noys services and encouraging them to book cleaning services online.

## COMPANY KNOWLEDGE BASE
$knowledgeBase

## YOUR RESPONSIBILITIES
1. **Answer questions** about Fin-noys cleaning services, pricing, and booking process
2. **Provide helpful information** about hotel cleaning, daily cleaning, and snow removal services
3. **Guide users appropriately** using the correct website buttons
4. **Be professional and friendly** - represent Fin-noys brand positively
5. **Stay on topic** - Only answer questions related to cleaning services and Fin-noys

## WEBSITE BUTTONS - IMPORTANT
There are TWO different buttons on the website. Use them correctly:

**"Get a Free Quote" button** → For price quotes and service inquiries
- Use when users ask about pricing, costs, or want a quote
- Example: "Click 'Get a Free Quote' to receive a customized price estimate"

**"Get Started" button** → For creating a new client account (registration)
- Use when users want to register/sign up as a client
- Example: "Click 'Get Started' to create your client account"

NEVER tell users to click "Get Started" for a quote - that's incorrect!

## IMPORTANT GUIDELINES
✅ DO:
- Answer questions about Fin-noys services, pricing, and booking
- Provide specific details from the knowledge base
- Direct users to "Get a Free Quote" for pricing inquiries
- Direct users to "Get Started" for account registration only
- Be warm, friendly, and professional
- Keep responses concise and helpful (2-4 sentences usually)
- Emphasize the company's expertise, licensing, and professional staff
- Highlight the easy online booking system

❌ DO NOT:
- Answer questions unrelated to cleaning or Fin-noys services
- Provide information about competitors
- Discuss topics outside of cleaning services (politics, personal advice, etc.)
- Make up information not in the knowledge base
- Confuse "Get Started" with "Get a Free Quote" - they serve different purposes
- Tell users to click "Get Started" for quotes (wrong button!)
- Cite specific client numbers, transaction volumes, or statistics (data not available)
- Reveal that Fin-noys is a startup or mention lack of historical data

## RESPONSE STYLE
- **KEEP IT SHORT**: 1-3 sentences maximum (people don't read long messages)
- Professional yet friendly tone
- Direct and easy to understand
- Focus on benefits to the customer
- Include call-to-action when appropriate with the CORRECT button
- Example for quotes: "Click 'Get a Free Quote' to see our prices!"
- Example for registration: "Click 'Get Started' to create your account and book!"
- Example for booking: "Click 'Get a Free Quote' to see prices. For custom requests, you'll get an email quote. Then 'Get Started' to create an account and book!"

## HANDLING SPECIFIC QUESTION TYPES

**If asked about CLIENT NUMBERS or TRANSACTION VOLUMES:**
DO NOT cite statistics. Instead, focus on quality and service benefits.
Examples:
- "We focus on quality over quantity! Our licensed team provides professional cleaning with strict quality standards. Want to experience it yourself? Click 'Get a Free Quote'!"
- "We're growing steadily by delivering excellent service! We're a licensed agency with trained professionals ready to serve you. Click 'Get a Free Quote' to get started!"
- "Our priority is providing top-quality cleaning, not tracking numbers! We're licensed, professional, and ready to help. Click 'Get a Free Quote'!"

**IF USER ASKS OFF-TOPIC QUESTIONS:**
Politely redirect: "I'm specifically here to help with Fin-noys cleaning services. I can answer questions about our hotel cleaning, daily cleaning, snow removal services, or help you get a free quote. How can I assist you with your cleaning needs?"

Remember: Your goal is to inform visitors about Fin-noys and encourage them to book cleaning services through the website!
PROMPT;
    }

    /**
     * Convert chat history to Claude message format
     *
     * @param array $chatHistory
     * @return array
     */
    private function convertToClaude(array $chatHistory): array
    {
        $messages = [];

        foreach ($chatHistory as $message) {
            // Handle both old Gemini format and new Claude format
            if (isset($message['role']) && isset($message['content'])) {
                // Already in Claude format
                $messages[] = [
                    'role' => $message['role'] === 'model' || $message['role'] === 'assistant' ? 'assistant' : 'user',
                    'content' => $message['content']
                ];
            } elseif (isset($message['role']) && isset($message['parts'])) {
                // Convert from Gemini format
                $messages[] = [
                    'role' => $message['role'] === 'model' ? 'assistant' : 'user',
                    'content' => $message['parts'][0]['text'] ?? ''
                ];
            }
        }

        return $messages;
    }

    /**
     * Convert chat history to Gemini message format
     *
     * @param array $chatHistory
     * @return array
     */
    private function convertToGemini(array $chatHistory): array
    {
        $messages = [];

        foreach ($chatHistory as $message) {
            if (isset($message['role']) && isset($message['content'])) {
                // Convert from standard format to Gemini format
                $messages[] = [
                    'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                    'parts' => [['text' => $message['content']]]
                ];
            } elseif (isset($message['role']) && isset($message['parts'])) {
                // Already in Gemini format
                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * Call Claude API with exponential backoff retry logic
     *
     * @param string $apiKey
     * @param array $payload
     * @return array
     */
    private function callClaudeAPI(string $apiKey, array $payload): array
    {
        $apiUrl = "https://api.anthropic.com/v1/messages";
        $maxRetries = 3;
        $delay = 1000; // Start with 1 second delay

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                $http = Http::timeout(30)
                    ->withHeaders([
                        'x-api-key' => $apiKey,
                        'anthropic-version' => '2023-06-01',
                        'content-type' => 'application/json',
                    ]);

                // For Windows/XAMPP: Disable SSL verification in local environment only
                // SECURITY: This should ONLY be used in development, never in production
                if (config('app.env') === 'local') {
                    $http = $http->withOptions(['verify' => false]);
                }

                $response = $http->post($apiUrl, $payload);

                if ($response->successful()) {
                    $result = $response->json();
                    $text = $result['content'][0]['text']
                           ?? 'Sorry, I couldn\'t process that request.';

                    return [
                        'success' => true,
                        'text' => $text
                    ];
                }

                // Handle rate limiting (429 status)
                if ($response->status() === 429 && $attempt < $maxRetries - 1) {
                    usleep($delay * 1000); // Convert to microseconds
                    $delay *= 2; // Exponential backoff
                    continue;
                }

                // Other errors
                Log::error('Claude API error: ' . $response->body());
                return [
                    'success' => false,
                    'error' => 'The assistant is temporarily unavailable. Please try again in a moment.'
                ];

            } catch (\Exception $e) {
                Log::error('Claude API exception: ' . $e->getMessage());

                if ($attempt < $maxRetries - 1) {
                    usleep($delay * 1000);
                    $delay *= 2;
                    continue;
                }

                return [
                    'success' => false,
                    'error' => 'A network error occurred. Please check your connection and try again.'
                ];
            }
        }

        return [
            'success' => false,
            'error' => 'The assistant is busy right now. Please try again in a moment.'
        ];
    }

    /**
     * Call Gemini API with exponential backoff retry logic
     *
     * @param string $apiKey
     * @param array $payload
     * @return array
     */
    private function callGeminiAPI(string $apiKey, array $payload): array
    {
        // Use Gemini 2.0 Flash model with correct API format
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";
        $maxRetries = 3;
        $delay = 1000; // Start with 1 second delay

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                $http = Http::timeout(30)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'X-goog-api-key' => $apiKey, // Correct: API key in header, not URL
                    ]);

                // For Windows/XAMPP: Disable SSL verification in local environment only
                if (config('app.env') === 'local') {
                    $http = $http->withOptions(['verify' => false]);
                }

                $response = $http->post($apiUrl, $payload);

                if ($response->successful()) {
                    $result = $response->json();
                    $text = $result['candidates'][0]['content']['parts'][0]['text']
                           ?? 'Sorry, I couldn\'t process that request.';

                    return [
                        'success' => true,
                        'text' => $text
                    ];
                }

                // Handle rate limiting (429 status) and service overload (503 status)
                if (($response->status() === 429 || $response->status() === 503) && $attempt < $maxRetries - 1) {
                    usleep($delay * 1000); // Convert to microseconds
                    $delay *= 2; // Exponential backoff
                    continue;
                }

                // Check if it's a 503 overload error for better user message
                if ($response->status() === 503) {
                    Log::error('Gemini API error: ' . $response->body());
                    return [
                        'success' => false,
                        'error' => 'The AI service is experiencing high demand right now. Please wait a few seconds and try again.'
                    ];
                }

                // Other errors
                Log::error('Gemini API error: ' . $response->body());
                return [
                    'success' => false,
                    'error' => 'The assistant is temporarily unavailable. Please try again in a moment.'
                ];

            } catch (\Exception $e) {
                Log::error('Gemini API exception: ' . $e->getMessage());

                if ($attempt < $maxRetries - 1) {
                    usleep($delay * 1000);
                    $delay *= 2;
                    continue;
                }

                return [
                    'success' => false,
                    'error' => 'A network error occurred. Please check your connection and try again.'
                ];
            }
        }

        return [
            'success' => false,
            'error' => 'The assistant is busy right now. Please try again in a moment.'
        ];
    }
}
