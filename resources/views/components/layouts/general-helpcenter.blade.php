@props([
    'faqs' => [], 
    'title' => 'Hi, How can we help you?',
    'supportPhone' => '+2355555888'
])

<div class="flex flex-row w-full gap-6 p-4 md:p-6 h-fit" 
    x-data="{
        openFaq: null,
        userInput: '',
        messages: [],
        expandedFaq: null,
        faqs: {{ json_encode($faqs) }},

        get filteredFaqs() {
            if (!this.userInput.trim()) {
                return this.faqs.filter(faq => faq.quick);
            }
            const query = this.userInput.toLowerCase();
            return this.faqs.filter(faq =>
                faq.quick && (
                    faq.question.toLowerCase().includes(query) ||
                    faq.keywords.some(k => query.includes(k))
                )
            );
        },

        clearMessages() {
            this.messages = [];
            this.expandedFaq = null;
        },

        selectPrompt(prompt) {
            this.userInput = prompt;
            this.sendMessage();
        },

        sendMessage() {
            if (!this.userInput.trim()) return;
            const queryText = this.userInput;
            this.messages.push({ type: 'user', text: queryText });
            const query = queryText.toLowerCase();

            const matchingFaqs = this.faqs.filter(faq =>
                faq.keywords.some(k => query.includes(k)) ||
                faq.question.toLowerCase().includes(query)
            );

            this.messages.push({
                type: 'bot',
                text: matchingFaqs.length
                    ? 'I found some relevant information for you:'
                    : 'I couldn’t find specific information. Try rephrasing or call support at {{ $supportPhone }}.',
                faqs: matchingFaqs.slice(0, 3)
            });

            this.userInput = '';
            setTimeout(() => {
                const container = document.getElementById('chat-container');
                if (container) container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            }, 100);
        },

        expandFaq(faq) { this.expandedFaq = faq; },
        get chatPairsCount() { return this.messages.filter(m => m.type === 'user').length; }
    }">

    <div class="w-full max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Customer Service</div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-6">We are available 24/7 to answer your inquiries.</p>

            <div class="mb-8 max-w-3xl">
                {{-- Clear Messages --}}
                <button @click="clearMessages()" x-show="messages.length > 0" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-red-500 mb-6 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    <span>Clear messages</span>
                </button>

                {{-- Chat Container --}}
                <div id="chat-container" class="space-y-4 mb-6 transition-all duration-300" :class="chatPairsCount > 1 ? 'max-h-[300px] w-full overflow-y-auto' : 'overflow-visible'" x-show="messages.length > 0">
                    <template x-for="(message, index) in messages" :key="index">
                        <div>
                            <div x-show="message.type === 'user'" class="flex justify-end mb-4">
                                <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 max-w-[80%] shadow-sm">
                                    <p class="text-sm" x-text="message.text"></p>
                                </div>
                            </div>
                            <div x-show="message.type === 'bot'" class="flex gap-3 mb-4">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                                </div>
                                <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-tl-sm px-4 py-3 max-w-[80%]">
                                    <p class="text-sm text-gray-700 dark:text-gray-300" x-text="message.text"></p>
                                    <div x-show="message.faqs && message.faqs.length > 0" class="mt-3 space-y-2">
                                        <template x-for="faq in message.faqs" :key="faq.id">
                                            <button @click="expandFaq(faq)" class="block w-full text-left text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                                • <span x-text="faq.question"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Expanded FAQ Display --}}
                <div x-show="expandedFaq" x-transition class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-100 dark:border-indigo-800 p-4 mb-6">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold text-indigo-900 dark:text-indigo-200 text-sm" x-text="expandedFaq?.question"></h3>
                        <button @click="expandedFaq = null" class="text-indigo-400 hover:text-indigo-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300" x-text="expandedFaq?.answer"></p>
                </div>

                {{-- Suggested Prompts --}}
                <div class="mb-4" x-show="messages.length === 0">
                    <p class="text-xs text-gray-500 mb-2 ml-1">Suggested questions:</p>
                    <div class="flex flex-wrap gap-2">
                        {{-- Here we show any FAQ marked as 'quick' --}}
                        <template x-for="faq in faqs.filter(f => f.quick).slice(0, 4)" :key="'suggest-'+faq.id">
                            <button @click="selectPrompt(faq.question)" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-xs text-gray-600 dark:text-gray-300 hover:border-indigo-500 hover:text-indigo-600 transition-all shadow-sm">
                                <span x-text="faq.question"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Input Section --}}
                <div class="relative">
                    <div class="flex items-center gap-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-300 dark:border-gray-600 p-2 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                        <input type="text" x-model="userInput" @keydown.enter="sendMessage" placeholder="Ask whatever you want..." class="flex-1 px-4 py-2 text-sm text-gray-900 dark:text-white bg-transparent focus:outline-none">
                        <button @click="sendMessage" :disabled="!userInput.trim()" :class="userInput.trim() ? 'bg-indigo-600' : 'bg-gray-300'" class="p-2 rounded-lg text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Accordion Section --}}
            <div class="mb-8 max-w-3xl">
                <h2 class="text-base font-medium text-gray-900 dark:text-white mb-4">Quick Answer Questions</h2>
                <div class="space-y-3">
                    <template x-for="faq in filteredFaqs" :key="'dropdown-'+faq.id">
                        <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm">
                            <button @click="openFaq = (openFaq === faq.id ? null : faq.id)" class="w-full flex items-center justify-between p-4 text-left bg-white dark:bg-gray-900 transition-colors">
                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="faq.question"></span>
                                <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': openFaq === faq.id}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="openFaq === faq.id" x-collapse>
                                <div class="p-4 pt-0 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-900">
                                    <p x-text="faq.answer"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-none max-w-3xl p-4 border-t border-gray-100 dark:border-gray-700">
                <div class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Still have questions?</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Contact us: <span class="font-bold text-indigo-600">{{ $supportPhone }}</span></div>
            </div>
        </div>
    </div>
</div>