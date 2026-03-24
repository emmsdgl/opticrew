{{-- Landing Page Chatbot - Pure Tailwind --}}

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    @keyframes cb-float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-5px)} }
    @keyframes cb-dots { 0%,100%{opacity:.5} 50%{opacity:.2} }
    @keyframes cb-msg-in { from{opacity:0;transform:translateY(8px) scale(0.97)} to{opacity:1;transform:translateY(0) scale(1)} }
    @keyframes cb-msg-sent { 0%{transform:scale(0.95);opacity:0.5} 50%{transform:scale(1.02)} 100%{transform:scale(1);opacity:1} }
    .cb-float { animation: cb-float 3s ease-in-out infinite; }
    .cb-typing { animation: cb-dots 1.2s ease-in-out infinite; }
    .cb-msg { animation: cb-msg-in 0.35s cubic-bezier(0.16,1,0.3,1); }
    .cb-msg-user { animation: cb-msg-sent 0.4s cubic-bezier(0.16,1,0.3,1); }
</style>
@endpush

<div class="fixed bottom-4 right-3 sm:bottom-5 sm:right-5 z-50"
     x-data="landingChatbot()" x-init="init()">

    {{-- FAB Button --}}
    <button @click="toggle()" x-show="!open"
            x-transition:enter="transition ease-out duration-200 delay-150"
            x-transition:enter-start="opacity-0 scale-75"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-75"
            class="w-14 h-14 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-700 relative cursor-pointer border-none">
        <i class="fas fa-comment-dots text-white text-xl"></i>
        <span x-show="unread > 0" x-text="unread"
              class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold border-2 border-white dark:border-gray-900"></span>
    </button>

    {{-- Chat Panel --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="open = false"
         class="absolute bottom-0 right-0 w-[300px] h-[460px] lg:w-[320px] lg:h-[500px] max-h-[calc(100vh-100px)] max-sm:fixed max-sm:inset-0 max-sm:w-full max-sm:h-full max-sm:max-h-none max-sm:rounded-none rounded-xl lg:rounded-2xl overflow-hidden shadow-2xl flex flex-col bg-white dark:bg-gray-900 z-[100]">

        {{-- Header --}}
        <div class="flex items-center justify-between px-3 py-2.5 sm:px-4 sm:py-3 shrink-0 bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/icons/castcrew/castcrew-pic-logo-ondark.svg') }}"
                     class="w-7 h-7 rounded-full" alt="">
                <div class="leading-none">
                    <p class="text-white text-sm sm:text-xs lg:text-sm font-bold">Fin-noys Assistant</p>
                    <p class="text-white/70 text-[10px] flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span> Online
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button @click="clearChat()" class="w-7 h-7 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/15 transition" title="Clear">
                    <i class="fas fa-redo text-[9px]"></i>
                </button>
                <button @click="open = false" class="w-7 h-7 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/15 transition" title="Close">
                    <i class="fas fa-times text-[11px]"></i>
                </button>
            </div>
        </div>

        {{-- Welcome Screen --}}
        <div x-show="!started" class="flex-1 min-h-0 flex flex-col items-center justify-center px-5 py-6 text-center overflow-y-auto bg-white dark:bg-gray-900">
            <div class="cb-float mb-4">
                <div class="w-16 h-16 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <img src="{{ asset('images/icons/opticrew-logo.svg') }}" class="w-9 h-9" alt="">
                </div>
            </div>
            <p class="text-gray-400 dark:text-gray-500 text-[9px] uppercase tracking-widest mb-1">Welcome to</p>
            <h2 class="text-base font-bold text-blue-600 dark:text-blue-400 mb-1">Fin-noys Assistant</h2>
            <p class="text-gray-400 dark:text-gray-500 text-[11px] leading-relaxed mb-4 max-w-[200px]">Ask me anything about our cleaning services!</p>
            <div class="flex flex-col gap-1.5 w-full max-w-[190px]">
                <button @click="send('What services do you offer?')" class="w-full py-1.5 text-[11px] rounded-full border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">Our Services</button>
                <button @click="send('How do I book an appointment?')" class="w-full py-1.5 text-[11px] rounded-full border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">Book Appointment</button>
                <button @click="send('What are your prices?')" class="w-full py-1.5 text-[11px] rounded-full border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">Pricing Info</button>
                <button @click="send('How can I contact you?')" class="w-full py-1.5 text-[11px] rounded-full border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">Contact Us</button>
            </div>
        </div>

        {{-- Messages --}}
        <div x-show="started" x-ref="msgBox"
             class="flex-1 min-h-0 overflow-y-auto py-2.5 space-y-1 bg-gray-50 dark:bg-gray-800 [&::-webkit-scrollbar]:w-[3px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded dark:[&::-webkit-scrollbar-thumb]:bg-gray-600">
        </div>

        {{-- Input --}}
        <div class="px-2.5 py-2 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 shrink-0">
            <div class="flex items-center gap-1.5 bg-blue-50 my-4 dark:bg-transparent rounded-full pl-3 pr-1 py-0.5">
                <input type="text" x-ref="input" x-model="text"
                       @keydown.enter.prevent="send()"
                       placeholder="Type a message..."
                       class="flex-1 bg-transparent border-none outline-none text-xs text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 py-1.5 rounded-full">
                <button @click="send()" :disabled="busy || !text.trim()"
                        class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 transition-colors"
                        :class="text.trim() && !busy ? 'bg-blue-500 hover:bg-blue-600 text-white' : 'bg-gray-300 dark:bg-gray-700 text-gray-400'">
                    <i class="fas fa-paper-plane text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function landingChatbot() {
    return {
        open: false,
        started: false,
        text: '',
        busy: false,
        unread: 0,
        history: [],

        init() {
            try {
                const h = sessionStorage.getItem('fn_chat_h');
                const m = sessionStorage.getItem('fn_chat_m');
                if (h) this.history = JSON.parse(h);
                if (m) {
                    const msgs = JSON.parse(m);
                    if (msgs.length) {
                        this.started = true;
                        this.$nextTick(() => {
                            msgs.forEach(m => this._bubble(m.r, m.t));
                            this._scroll();
                        });
                    }
                }
            } catch(e) {}
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.unread = 0;
                this.$nextTick(() => { this.$refs.input?.focus(); this._scroll(); });
            }
        },

        async send(msg) {
            const q = msg || this.text.trim();
            if (!q || this.busy) return;

            this.started = true;
            this.text = '';
            this.busy = true;
            await this.$nextTick();

            this.history.push({ role:'user', parts:[{text:q}] });
            this._bubble('user', q);

            const loader = this._bubble('bot', 'Typing...', true);

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('/api/chatbot/message', {
                    method: 'POST',
                    headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf||'' },
                    body: JSON.stringify({ message: q, chat_history: this.history })
                });
                const data = await res.json();
                loader.remove();
                const reply = data.success ? data.message : (data.message || 'Sorry, something went wrong.');
                if (data.success) {
                    this.history = data.chat_history || this.history;
                    this.history.push({ role:'model', parts:[{text:data.message}] });
                }
                this._bubble('bot', reply);
            } catch(e) {
                loader.remove();
                this._bubble('bot', 'Network error. Please try again.');
            }

            this.busy = false;
            this._save();
            if (!this.open) this.unread++;
            this.$nextTick(() => this.$refs.input?.focus());
        },

        _bubble(role, txt, loading=false) {
            const box = this.$refs.msgBox;
            if (!box) return null;

            const isUser = role === 'user';
            const botAvatar = '{{ asset("images/icons/castcrew/castcrew-pic-logo.svg") }}';
            const userAvatar = null; // user icon via FA

            // Outer row: avatar + bubble column
            const wrap = document.createElement('div');
            wrap.className = 'flex ' + (isUser ? 'flex-row-reverse' : 'flex-row') + ' items-start gap-1.5 px-2.5 mb-2';

            // Avatar
            const av = document.createElement('div');
            av.className = 'w-6 h-6 rounded-full shrink-0 flex items-center justify-center ' + (isUser ? 'bg-blue-500' : 'bg-gray-200 dark:bg-gray-600');
            if (isUser) {
                av.innerHTML = '<i class="fas fa-user text-white" style="font-size:10px"></i>';
            } else {
                av.innerHTML = '<img src="' + botAvatar + '" class="w-4 h-4" alt="">';
            }

            // Bubble + timestamp column
            const col = document.createElement('div');
            col.className = 'flex flex-col ' + (isUser ? 'items-end' : 'items-start') + ' min-w-0 max-w-[calc(100%-2.5rem)]';

            const b = document.createElement('div');
            b.className = (isUser ? 'cb-msg-user' : 'cb-msg') + ' px-3 py-2 text-xs leading-relaxed break-words '
                + (isUser
                    ? 'text-white rounded-2xl rounded-br-md'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-2xl rounded-bl-md');
            if (isUser) b.style.background = 'linear-gradient(135deg,#0084ff,#0066ff)';
            if (loading) b.classList.add('cb-typing');

            b.dataset.raw = txt;
            b.innerHTML = txt.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                .replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>')
                .replace(/\*(.+?)\*/g,'<em>$1</em>')
                .replace(/\n/g,'<br>')
                .replace(/^•\s?(.+)/gm,'<span class="flex gap-1"><span>&bull;</span><span>$1</span></span>');

            col.appendChild(b);

            if (!loading) {
                const ts = document.createElement('span');
                ts.className = 'text-[9px] text-gray-400 dark:text-gray-500 mt-0.5 px-1';
                const now = new Date();
                let h = now.getHours(); const mn = now.getMinutes();
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12 || 12;
                ts.textContent = h + ':' + (mn < 10 ? '0' : '') + mn + ' ' + ampm;
                col.appendChild(ts);
            }

            wrap.appendChild(av);
            wrap.appendChild(col);
            box.appendChild(wrap);
            this._scroll();
            return wrap;
        },

        _scroll() {
            const box = this.$refs.msgBox;
            if (box) box.scrollTop = box.scrollHeight;
        },

        clearChat() {
            if (!confirm('Clear chat?')) return;
            this.history = [];
            this.started = false;
            if (this.$refs.msgBox) this.$refs.msgBox.innerHTML = '';
            sessionStorage.removeItem('fn_chat_h');
            sessionStorage.removeItem('fn_chat_m');
        },

        _save() {
            try {
                sessionStorage.setItem('fn_chat_h', JSON.stringify(this.history));
                const box = this.$refs.msgBox;
                if (!box) return;
                const msgs = [];
                box.querySelectorAll('.cb-msg').forEach(el => {
                    const isUser = el.style.background?.includes('0084ff');
                    msgs.push({ r: isUser?'user':'bot', t: el.dataset.raw || el.textContent.trim() });
                });
                sessionStorage.setItem('fn_chat_m', JSON.stringify(msgs));
            } catch(e) {}
        }
    };
}
</script>
@endpush
