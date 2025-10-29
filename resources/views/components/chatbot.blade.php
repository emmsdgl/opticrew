<!-- Chatbot Toggle Button -->
<button id="toggle-chat"
    class="fixed bottom-6 right-6 z-[9999] bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition-all duration-300 hover:scale-110">
    <i class="fa-solid fa-comment text-2xl"></i>
    <!-- Unread Badge -->
    <span id="unread-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center animate-pulse">
        0
    </span>
</button>

<!-- Chatbot Window -->
<div id="chat-window"
    class="hidden fixed bottom-24 right-6 z-[9999] w-96 h-[500px] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-200">
    <!-- Chat Header -->
    <div class="bg-blue-500 text-white p-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                <i class="fa-solid fa-robot text-blue-500"></i>
            </div>
            <div>
                <h3 class="font-semibold text-white">Fin-noys Assistant</h3>
                <p class="text-xs text-blue-100">Always here to help</p>
            </div>
        </div>
        <button id="close-chat" class="hover:bg-blue-600 p-2 rounded-full transition">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>

    <!-- Chat Messages Container -->
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
        <!-- Messages will be loaded dynamically -->
    </div>

    <!-- Chat Input -->
    <div class="p-4 border-t border-gray-200 bg-white">
        <div class="flex gap-2">
            <input type="text" id="user-input" placeholder="Ask a question..."
                class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <button id="send-button"
                class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
    /* Ensure chatbot is always positioned correctly */
    #toggle-chat {
        position: fixed !important;
        bottom: 1.5rem !important;
        right: 1.5rem !important;
    }

    #chat-window {
        position: fixed !important;
        bottom: 6rem !important;
        right: 1.5rem !important;
    }

    .chat-message {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .user-message {
        background-color: #3B82F6;
        color: white;
        margin-left: auto;
        padding: 12px;
        border-radius: 12px;
        max-width: 80%;
    }

    .assistant-message {
        background-color: #DBEAFE;
        color: #071957;
        padding: 12px;
        border-radius: 12px;
        max-width: 80%;
    }

    .assistant-message strong {
        font-weight: 700;
        color: #1E40AF;
    }

    .user-message strong {
        font-weight: 700;
    }

    .assistant-message em {
        font-style: italic;
    }

    .assistant-message code {
        font-family: monospace;
        font-size: 0.9em;
    }

    .assistant-message a {
        color: #2563EB;
        text-decoration: underline;
        font-weight: 600;
    }

    .assistant-message a:hover {
        color: #1E40AF;
    }

    .loading-indicator {
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    #chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    #chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #chat-messages::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<script type="module">
    // Chatbot Logic
    const chatWindow = document.getElementById('chat-window');
    const toggleButton = document.getElementById('toggle-chat');
    const closeButton = document.getElementById('close-chat');
    const messagesContainer = document.getElementById('chat-messages');
    const userInput = document.getElementById('user-input');
    const sendButton = document.getElementById('send-button');
    const unreadBadge = document.getElementById('unread-badge');

    let chatHistory = [];
    let displayedMessages = [];
    let unreadCount = 0;
    let isChatOpen = false;
    const apiKey = "{{ config('services.gemini.api_key', '') }}";
    const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-05-20:generateContent?key=${apiKey}`;
    const systemPrompt = `You are the Fin-noys Cleaning Service Assistant. Your primary goal is to answer general questions about the company's services and help users navigate the website.

IMPORTANT RULES:
1. **Price Quotations**: When users ask about pricing, rates, or cost estimates, ALWAYS direct them to visit the Price Quotation page at {{ route('quotation') }}. Say: "For accurate pricing and personalized quotes, please visit our Price Quotation page: {{ route('quotation') }}"

2. **Booking Appointments**: You CANNOT create actual bookings. When users want to book a service, direct them to: "To book an appointment, please log in to your account at {{ route('login') }} or contact us directly."

3. **General Information**: You can answer questions about:
   - Types of cleaning services offered (residential, commercial, deep cleaning, etc.)
   - Service areas and availability
   - General company information
   - How to navigate the website

Keep responses friendly, professional, and concise. Do not answer questions unrelated to cleaning or Fin-noys services. NEVER pretend to book appointments or provide specific pricing - always redirect to the appropriate pages.`;

    // Load chat history from localStorage
    function loadChatHistory() {
        const saved = localStorage.getItem('finnoys_chat_history');
        const savedMessages = localStorage.getItem('finnoys_chat_messages');
        const savedUnread = localStorage.getItem('finnoys_unread_count');

        if (saved) {
            chatHistory = JSON.parse(saved);
        }

        if (savedMessages) {
            displayedMessages = JSON.parse(savedMessages);
            // Clear existing messages except welcome message
            messagesContainer.innerHTML = '';
            // Restore all messages
            displayedMessages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('chat-message', msg.role === 'user' ? 'user-message' : 'assistant-message');
                messageDiv.innerHTML = markdownToHtml(msg.text);
                messagesContainer.appendChild(messageDiv);
            });
            scrollToBottom();
        }

        if (savedUnread) {
            unreadCount = parseInt(savedUnread);
            updateUnreadBadge();
        }
    }

    // Save chat history to localStorage
    function saveChatHistory() {
        localStorage.setItem('finnoys_chat_history', JSON.stringify(chatHistory));
        localStorage.setItem('finnoys_chat_messages', JSON.stringify(displayedMessages));
        localStorage.setItem('finnoys_unread_count', unreadCount.toString());
    }

    // Update unread badge
    function updateUnreadBadge() {
        if (unreadCount > 0) {
            unreadBadge.textContent = unreadCount;
            unreadBadge.classList.remove('hidden');
        } else {
            unreadBadge.classList.add('hidden');
        }
    }

    // Clear unread count
    function clearUnreadCount() {
        unreadCount = 0;
        updateUnreadBadge();
        saveChatHistory();
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function markdownToHtml(text) {
        // Convert markdown to HTML
        return text
            // Bold: **text** or __text__
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/__(.+?)__/g, '<strong>$1</strong>')
            // Italic: *text* or _text_ (but not if already in bold)
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/_(.+?)_/g, '<em>$1</em>')
            // Links: [text](url)
            .replace(/\[([^\]]+)\]\(([^\)]+)\)/g, '<a href="$2" target="_blank" class="underline">$1</a>')
            // Inline code: `text`
            .replace(/`([^`]+)`/g, '<code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">$1</code>')
            // Line breaks
            .replace(/\n/g, '<br>');
    }

    function appendMessage(role, text) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('chat-message', role === 'user' ? 'user-message' : 'assistant-message');
        messageDiv.innerHTML = markdownToHtml(text);
        messagesContainer.appendChild(messageDiv);

        // Save to displayed messages
        displayedMessages.push({ role, text });

        // If assistant message and chat is closed, increment unread
        if (role === 'assistant' && !isChatOpen) {
            unreadCount++;
            updateUnreadBadge();
        }

        saveChatHistory();
        scrollToBottom();
    }

    async function getGeminiResponse(query) {
        chatHistory.push({ role: "user", parts: [{ text: query }] });

        try {
            const payload = {
                contents: chatHistory,
                systemInstruction: { parts: [{ text: systemPrompt }] },
                tools: [{ "google_search": {} }],
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
        isChatOpen = !chatWindow.classList.contains('hidden');

        if (isChatOpen) {
            userInput.focus();
            clearUnreadCount();
        }
    });

    closeButton.addEventListener('click', () => {
        chatWindow.classList.add('hidden');
        isChatOpen = false;
    });

    sendButton.addEventListener('click', sendMessage);

    userInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Initialize chat on page load
    window.addEventListener('DOMContentLoaded', () => {
        loadChatHistory();

        // If no saved messages, add welcome message to displayedMessages
        if (displayedMessages.length === 0) {
            const welcomeMsg = {
                role: 'assistant',
                text: "Hello! I'm your Fin-noys cleaning assistant. I can help answer general questions about our services. For pricing quotes, visit our <a href=\"{{ route('quotation') }}\" class=\"underline font-semibold\">Price Quotation page</a>. To book an appointment, please <a href=\"{{ route('login') }}\" class=\"underline font-semibold\">log in</a>. How can I help you today?"
            };
            displayedMessages.push(welcomeMsg);
            saveChatHistory();
        }
    });
</script>