<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    /**
     * Static keyword-to-response mapping for Fin-noys business topics.
     * Pricing data sourced from the Price Quotation page tables.
     */
    private array $responses = [
        // Greetings
        [
            'keywords' => ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening', 'howdy', 'greetings', 'moi', 'hei', 'terve'],
            'response' => "Hello! Welcome to Fin-noys Cleaning Services. How can I help you today?\n\nHere are some things I can help with:\n• Our cleaning services and pricing\n• How to book an appointment\n• Service areas we cover\n• Contact information\n• Website navigation",
        ],

        // About / Company
        [
            'keywords' => ['about', 'who are you', 'what is finnoys', 'what is fin-noys', 'company', 'business', 'tell me about'],
            'response' => "Fin-noys is a professional cleaning services provider with extensive experience in the hospitality industry. We serve homes and businesses across Finland, focusing on the Lapland region, Municipality of Inari, and Saariselkä.\n\nWe offer personal and company booking services including final cleaning, deep cleaning, daily room cleaning, and seasonal snow-out cleaning.",
        ],

        // Services - General
        [
            'keywords' => ['service', 'services', 'offer', 'what do you do', 'cleaning type', 'cleaning options', 'palvelut'],
            'response' => "We offer the following cleaning services:\n\nPersonal Booking:\n• Deep Cleaning — Thorough top-to-bottom cleaning (€120–€480)\n• Daily Room Cleaning — Complete room refresh for accommodations\n• Snowout Cleaning — Seasonal clearing for cabin pathways\n• Light Daily Cleaning — Routine upkeep for fresh spaces\n• Full Daily Cleaning — Comprehensive cleaning for all areas\n\nCompany Booking:\n• Hotel Rooms, Cabins, Cottages, Igloos\n• Restaurant, Reception, Saunas, Hallway\n\nFor detailed pricing, visit the Price Quotation page.",
        ],

        // Deep Cleaning
        [
            'keywords' => ['deep clean', 'deep cleaning', 'thorough clean', 'top to bottom', 'intensive'],
            'response' => "Our Deep Cleaning is an intensive service for spotless results in hard-to-reach areas. Base rate: €48/hour.\n\nPricing by unit size (Normal Day / Sun & Holiday):\n• 20–50 m²: €120 / €240\n• 51–70 m²: €168 / €336\n• 71–90 m²: €216 / €432\n• 91–120 m²: €264 / €528\n• 121–140 m²: €312 / €624\n• 141–160 m²: €360 / €720\n• 161–180 m²: €408 / €816\n• 181–220 m²: €480 / €960\n\nIncludes: all final cleaning tasks, hard-to-reach areas, detailed scrubbing, behind appliances, window sills, baseboards, and deep floor treatment.\n\nAll prices include 24% VAT. Sundays and holidays are double rate.",
        ],

        // Final Cleaning
        [
            'keywords' => ['final clean', 'final cleaning', 'move out', 'move-out', 'regular maintenance'],
            'response' => "Our Final Cleaning is a complete cleaning solution for regular maintenance and move-out situations.\n\nPricing by unit size (Normal Day / Sun & Holiday):\n• 20–50 m²: €70 / €140\n• 51–70 m²: €105 / €210\n• 71–90 m²: €140 / €280\n• 91–120 m²: €175 / €350\n• 121–140 m²: €210 / €420\n• 141–160 m²: €245 / €490\n• 161–180 m²: €280 / €560\n• 181–220 m²: €315 / €630\n\nIncludes: kitchen cleaning, living room & bedroom tidying, bathroom & sauna cleaning, vacuuming & mopping.\n\nAll prices include 24% VAT. Sundays and holidays are double rate.",
        ],

        // Full Daily Cleaning
        [
            'keywords' => ['full daily', 'comprehensive daily'],
            'response' => "Our Full Daily Cleaning is a comprehensive cleaning service covering all areas for optimal hygiene and presentation. It's ideal for accommodations and businesses that need daily thorough cleaning.\n\nFor exact pricing based on your space, visit the Price Quotation page or contact us for a custom quote.",
        ],

        // Light Daily Cleaning
        [
            'keywords' => ['light daily', 'light cleaning', 'routine', 'upkeep'],
            'response' => "Our Light Daily Cleaning is a routine upkeep service designed to keep spaces fresh and presentable between deeper cleans.\n\nFor exact pricing based on your space, visit the Price Quotation page or contact us for a custom quote.",
        ],

        // Daily Room Cleaning
        [
            'keywords' => ['daily room', 'room cleaning', 'room refresh'],
            'response' => "Our Daily Room Cleaning provides a complete room refresh tailored for guest accommodations. It keeps rooms guest-ready with regular maintenance.\n\nFor exact pricing based on your space, visit the Price Quotation page or contact us for a custom quote.",
        ],

        // Snow Out Cleaning
        [
            'keywords' => ['snow out', 'snowout', 'snow cleaning', 'seasonal', 'turnover clean', 'between guests', 'cabin pathway'],
            'response' => "Our Snowout Cleaning is a seasonal service focused on clearing snow and ice from cabin pathways for safety and accessibility. It also includes comprehensive cleaning for vacation rentals between guest stays.\n\nFor exact pricing based on your space, visit the Price Quotation page or contact us for a custom quote.",
        ],

        // Pricing / Cost - General
        [
            'keywords' => ['price', 'pricing', 'cost', 'how much', 'rate', 'fee', 'quote', 'quotation', 'hinta', 'budget', 'expensive', 'cheap', 'affordable', 'tariff'],
            'response' => "Our pricing is based on unit size (m²) and service type:\n\nFinal Cleaning (most popular):\n• From €70 (20–50 m²) to €315 (181–220 m²)\n\nDeep Cleaning (€48/hr based):\n• From €120 (20–50 m²) to €480 (181–220 m²)\n\nSpecial Day Rates:\n• Sundays and public holidays are charged at double the normal rate\n\nAll prices include 24% VAT — no hidden fees.\n\nFor a detailed custom quote, visit the Price Quotation page from the main menu.",
        ],

        // VAT / Tax
        [
            'keywords' => ['vat', 'tax', 'alv', 'hidden fee', 'hidden charge', 'extra charge', 'additional cost'],
            'response' => "All our prices include 24% VAT. There are no hidden fees or additional charges. What you see on our pricing table is the final price you pay.\n\nSundays and public holidays are charged at double the normal rate due to special scheduling requirements.",
        ],

        // Sunday / Holiday rates
        [
            'keywords' => ['sunday', 'holiday', 'weekend', 'special day', 'double rate', 'pyhäpäivä', 'sunnuntai'],
            'response' => "Our special day rates apply on Sundays and public holidays. These are charged at double the normal rate due to special scheduling requirements.\n\nFor example, Final Cleaning for 20–50 m²:\n• Normal day: €70\n• Sunday/Holiday: €140\n\nDeep Cleaning for 20–50 m²:\n• Normal day: €120\n• Sunday/Holiday: €240",
        ],

        // Unit size / Square meters
        [
            'keywords' => ['square meter', 'square metres', 'unit size', 'how big', 'size', 'neliö', 'm2', 'm²', 'area size'],
            'response' => "Our pricing is based on unit size in square meters (m²). We cover these size ranges:\n\n• 20–50 m²\n• 51–70 m²\n• 71–90 m²\n• 91–120 m²\n• 121–140 m²\n• 141–160 m²\n• 161–180 m²\n• 181–220 m²\n\nFor spaces larger than 220 m², please contact us for a custom quote.",
        ],

        // Company booking
        [
            'keywords' => ['company', 'corporate', 'hotel', 'cabin', 'cottage', 'igloo', 'restaurant', 'reception', 'sauna', 'hallway', 'yritys'],
            'response' => "We offer company booking services for businesses including:\n\n• Hotel Rooms Cleaning\n• Cabins & Cottages\n• Igloos\n• Restaurant Cleaning\n• Reception Areas\n• Saunas\n• Hallways\n• Light & Full Daily Cleaning\n• Deep Cleaning & Snowout\n\nFor corporate rates and custom contracts, please contact us or visit the Price Quotation page.",
        ],

        // Booking / Appointment
        [
            'keywords' => ['book', 'booking', 'appointment', 'schedule', 'reserve', 'varaus', 'ajanvaraus', 'how to book', 'make appointment'],
            'response' => "To book a cleaning service:\n\n1. Log in to your account (or register using Google for convenience)\n2. Go to your Dashboard\n3. Create a new appointment using the booking form (3-step process)\n\nWe offer both personal and company bookings. After booking, you can track appointment status, view assigned teams, and monitor progress from your dashboard.",
        ],

        // Account / Login / Register
        [
            'keywords' => ['login', 'log in', 'sign in', 'register', 'sign up', 'account', 'create account', 'google auth', 'google sign'],
            'response' => "You can access your account by clicking the 'Log In' button on the top right of the website.\n\nDon't have an account yet? You can register directly or sign in with your Google account for quick access.\n\nOnce logged in, you'll have access to your dashboard where you can manage appointments, track progress, and more.",
        ],

        // Dashboard
        [
            'keywords' => ['dashboard', 'my account', 'my appointments', 'track', 'status', 'progress'],
            'response' => "Your Dashboard is your central hub after logging in. From there you can:\n\n• Create new cleaning appointments\n• Track appointment status and progress\n• View your assigned cleaning teams\n• Cancel appointments if needed\n• Provide feedback after service completion\n\nLog in to access your Dashboard.",
        ],

        // Contact
        [
            'keywords' => ['contact', 'email', 'phone', 'call', 'reach', 'address', 'location', 'yhteystiedot', 'where are you'],
            'response' => "You can reach us through:\n\n• Email: finnoys0823@gmail.com\n• Phone: 09288515619\n• Address: Saariselantie 6 C10, Saariselka 99830 Finland\n\nYou can also visit the Contact page on our website for more options.",
        ],

        // Service Areas
        [
            'keywords' => ['service area', 'service areas', 'where do you', 'what areas', 'coverage', 'serve', 'lapland', 'inari', 'saariselka', 'saariselkä', 'do you operate', 'location'],
            'response' => "We serve homes and businesses across:\n\n• Lapland Region\n• Municipality of Inari (including Ivalo, Nellim, Lemmenjoki)\n• Saariselkä and surrounding areas\n\nAll within northern Finland, above the Arctic Circle.",
        ],

        // Careers / Jobs
        [
            'keywords' => ['career', 'careers', 'job', 'jobs', 'apply', 'work for', 'work with', 'hiring', 'employment', 'join', 'vacancy', 'vacancies', 'opening', 'openings', 'recruit', 'recruitment', 'resume', 'cv', 'työ', 'rekry', 'työpaikka'],
            'response' => "Interested in joining our team? You can view all current job openings and submit your application on our Recruitment page:\n\nhttps://www.finnoys.com/recruitment\n\nWe're always looking for dedicated cleaning professionals to join Fin-noys! Check the page regularly for new opportunities.",
        ],

        // Language
        [
            'keywords' => ['language', 'finnish', 'english', 'suomi', 'kieli', 'translate'],
            'response' => "Our website supports both English and Finnish (Suomi). You can switch languages anytime using the language selector in the navigation bar.",
        ],

        // Cancel / Feedback
        [
            'keywords' => ['cancel', 'cancellation', 'feedback', 'review', 'complaint', 'refund'],
            'response' => "You can cancel appointments directly from your Dashboard after logging in.\n\nAfter a service is completed, you'll have the option to provide feedback about your experience. We value your input as it helps us improve our services.\n\nFor any complaints or refund inquiries, please contact us at finnoys0823@gmail.com or call 09288515619.",
        ],

        // Payment
        [
            'keywords' => ['pay', 'payment', 'invoice', 'billing', 'maksu'],
            'response' => "For payment and billing inquiries, please contact us directly:\n\n• Email: finnoys0823@gmail.com\n• Phone: 09288515619\n\nAll our prices include 24% VAT with no hidden fees. Visit the Price Quotation page for detailed pricing.",
        ],

        // Thanks / Goodbye
        [
            'keywords' => ['thank', 'thanks', 'kiitos', 'bye', 'goodbye', 'see you', 'appreciate', 'helpful'],
            'response' => "You're welcome! If you have any more questions about our services, feel free to ask anytime. Have a great day!",
        ],

        // Help
        [
            'keywords' => ['help', 'assist', 'support', 'apu', 'apua'],
            'response' => "I'm here to help! Here's what I can assist you with:\n\n• Our cleaning services and pricing\n• Final Cleaning & Deep Cleaning rates\n• How to book an appointment\n• Service areas we cover\n• Contact information\n• Company booking options\n\nJust type your question and I'll do my best to help!",
        ],

        // Website navigation
        [
            'keywords' => ['navigate', 'navigation', 'how to', 'where can i find', 'page', 'website', 'site'],
            'response' => "Here's how to navigate our website:\n\n• Services — View all our cleaning offerings\n• Price Quotation — View pricing tables and request a custom quote\n• About — Learn more about Fin-noys\n• Careers — See job openings\n• Contact — Get in touch with us\n• Log In — Access your dashboard to book and manage appointments\n\nAll pages are accessible from the main navigation menu at the top.",
        ],
    ];

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'chat_history' => 'array'
            ]);

            $userMessage = $request->input('message');
            $chatHistory = $request->input('chat_history', []);

            $botResponse = $this->getStaticResponse($userMessage);

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
            return response()->json([
                'success' => false,
                'message' => 'Invalid request: ' . $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, I encountered an error. Please try again.'
            ], 500);
        }
    }

    /**
     * Off-topic words that indicate the user is NOT asking about Fin-noys services.
     * If the message contains these AND no strong service-related keywords, reject it.
     */
    private array $offTopicWords = [
        'food', 'dish', 'recipe', 'cook', 'restaurant food', 'eat', 'meal', 'cuisine',
        'weather', 'forecast', 'temperature', 'rain', 'snow forecast',
        'news', 'politics', 'election', 'president', 'war',
        'movie', 'film', 'music', 'song', 'game', 'sport', 'football',
        'math', 'calculate', 'solve', 'equation',
        'joke', 'funny', 'story', 'poem', 'write me',
        'travel', 'tourist', 'visit', 'place to visit', 'sightseeing', 'attraction',
        'health', 'doctor', 'medicine', 'symptom', 'diet',
        'programming', 'code', 'python', 'javascript',
        'history', 'who invented', 'when was', 'capital of',
        'best place', 'best food', 'best thing', 'recommend me',
        'what should i', 'where should i',
    ];

    /**
     * Strong intent words that confirm the user IS asking about Fin-noys topics.
     * These override off-topic detection when present.
     */
    private array $intentWords = [
        'clean', 'cleaning', 'finnoys', 'fin-noys', 'book', 'booking', 'appointment',
        'service', 'services', 'price', 'pricing', 'cost', 'quote', 'quotation',
        'deep clean', 'final clean', 'snowout', 'snow out', 'daily room',
        'dashboard', 'login', 'log in', 'register', 'sign up', 'account',
        'contact', 'email', 'phone', 'address', 'career', 'job', 'apply',
        'cancel', 'feedback', 'refund', 'payment', 'invoice', 'vat',
        'cabin', 'cottage', 'igloo', 'hotel', 'sauna',
        'palvelut', 'varaus', 'hinta', 'siivous',
    ];

    private string $offTopicResponse = "I appreciate your curiosity, but I can only help with Fin-noys cleaning services topics.\n\nHere's what I can assist you with:\n• Our cleaning services and pricing\n• How to book an appointment\n• Service areas we cover\n• Contact information\n• Website navigation\n\nFeel free to ask about any of these!";

    private string $fallbackResponse = "I'm sorry, I didn't quite understand that. I can help you with:\n\n• Our cleaning services and pricing\n• Final Cleaning & Deep Cleaning rate tables\n• How to book an appointment\n• Service areas we cover\n• Contact information\n• Company booking options\n\nTry asking about any of these topics!";

    /**
     * Match user message against keyword patterns and return the best static response.
     */
    private function getStaticResponse(string $message): string
    {
        $normalized = mb_strtolower(trim($message));

        // Check if the message has a strong service-related intent
        $hasIntent = false;
        foreach ($this->intentWords as $intent) {
            if (str_contains($normalized, $intent)) {
                $hasIntent = true;
                break;
            }
        }

        // If no service intent found, check for off-topic words
        if (!$hasIntent) {
            foreach ($this->offTopicWords as $offTopic) {
                if (str_contains($normalized, $offTopic)) {
                    return $this->offTopicResponse;
                }
            }
        }

        // Proceed with keyword matching
        $bestMatch = null;
        $bestScore = 0;

        foreach ($this->responses as $entry) {
            $score = 0;
            foreach ($entry['keywords'] as $keyword) {
                if (str_contains($normalized, mb_strtolower($keyword))) {
                    $score += strlen($keyword);
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $entry['response'];
            }
        }

        if ($bestMatch) {
            return $bestMatch;
        }

        return $this->fallbackResponse;
    }
}
