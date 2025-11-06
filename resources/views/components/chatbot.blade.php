{{-- resources/views/components/chatbot.blade.php --}}

{{-- Chatbot Styles --}}
@push('styles')
    <style>
        /* Minimal custom styles - only what Tailwind can't do */
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

        .chat-message {
            animation: slideIn 0.3s ease-out;
        }

        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        #chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        #chat-messages::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 10px;
        }

        #chat-messages::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }

        .dark #chat-messages::-webkit-scrollbar-thumb {
            background: #4B5563;
        }

        @keyframes pulse-loading {
            0%, 100% {
                opacity: 0.7;
            }
            50% {
                opacity: 0.4;
            }
        }

        .loading-indicator {
            animation: pulse-loading 1.5s ease-in-out infinite;
        }

        @keyframes badgePulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        .unread-badge {
            animation: badgePulse 2s ease-in-out infinite;
        }
    </style>
@endpush

{{-- Chatbot HTML --}}
<div class="fixed bottom-6 right-6 z-50">
    <!-- Toggle Button (Floating Action Button) -->
    <button id="toggle-chat" type="button"
        class="relative flex items-center justify-center w-16 h-16 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white rounded-full shadow-2xl transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-700">
        <i class="fas fa-comment-dots text-xl"></i>
        <span id="unread-badge"
            class="unread-badge absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold border-2 border-white dark:border-gray-900 hidden">
            0
        </span>
    </button>

    <!-- Chat Window -->
    <div id="chat-window"
        class="hidden absolute bottom-20 right-0 w-96 h-[600px] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden transition-colors">

        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 text-white p-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Bot Icon -->
                <div class="w-10 h-10 flex items-center justify-center">
                    <img src="{{ asset('images/finnoys-text-logo.svg') }}" class="w-10 h-10" alt="Fin-noys">
                </div>
                <!-- Bot Info -->
                <div>
                    <h3 class="font-bold text-white text-base">Fin-noys Assistant</h3>
                    <p class="text-xs text-blue-100 flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1.5 animate-pulse"></span>
                        Online
                    </p>
                </div>
            </div>
            <!-- Header Actions -->
            <div class="flex items-center space-x-2">
                <button id="refresh-chat"
                    class="text-white hover:text-blue-100 transition p-2 rounded-full hover:bg-blue-500 dark:hover:bg-blue-600"
                    title="Refresh chat">
                    <i class="fas fa-redo text-sm"></i>
                </button>
                <button id="close-chat"
                    class="text-white hover:text-blue-100 transition p-2 rounded-full hover:bg-blue-500 dark:hover:bg-blue-600"
                    title="Close chat">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Date Separator -->
        <div class="px-4 py-2 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <p class="text-xs text-center text-gray-500 dark:text-gray-400" id="chat-date">
                {{ date('F j, Y') }}
            </p>
        </div>

        <!-- Chat Messages Container -->
        <div id="chat-messages" class="flex-1 overflow-y-auto py-4 bg-white dark:bg-gray-800 space-y-2">
            <!-- Welcome Message -->
            <div class="flex justify-start px-3">
                <div class="chat-message max-w-[75%] bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 text-sm shadow-sm break-words">
                    Hi there! 👋 I'm your Fin-noys cleaning assistant. How can I help you today?
                </div>
            </div>
        </div>

        <!-- Quick Reply Buttons (Dynamic) -->
        <div id="quick-replies" class="hidden px-4 py-2 flex flex-wrap gap-2 bg-white dark:bg-gray-800">
            <!-- Quick replies will be inserted here dynamically -->
        </div>

        <!-- Chat Input Area -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="flex items-center space-x-2">
                <!-- Input Field -->
                <input type="text" id="user-input" placeholder="Type a message..."
                    class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent text-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">

                <!-- Send Button -->
                <button id="send-button"
                    class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white p-2.5 rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center w-10 h-10">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Chatbot JavaScript --}}
@push('scripts')
    <script>
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) {
            console.error('CSRF token not found. Please add <meta name="csrf-token" content="{{ csrf_token() }}"> to your layout head section.');
        }

        // --- CHATBOT LOGIC ---
        const chatWindow = document.getElementById('chat-window');
        const toggleButton = document.getElementById('toggle-chat');
        const closeButton = document.getElementById('close-chat');
        const refreshButton = document.getElementById('refresh-chat');
        const messagesContainer = document.getElementById('chat-messages');
        const userInput = document.getElementById('user-input');
        const sendButton = document.getElementById('send-button');
        const unreadBadge = document.getElementById('unread-badge');

        // ========== CONVERSATION RETENTION WITH SESSIONSTORAGE ==========
        const STORAGE_KEY = 'opticrew_chat_history';
        const STORAGE_MESSAGES_KEY = 'opticrew_chat_messages';

        let chatHistory = [];
        let unreadCount = 0;
        let isChatOpen = false;
        const apiUrl = "/api/chatbot/message";

        // Load chat history from sessionStorage
        function loadChatFromStorage() {
            try {
                const stored = sessionStorage.getItem(STORAGE_KEY);
                const storedMessages = sessionStorage.getItem(STORAGE_MESSAGES_KEY);

                if (stored) {
                    chatHistory = JSON.parse(stored);
                }

                if (storedMessages) {
                    const messages = JSON.parse(storedMessages);
                    messages.forEach(msg => {
                        appendMessage(msg.role, msg.text, false);
                    });
                    scrollToBottom();
                }
            } catch (error) {
                console.error('Error loading chat from storage:', error);
            }
        }

        // Save chat history to sessionStorage
        function saveChatToStorage() {
            try {
                sessionStorage.setItem(STORAGE_KEY, JSON.stringify(chatHistory));

                const messages = [];
                const messageElements = messagesContainer.querySelectorAll('.chat-message');

                messageElements.forEach((el, index) => {
                    // Skip the welcome message (first message)
                    if (index === 0) return;

                    const isUser = el.classList.contains('bg-blue-600') || el.classList.contains('bg-[#5B5FEF]');
                    const role = isUser ? 'user' : 'assistant';
                    const text = el.textContent.trim();
                    messages.push({ role, text });
                });

                sessionStorage.setItem(STORAGE_MESSAGES_KEY, JSON.stringify(messages));
            } catch (error) {
                console.error('Error saving chat to storage:', error);
            }
        }

        // Clear chat storage
        window.clearChatStorage = function() {
            sessionStorage.removeItem(STORAGE_KEY);
            sessionStorage.removeItem(STORAGE_MESSAGES_KEY);
            chatHistory = [];

            // Clear all messages except welcome message
            const firstMessage = messagesContainer.firstElementChild;
            messagesContainer.innerHTML = '';
            messagesContainer.appendChild(firstMessage);

            console.log('Chat history cleared');
        };

        // Scroll to bottom
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
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

        // Increment unread count
        function incrementUnread() {
            if (!isChatOpen) {
                unreadCount++;
                updateUnreadBadge();
            }
        }

        // Clear unread count
        function clearUnread() {
            unreadCount = 0;
            updateUnreadBadge();
        }

        // Append message to chat UI
        function appendMessage(role, text, saveToStorage = true) {
            const messageWrapper = document.createElement('div');
            const isUser = role === 'user';

            // Container with proper alignment
            messageWrapper.className = `flex ${isUser ? 'justify-end' : 'justify-start'} px-3`;

            // Message bubble
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message max-w-[75%] rounded-2xl px-4 py-3 text-sm shadow-sm break-words whitespace-pre-wrap ${
                isUser
                    ? 'bg-[#5B5FEF] text-white rounded-br-sm'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-bl-sm'
            }`;
            messageDiv.textContent = text;

            messageWrapper.appendChild(messageDiv);
            messagesContainer.appendChild(messageWrapper);

            scrollToBottom();

            if (saveToStorage) {
                saveChatToStorage();
            }
        }

        // Handle API call to Laravel backend
        async function getAssistantResponse(query) {
            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: query,
                        chat_history: chatHistory
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    chatHistory = result.chat_history || chatHistory;

                    chatHistory.push({
                        role: "model",
                        parts: [{ text: result.message }]
                    });

                    saveChatToStorage();
                    return result.message;
                } else {
                    console.error("API error:", result.message);
                    return result.message || "Sorry, I couldn't process that request.";
                }

            } catch (error) {
                console.error("Fetch error:", error);
                return "A network error occurred. Please check your connection and try again.";
            }
        }

        // Handle sending a message
        async function sendMessage() {
            const query = userInput.value.trim();
            if (!query) return;

            // Add user message to chat history
            chatHistory.push({
                role: "user",
                parts: [{ text: query }]
            });

            // Display user message
            appendMessage('user', query);
            userInput.value = '';
            sendButton.disabled = true;
            userInput.disabled = true;
            userInput.placeholder = "Typing...";

            // Display loading indicator
            const loadingWrapper = document.createElement('div');
            loadingWrapper.className = 'flex justify-start px-3 loading-container';

            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'chat-message max-w-[75%] bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 text-sm shadow-sm loading-indicator opacity-70';
            loadingDiv.textContent = 'Typing...';

            loadingWrapper.appendChild(loadingDiv);
            messagesContainer.appendChild(loadingWrapper);
            scrollToBottom();

            // Get response from backend
            const responseText = await getAssistantResponse(query);

            // Remove loading indicator
            messagesContainer.removeChild(loadingWrapper);

            // Display assistant response
            appendMessage('assistant', responseText);

            // Increment unread count if chat is closed
            incrementUnread();

            // Re-enable input
            sendButton.disabled = false;
            userInput.disabled = false;
            userInput.placeholder = "Type a message...";
            userInput.focus();
        }

        // Event Listeners
        toggleButton.addEventListener('click', () => {
            chatWindow.classList.toggle('hidden');
            isChatOpen = !chatWindow.classList.contains('hidden');

            if (isChatOpen) {
                clearUnread();
                userInput.focus();
            }
        });

        closeButton.addEventListener('click', () => {
            chatWindow.classList.add('hidden');
            isChatOpen = false;
        });

        refreshButton.addEventListener('click', () => {
            if (confirm('Are you sure you want to clear the chat history?')) {
                window.clearChatStorage();
            }
        });

        sendButton.addEventListener('click', sendMessage);

        userInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Initialize: Load chat history on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadChatFromStorage();
        });
    </script>
@endpush
