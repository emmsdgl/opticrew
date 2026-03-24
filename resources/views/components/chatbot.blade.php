{{-- resources/views/components/chatbot.blade.php --}}

{{-- Chatbot Styles --}}
@push('styles')
    <style>
        /* Animations */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes msgIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes dotPulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 0.3; }
        }
        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .chat-window-anim {
            animation: slideUp 0.3s ease-out;
        }
        .chat-message {
            animation: msgIn 0.25s ease-out;
        }
        .loading-indicator {
            animation: dotPulse 1.2s ease-in-out infinite;
        }
        .unread-badge {
            animation: badgePulse 2s ease-in-out infinite;
        }
        .float-slow {
            animation: floatSlow 3s ease-in-out infinite;
        }

        /* Scrollbar */
        #chat-messages::-webkit-scrollbar { width: 4px; }
        #chat-messages::-webkit-scrollbar-track { background: transparent; }
        #chat-messages::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .dark #chat-messages::-webkit-scrollbar-thumb { background: #4b5563; }

        /* Chat window responsive sizing */
        .chatbot-window {
            position: absolute;
            bottom: 4rem;
            right: 0;
            width: min(380px, calc(100vw - 2rem));
            height: min(540px, calc(100vh - 8rem));
            z-index: 9999;
        }
    </style>
@endpush

{{-- Chatbot HTML --}}
<div class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50" x-data="chatbot()" x-init="init()">

    <!-- Toggle FAB -->
    <button @click="toggleChat()" type="button"
        class="relative flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-full shadow-lg transition-all duration-200 hover:scale-105 focus:outline-none"
        style="background: linear-gradient(135deg, #0084ff 0%, #0066ff 100%); box-shadow: 0 4px 14px rgba(0, 102, 255, 0.4);">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="white">
            <path d="M12 2C6.477 2 2 5.813 2 10.5c0 2.65 1.33 5.02 3.42 6.61V22l4.29-2.36c.74.13 1.5.2 2.29.2 5.523 0 10-3.813 10-8.5S17.523 2 12 2zm1.1 11.44L10.83 11 6.2 13.72l5.1-5.44 2.27 2.44 4.63-2.72-5.1 5.44z"/>
        </svg>
        <span x-show="unreadCount > 0" x-text="unreadCount"
            class="unread-badge absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold border-2 border-white dark:border-gray-900">
        </span>
    </button>

    <!-- Chat Window -->
    <div x-show="isOpen" x-cloak x-transition @click.outside="isOpen = false"
        class="chatbot-window chat-window-anim rounded-2xl overflow-hidden shadow-2xl flex flex-col bg-white dark:bg-gray-800"
        style="box-shadow: 0 8px 30px rgba(0,0,0,0.15), 0 2px 8px rgba(0,0,0,0.08);">

        <!-- Header -->
        <div class="flex items-center justify-between px-3 py-2.5 sm:px-4 sm:py-3 flex-shrink-0"
            style="background: linear-gradient(135deg, #0061ff 0%, #0a7cff 50%, #59a6ff 100%);">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/icons/opticrew-logo.svg') }}"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border-2 border-white/30 object-cover" alt="Fin-noys">
                <div>
                    <h3 class="text-white font-semibold text-xs sm:text-sm leading-tight">Fin-noys Assistant</h3>
                    <p class="text-[10px] sm:text-xs text-white/75 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span> Online
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button @click="refreshChat()"
                    class="w-7 h-7 rounded-full flex items-center justify-center text-white/80 hover:text-white hover:bg-white/15 transition" title="Clear chat">
                    <i class="fas fa-redo text-[10px]"></i>
                </button>
                <button @click="isOpen = false"
                    class="w-7 h-7 rounded-full flex items-center justify-center text-white/80 hover:text-white hover:bg-white/15 transition" title="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Welcome Screen (shown when no conversation yet) -->
        <div x-show="!hasConversation" class="flex-1 flex flex-col items-center justify-center bg-white dark:bg-gray-800 px-6 py-8 text-center overflow-y-auto" style="min-height: 0;">
            <!-- Logo with float animation -->
            <div class="float-slow mb-5">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 flex items-center justify-center shadow-lg shadow-blue-100 dark:shadow-blue-900/20">
                    <img src="{{ asset('images/icons/opticrew-logo.svg') }}" class="w-12 h-12 sm:w-14 sm:h-14" alt="Fin-noys">
                </div>
            </div>

            <p class="text-gray-400 dark:text-gray-500 text-[10px] sm:text-xs uppercase tracking-widest mb-2">Welcome to</p>
            <h2 class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">Fin-noys Assistant</h2>
            <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm leading-relaxed mb-6 max-w-[240px]">
                Your cleaning service assistant. Ask me anything about our services!
            </p>

            <!-- Quick Start Buttons -->
            <div class="flex flex-col gap-2 w-full max-w-[220px]">
                <button @click="quickSend('What services do you offer?')"
                    class="w-full px-4 py-2 text-xs sm:text-sm rounded-full border border-blue-200 dark:border-blue-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                    Our Services
                </button>
                <button @click="quickSend('How do I book an appointment?')"
                    class="w-full px-4 py-2 text-xs sm:text-sm rounded-full border border-blue-200 dark:border-blue-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                    Book Appointment
                </button>
                <button @click="quickSend('What are your prices?')"
                    class="w-full px-4 py-2 text-xs sm:text-sm rounded-full border border-blue-200 dark:border-blue-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                    Pricing Info
                </button>
                <button @click="quickSend('How can I contact you?')"
                    class="w-full px-4 py-2 text-xs sm:text-sm rounded-full border border-blue-200 dark:border-blue-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                    Contact Us
                </button>
            </div>
        </div>

        <!-- Chat Messages (shown when conversation started) -->
        <div x-show="hasConversation" id="chat-messages"
            class="flex-1 overflow-y-auto py-3 bg-gray-50 dark:bg-gray-800 space-y-1.5"
            style="min-height: 0;">
        </div>

        <!-- Input Area (always visible) -->
        <div class="px-2.5 py-2 sm:px-3 sm:py-2.5 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded-full px-3 sm:px-3.5 py-0.5">
                <input type="text" x-ref="chatInput"
                    x-model="inputText"
                    @keydown.enter.prevent="sendMessage()"
                    placeholder="Type a message..."
                    class="flex-1 bg-transparent border-none outline-none text-xs sm:text-sm text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 py-1.5 sm:py-2">
                <button @click="sendMessage()" :disabled="isSending || !inputText.trim()"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center transition-all duration-200 flex-shrink-0"
                    :class="inputText.trim() && !isSending
                        ? 'bg-blue-500 hover:bg-blue-600 text-white cursor-pointer'
                        : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'">
                    <i class="fas fa-paper-plane text-[10px] sm:text-xs"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Chatbot JavaScript --}}
@push('scripts')
    <script>
        function chatbot() {
            return {
                isOpen: false,
                hasConversation: false,
                inputText: '',
                isSending: false,
                unreadCount: 0,
                chatHistory: [],
                apiUrl: '/api/chatbot/message',

                init() {
                    this.loadFromStorage();
                },

                toggleChat() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.unreadCount = 0;
                        this.$nextTick(() => {
                            if (this.$refs.chatInput) this.$refs.chatInput.focus();
                            this.scrollToBottom();
                        });
                    }
                },

                quickSend(text) {
                    this.inputText = text;
                    this.sendMessage();
                },

                async sendMessage() {
                    const query = this.inputText.trim();
                    if (!query || this.isSending) return;

                    // Switch to conversation view
                    this.hasConversation = true;
                    this.inputText = '';
                    this.isSending = true;

                    await this.$nextTick();

                    // Add user message
                    this.chatHistory.push({ role: 'user', parts: [{ text: query }] });
                    this.appendBubble('user', query);

                    // Show typing indicator
                    const loader = this.appendBubble('assistant', 'Typing...', true);

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        const res = await fetch(this.apiUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken || ''
                            },
                            body: JSON.stringify({ message: query, chat_history: this.chatHistory })
                        });

                        const data = await res.json();
                        loader.remove();

                        if (data.success) {
                            this.chatHistory = data.chat_history || this.chatHistory;
                            this.chatHistory.push({ role: 'model', parts: [{ text: data.message }] });
                            this.appendBubble('assistant', data.message);
                        } else {
                            this.appendBubble('assistant', data.message || 'Sorry, something went wrong.');
                        }
                    } catch (e) {
                        loader.remove();
                        this.appendBubble('assistant', 'Network error. Please try again.');
                    }

                    this.isSending = false;
                    this.saveToStorage();

                    if (!this.isOpen) this.unreadCount++;

                    this.$nextTick(() => {
                        if (this.$refs.chatInput) this.$refs.chatInput.focus();
                    });
                },

                appendBubble(role, text, isLoading = false) {
                    const container = document.getElementById('chat-messages');
                    if (!container) return null;

                    const wrapper = document.createElement('div');
                    const isUser = role === 'user';
                    wrapper.className = `flex ${isUser ? 'justify-end' : 'justify-start'} px-3`;

                    const bubble = document.createElement('div');
                    bubble.className = `chat-message max-w-[78%] px-3 py-2 text-xs sm:text-[0.8125rem] leading-relaxed break-words ${
                        isUser
                            ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-[1.125rem] rounded-br-[0.25rem]'
                            : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-[1.125rem] rounded-bl-[0.25rem] shadow-sm'
                    }`;

                    if (isLoading) bubble.classList.add('loading-indicator');
                    bubble.dataset.rawText = text;
                    bubble.innerHTML = this.formatText(text);

                    wrapper.appendChild(bubble);
                    container.appendChild(wrapper);
                    this.scrollToBottom();

                    return wrapper;
                },

                formatText(text) {
                    return text
                        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.+?)\*/g, '<em>$1</em>')
                        .replace(/^[\*\-]\s+(.+)/gm, '<span class="flex"><span class="mr-1.5">&bull;</span><span>$1</span></span>')
                        .replace(/\n/g, '<br>');
                },

                scrollToBottom() {
                    const el = document.getElementById('chat-messages');
                    if (el) el.scrollTop = el.scrollHeight;
                },

                refreshChat() {
                    if (!confirm('Clear chat history?')) return;
                    this.chatHistory = [];
                    this.hasConversation = false;
                    const container = document.getElementById('chat-messages');
                    if (container) container.innerHTML = '';
                    sessionStorage.removeItem('opticrew_chat_history');
                    sessionStorage.removeItem('opticrew_chat_messages');
                },

                saveToStorage() {
                    try {
                        sessionStorage.setItem('opticrew_chat_history', JSON.stringify(this.chatHistory));
                        const container = document.getElementById('chat-messages');
                        if (!container) return;
                        const msgs = [];
                        container.querySelectorAll('.chat-message').forEach(el => {
                            const isUser = el.classList.contains('from-blue-500');
                            msgs.push({ role: isUser ? 'user' : 'assistant', text: el.dataset.rawText || el.textContent.trim() });
                        });
                        sessionStorage.setItem('opticrew_chat_messages', JSON.stringify(msgs));
                    } catch (e) {}
                },

                loadFromStorage() {
                    try {
                        const stored = sessionStorage.getItem('opticrew_chat_history');
                        const storedMsgs = sessionStorage.getItem('opticrew_chat_messages');

                        if (stored) this.chatHistory = JSON.parse(stored);
                        if (storedMsgs) {
                            const messages = JSON.parse(storedMsgs);
                            if (messages.length > 0) {
                                this.hasConversation = true;
                                this.$nextTick(() => {
                                    messages.forEach(msg => this.appendBubble(msg.role, msg.text));
                                    this.scrollToBottom();
                                });
                            }
                        }
                    } catch (e) {}
                }
            };
        }
    </script>
@endpush
