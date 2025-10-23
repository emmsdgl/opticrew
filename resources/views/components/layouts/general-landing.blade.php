<x-layouts.general-landing :title="'Home - Fin-noys Cleaning Services'">

    @slot('topbar')
        @php
            $navItems = [
                ['label' => 'Home', 'url' => route('/landingpage-home'), 'active' => true],
                ['label' => 'About', 'url' => route('/')],
                ['label' => 'Services', 'url' => route('/')],
                ['label' => 'Price Quotation', 'url' => route('/')],
            ];
        @endphp

        <x-topbar 
            :navItems="$navItems"
            logo="/images/finnoys-text-logo.svg"
            logoAlt="Fin-noys"
            companyName="Fin-noys"
            :showAuth="!auth()->check()"
            :loginRoute="route('login')"
        />
    @endslot

    <style>
        @font-face {
            font-family: 'fam-regular';
            src: url('/fonts/FamiljenGrotesk-Regular.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'fam-bold';
            src: url('/fonts/FamiljenGrotesk-Bold.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'fam-bold-italic';
            src: url('/fonts/FamiljenGrotesk-BoldItalic.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        .font-regular {
            font-family: 'fam-regular', sans-serif;
        }

        .font-bold-custom {
            font-family: 'fam-bold', sans-serif;
        }

        .font-bold-italic {
            font-family: 'fam-bold-italic', sans-serif;
        }

        body {
            background-image: url(/images/backgrounds/landing-page-2.svg);
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .dark body {
            background-image: url(/images/backgrounds/landing-page-2-dark.svg);
            background-color: #0f172a;
        }

        .soft-glow {
            box-shadow: 0 80px 90px rgba(255, 252, 252, 0.937);
        }

        .dark .soft-glow {
            box-shadow: 0 80px 90px rgba(15, 23, 42, 0.8);
        }

        .soft-glow-2 {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.218);
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .soft-glow-2 {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.5);
        }

        .frosted-card {
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .frosted-card {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .feature-card.blurred {
            filter: blur(3px);
            transition: filter 0.3s ease-in-out;
        }

        .feature-card.scroll-hidden {
            opacity: 0;
            transform: translateY(50px);
        }

        .feature-card.scroll-visible {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        /* Chatbot Styles */
        .chat-message {
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            max-width: 80%;
        }

        .user-message {
            background-color: #3b82f6;
            color: white;
            margin-left: auto;
        }

        .assistant-message {
            background-color: #f3f4f6;
            color: #1f2937;
        }

        .dark .assistant-message {
            background-color: #374151;
            color: #f3f4f6;
        }

        .loading-indicator {
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>

    <!-- Hero Section -->
    <div id="main-container" class="font-regular">
        <!-- Hero Content -->
        <div id="container-1" class="relative isolate text-center w-full max-w-4xl mx-auto pt-11 pb-24 px-4 md:px-6">
            <!-- Badge -->
            <div class="hidden sm:mb-8 sm:flex sm:justify-center">
                <div class="relative rounded-full px-3 py-1 text-sm text-gray-700 dark:text-gray-300 ring-1 ring-gray-900/10 dark:ring-gray-100/10 hover:ring-gray-900/20 dark:hover:ring-gray-100/20 transition-all">
                    OptiCrew: Your Doorway To A Clean Space.
                </div>
            </div>

            <!-- Main Heading -->
            <h1 class="font-bold-custom text-4xl md:text-5xl lg:text-6xl tracking-normal text-blue-950 dark:text-white p-6 md:p-10">
                Delivering
                <span class="text-blue-500 dark:text-blue-400 inline-flex items-center">
                    <span class="mr-2">
                        <img src="/images/icons/sparkle.svg" alt="sparkle" class="h-8 md:h-12 w-auto">
                    </span>
                    cleanliness
                </span>
                <br>
                in every corner
            </h1>

            <!-- Description -->
            <p class="mt-8 text-sm md:text-base w-full md:w-3/4 mx-auto text-center md:text-justify text-gray-700 dark:text-gray-300 leading-relaxed">
                At Fin-noys Cleaning Service, we redefine what it means to maintain a clean and welcoming environment. Our team of skilled professionals is dedicated to providing top-tier cleaning solutions tailored to your needs, whether it's your home, office, or commercial space.
            </p>

            <!-- CTA Button -->
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="{{ route('quotation') }}"
                    class="rounded-full bg-blue-600 dark:bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 dark:hover:bg-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400 transition-all">
                    Get a Quote
                </a>
            </div>
        </div>

        <!-- Features Section -->
        <div id="container-2" class="relative isolate mt-16 pb-24 px-4 md:px-6">
            <h2 class="font-bold-custom text-3xl md:text-4xl lg:text-5xl text-center text-blue-950 dark:text-white mb-12">
                Why Choose Us?
            </h2>

            <!-- Feature Cards Grid -->
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                <!-- Feature Card 1 -->
                <div class="feature-card frosted-card rounded-2xl p-6 md:p-8 hover:scale-105 transition-transform duration-300">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="font-bold-custom text-xl md:text-2xl text-blue-950 dark:text-white mb-3">Trusted Professionals</h3>
                    <p class="text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                        Our vetted and trained cleaning specialists ensure your space is in safe hands, delivering exceptional results every time.
                    </p>
                </div>

                <!-- Feature Card 2 -->
                <div class="feature-card frosted-card rounded-2xl p-6 md:p-8 hover:scale-105 transition-transform duration-300">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-leaf text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <h3 class="font-bold-custom text-xl md:text-2xl text-blue-950 dark:text-white mb-3">Eco-Friendly Solutions</h3>
                    <p class="text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                        We prioritize the environment by using sustainable products that are safe for your family, pets, and the planet.
                    </p>
                </div>

                <!-- Feature Card 3 -->
                <div class="feature-card frosted-card rounded-2xl p-6 md:p-8 hover:scale-105 transition-transform duration-300">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-check text-2xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <h3 class="font-bold-custom text-xl md:text-2xl text-blue-950 dark:text-white mb-3">Flexible Scheduling</h3>
                    <p class="text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                        Book services at your convenience with our easy-to-use scheduling system, designed to fit your busy lifestyle.
                    </p>
                </div>

                <!-- Feature Card 4 -->
                <div class="feature-card frosted-card rounded-2xl p-6 md:p-8 hover:scale-105 transition-transform duration-300">
                    <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-dollar-sign text-2xl text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <h3 class="font-bold-custom text-xl md:text-2xl text-blue-950 dark:text-white mb-3">Affordable Pricing</h3>
                    <p class="text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                        Quality cleaning doesn't have to break the bank. We offer competitive rates without compromising on service excellence.
                    </p>
                </div>

                <!-- Feature Card 5 -->
                <div class="feature-card frosted-card rounded-2xl p-6 md:p-8 hover:scale-105 transition-transform duration-300">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-star text-2xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <h3 class="font-bold-custom text-xl md:text-2xl text-blue-950 dark:text-white mb-3">Satisfaction Guaranteed</h3>
                    <p class="text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                        Your happiness is our priority. If you're not completely satisfied, we'll make it right—no questions asked.
                    </p>
                </div>

                <!-- Feature Card 6 -->
                <div class="feature-card frosted-card rounded-2xl p-6 md:p-8 hover:scale-105 transition-transform duration-300">
                    <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-sparkles text-2xl text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <h3 class="font-bold-custom text-xl md:text-2xl text-blue-950 dark:text-white mb-3">Deep Cleaning Experts</h3>
                    <p class="text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                        From carpets to corners, we tackle every detail with precision, leaving your space spotless and refreshed.
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div id="stats-container" class="relative isolate mt-16 pb-24 px-4 md:px-6">
            <div class="max-w-7xl mx-auto soft-glow-2 rounded-3xl p-8 md:p-12">
                <h2 class="font-bold-custom text-3xl md:text-4xl text-center text-blue-950 dark:text-white mb-12">
                    Our Impact in Numbers
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                    <!-- Stat 1 -->
                    <div class="text-center">
                        <div class="font-bold-custom text-4xl md:text-5xl lg:text-6xl text-blue-600 dark:text-blue-400 mb-2" data-target="1200">0</div>
                        <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300">Happy Clients</p>
                    </div>

                    <!-- Stat 2 -->
                    <div class="text-center">
                        <div class="font-bold-custom text-4xl md:text-5xl lg:text-6xl text-blue-600 dark:text-blue-400 mb-2" data-target="5000">0</div>
                        <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300">Cleaning Sessions</p>
                    </div>

                    <!-- Stat 3 -->
                    <div class="text-center">
                        <div class="font-bold-custom text-4xl md:text-5xl lg:text-6xl text-blue-600 dark:text-blue-400 mb-2" data-target="98">0</div>
                        <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300">Satisfaction Rate (%)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="relative isolate mt-16 pb-24 px-4 md:px-6">
            <div class="max-w-4xl mx-auto frosted-card rounded-3xl p-8 md:p-12 text-center">
                <h2 class="font-bold-custom text-3xl md:text-4xl text-blue-950 dark:text-white mb-6">
                    Ready to Experience Cleanliness Like Never Before?
                </h2>
                <p class="text-base md:text-lg text-gray-700 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                    Join thousands of satisfied customers who trust Fin-noys for their cleaning needs. Book your service today!
                </p>
                <a href="{{ route('quotation') }}"
                    class="inline-block rounded-full bg-blue-600 dark:bg-blue-500 px-8 py-4 text-base font-semibold text-white shadow-lg hover:bg-blue-500 dark:hover:bg-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400 transition-all">
                    Get Started Now
                </a>
            </div>
        </div>
    </div>

    <!-- Chatbot -->
    <div id="chat-container" class="fixed bottom-6 right-6 z-50">
        <!-- Chat Toggle Button -->
        <button id="toggle-chat" 
            class="w-16 h-16 bg-blue-600 dark:bg-blue-500 text-white rounded-full shadow-lg hover:bg-blue-500 dark:hover:bg-blue-400 transition-all flex items-center justify-center">
            <i class="fas fa-comments text-2xl"></i>
        </button>

        <!-- Chat Window -->
        <div id="chat-window" 
            class="hidden absolute bottom-20 right-0 w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <!-- Chat Header -->
            <div class="bg-blue-600 dark:bg-blue-500 text-white p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-robot text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold">Fin-noys Assistant</h3>
                        <p class="text-xs opacity-90">Online</p>
                    </div>
                </div>
                <button id="close-chat" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Chat Messages -->
            <div id="chat-messages" class="h-96 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900">
                <div class="assistant-message chat-message">
                    Hello! I'm the Fin-noys Assistant. How can I help you with our cleaning services today?
                </div>
            </div>

            <!-- Chat Input -->
            <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <div class="flex space-x-2">
                    <input type="text" id="user-input"
                        placeholder="Ask a question..."
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <button id="send-button"
                        class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-500 dark:hover:bg-blue-400 transition-colors">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Feature Cards Animation
            const featureCards = document.querySelectorAll('.feature-card');
            
            const cardObserverOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.2
            };

            const cardObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.remove('scroll-hidden');
                        entry.target.classList.add('scroll-visible');
                    }
                });
            }, cardObserverOptions);

            featureCards.forEach(card => {
                card.classList.add('scroll-hidden');
                cardObserver.observe(card);
            });

            // Hover Blur Effect
            featureCards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    featureCards.forEach(otherCard => {
                        if (otherCard !== card) {
                            otherCard.classList.add('blurred');
                        }
                    });
                });

                card.addEventListener('mouseleave', () => {
                    featureCards.forEach(otherCard => {
                        otherCard.classList.remove('blurred');
                    });
                });
            });

            // Counter Animation
            const counterElements = document.querySelectorAll('[data-target]');
            let counterAnimationTriggered = false;

            function animateCount(element) {
                const target = parseInt(element.getAttribute('data-target'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        element.textContent = target;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(current);
                    }
                }, 16);
            }

            const counterObserverOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.5
            };

            const counterObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !counterAnimationTriggered) {
                        counterElements.forEach(animateCount);
                        counterAnimationTriggered = true;
                        observer.unobserve(entry.target);
                    }
                });
            }, counterObserverOptions);

            const statsContainer = document.getElementById('stats-container');
            if (statsContainer) {
                counterObserver.observe(statsContainer);
            }

            // Chatbot Logic
            const chatWindow = document.getElementById('chat-window');
            const toggleButton = document.getElementById('toggle-chat');
            const closeButton = document.getElementById('close-chat');
            const messagesContainer = document.getElementById('chat-messages');
            const userInput = document.getElementById('user-input');
            const sendButton = document.getElementById('send-button');

            let chatHistory = [];
            const apiKey = "YOUR_GEMINI_API_KEY_HERE"; // Replace with your API key
            const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=${apiKey}`;
            const systemPrompt = "You are the Fin-noys Cleaning Service Assistant. Your primary goal is to answer questions about the company's services, provide cleaning quotes, and encourage booking. Keep responses friendly, professional, and concise. Do not answer questions unrelated to cleaning or Fin-noys services.";

            function scrollToBottom() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            function appendMessage(role, text) {
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('chat-message', role === 'user' ? 'user-message' : 'assistant-message');
                messageDiv.innerHTML = text.replace(/\n/g, '<br>');
                messagesContainer.appendChild(messageDiv);
                scrollToBottom();
            }

            async function getGeminiResponse(query) {
                chatHistory.push({ role: "user", parts: [{ text: query }] });

                try {
                    const payload = {
                        contents: chatHistory,
                        systemInstruction: { parts: [{ text: systemPrompt }] }
                    };

                    const maxRetries = 5;
                    let delay = 1000;

                    for (let i = 0; i < maxRetries; i++) {
                        const response = await fetch(apiUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });

                        if (response.ok) {
                            const result = await response.json();
                            const candidate = result.candidates?.[0];
                            const text = candidate?.content?.parts?.[0]?.text || "Sorry, I couldn't process that request.";
                            chatHistory.push({ role: "model", parts: [{ text: text }] });
                            return text;
                        } else if (response.status === 429) {
                            await new Promise(resolve => setTimeout(resolve, delay));
                            delay *= 2;
                        } else {
                            console.error("API error:", response.status, await response.text());
                            return "An error occurred while connecting to the assistant. Please try again.";
                        }
                    }
                    return "The assistant is busy right now. Please try again in a moment.";
                } catch (error) {
                    console.error("Fetch error:", error);
                    return "A network error occurred. Please check your connection.";
                }
            }

            async function sendMessage() {
                const query = userInput.value.trim();
                if (!query) return;

                appendMessage('user', query);
                userInput.value = '';
                sendButton.disabled = true;
                userInput.placeholder = "Assistant is typing...";

                const thinkingMessage = document.createElement('div');
                thinkingMessage.classList.add('assistant-message', 'chat-message', 'loading-indicator');
                thinkingMessage.innerHTML = '...';
                messagesContainer.appendChild(thinkingMessage);
                scrollToBottom();

                const responseText = await getGeminiResponse(query);
                messagesContainer.removeChild(thinkingMessage);
                appendMessage('assistant', responseText);

                sendButton.disabled = false;
                userInput.placeholder = "Ask a question...";
                userInput.focus();
            }

            toggleButton.addEventListener('click', () => {
                chatWindow.classList.toggle('hidden');
                if (!chatWindow.classList.contains('hidden')) {
                    userInput.focus();
                }
            });

            closeButton.addEventListener('click', () => {
                chatWindow.classList.add('hidden');
            });

            sendButton.addEventListener('click', sendMessage);

            userInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        });
    </script>
    @endpush

</x-layouts.general-landing>