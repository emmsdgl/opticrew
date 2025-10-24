<!-- Chatbot Toggle Button -->
<button id="toggle-chat"
    class="fixed bottom-6 right-6 z-50 bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition-all duration-300 hover:scale-110">
    <i class="fa-solid fa-comment text-2xl"></i>
</button>

<!-- Chatbot Window -->
<div id="chat-window"
    class="hidden fixed bottom-24 right-6 z-50 w-96 h-[500px] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-200">
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
        <div class="chat-message assistant-message bg-blue-100 text-blue-950 p-3 rounded-lg max-w-[80%]">
            Hello! I'm your Fin-noys cleaning assistant. How can I help you today?
        </div>
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

    let chatHistory = [];
    const apiKey = "{{ config('services.gemini.api_key', '') }}";
    const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-05-20:generateContent?key=${apiKey}`;
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
</script>