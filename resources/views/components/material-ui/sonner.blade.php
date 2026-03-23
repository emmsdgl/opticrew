{{-- Sonner Toast Notification Component --}}
{{-- Polls for new notifications and shows toast popups with a bell chime --}}

<div id="sonner-container"
     class="fixed bottom-4 right-4 z-[9999] flex flex-col-reverse gap-3 pointer-events-none max-w-sm w-full"
     x-data="sonnerToast()"
     x-init="startPolling()">

    <template x-for="toast in toasts" :key="toast.id">
        <div class="pointer-events-auto w-full transform transition-all duration-300 ease-out"
             x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-4 opacity-0 scale-95"
             x-transition:enter-end="translate-y-0 opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 opacity-100 scale-100"
             x-transition:leave-end="translate-y-4 opacity-0 scale-95">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
                 :class="{ 'ring-1 ring-blue-200 dark:ring-blue-800': toast.type !== 'error' }">

                {{-- Progress bar --}}
                <div class="h-0.5 bg-gray-100 dark:bg-gray-700 w-full">
                    <div class="h-full rounded-full transition-all ease-linear"
                         :class="{
                             'bg-blue-500': toast.type === 'info' || toast.type === 'default',
                             'bg-green-500': toast.type === 'success',
                             'bg-yellow-500': toast.type === 'warning',
                             'bg-red-500': toast.type === 'error',
                         }"
                         :style="'width: ' + toast.progress + '%; transition-duration: 100ms;'">
                    </div>
                </div>

                <div class="flex items-start gap-3 p-4">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5"
                         :class="{
                             'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400': toast.type === 'info' || toast.type === 'default',
                             'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400': toast.type === 'success',
                             'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400': toast.type === 'warning',
                             'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400': toast.type === 'error',
                         }">
                        <template x-if="toast.type === 'info' || toast.type === 'default'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'success'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </template>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" x-text="toast.title"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2" x-text="toast.message"></p>
                        <template x-if="toast.actionUrl">
                            <a :href="toast.actionUrl"
                               class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mt-1.5 transition-colors"
                               x-text="toast.actionLabel || 'View'"></a>
                        </template>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1" x-text="toast.time"></p>
                    </div>

                    {{-- Close button --}}
                    <button @click="dismissToast(toast.id)"
                            class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-0.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

{{-- Bell chime audio (generated via Web Audio API) --}}
<script>
document.addEventListener('alpine:init', () => {
    if (document.querySelector('[x-sonner-registered]')) return;
    document.documentElement.setAttribute('x-sonner-registered', '');

    // Bell chime using Web Audio API
    window.playBellChime = function() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();

            // Bell tone 1
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(830, ctx.currentTime);
            osc1.frequency.exponentialRampToValueAtTime(600, ctx.currentTime + 0.3);
            gain1.gain.setValueAtTime(0.3, ctx.currentTime);
            gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.6);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(ctx.currentTime);
            osc1.stop(ctx.currentTime + 0.6);

            // Bell tone 2 (harmonic)
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(1245, ctx.currentTime + 0.08);
            osc2.frequency.exponentialRampToValueAtTime(900, ctx.currentTime + 0.35);
            gain2.gain.setValueAtTime(0, ctx.currentTime);
            gain2.gain.setValueAtTime(0.15, ctx.currentTime + 0.08);
            gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(ctx.currentTime);
            osc2.stop(ctx.currentTime + 0.5);

            // Clean up
            setTimeout(() => ctx.close(), 1000);
        } catch (e) {
            // Audio not supported or blocked
        }
    };

    // Notification type mapping
    const typeMap = {
        'appointment_approved': 'success',
        'task_assigned': 'info',
        'task_completed': 'success',
        'schedule_updated': 'warning',
        'system_message': 'info',
        'payment_received': 'success',
        'leave_approved': 'success',
        'leave_rejected': 'error',
        'application_received': 'info',
        'application_hired': 'success',
        'application_rejected': 'error',
    };

    Alpine.data('sonnerToast', () => ({
        toasts: [],
        lastCheck: null,
        pollInterval: null,
        maxToasts: 5,
        toastDuration: 6000,
        seenIds: new Set(),

        startPolling() {
            // Restore seen IDs from sessionStorage so toasts don't repeat across page loads
            try {
                const stored = JSON.parse(sessionStorage.getItem('sonner_seen_ids') || '[]');
                stored.forEach(id => this.seenIds.add(id));
            } catch (e) {}

            // Set initial timestamp to now to only show NEW notifications
            this.lastCheck = sessionStorage.getItem('sonner_last_check') || new Date().toISOString();
            sessionStorage.setItem('sonner_last_check', this.lastCheck);

            // Poll every 15 seconds
            this.pollInterval = setInterval(() => this.checkNewNotifications(), 15000);

            // Also check once after a short delay on page load
            setTimeout(() => this.checkNewNotifications(), 3000);
        },

        async checkNewNotifications() {
            try {
                const url = `/notifications/latest-unread${this.lastCheck ? '?since=' + encodeURIComponent(this.lastCheck) : ''}`;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (!res.ok) return;

                const data = await res.json();
                const newNotifs = (data.notifications || []).filter(n => !this.seenIds.has(n.id));

                if (newNotifs.length > 0) {
                    // Update last check to the newest notification's timestamp
                    this.lastCheck = newNotifs[0].created_at;
                    sessionStorage.setItem('sonner_last_check', this.lastCheck);

                    // Show toasts for each new notification
                    newNotifs.reverse().forEach((notif, i) => {
                        setTimeout(() => {
                            this.addToast(notif);
                        }, i * 400);
                    });
                }
            } catch (e) {
                // Silently fail on network errors
            }
        },

        addToast(notif) {
            this.seenIds.add(notif.id);
            try {
                sessionStorage.setItem('sonner_seen_ids', JSON.stringify([...this.seenIds]));
            } catch (e) {}

            const toastType = typeMap[notif.type] || notif.type || 'default';
            const duration = notif.duration || this.toastDuration;
            const toast = {
                id: notif.id,
                title: notif.title,
                message: notif.message,
                type: toastType,
                time: this.timeAgo(notif.created_at),
                visible: true,
                progress: 100,
                actionUrl: notif.actionUrl || null,
                actionLabel: notif.actionLabel || null,
            };

            // Remove oldest if at max
            if (this.toasts.length >= this.maxToasts) {
                this.toasts.shift();
            }

            this.toasts.push(toast);

            // Play bell chime
            if (window.playBellChime) window.playBellChime();

            // Skip auto-dismiss and progress bar for persistent toasts
            if (notif.persistent) {
                toast.progress = 0;
                return;
            }

            // Animate progress bar
            const startTime = Date.now();
            const progressInterval = setInterval(() => {
                const elapsed = Date.now() - startTime;
                const remaining = Math.max(0, 100 - (elapsed / duration) * 100);
                const found = this.toasts.find(t => t.id === toast.id);
                if (found) {
                    found.progress = remaining;
                } else {
                    clearInterval(progressInterval);
                }
            }, 100);

            // Auto dismiss
            setTimeout(() => {
                this.dismissToast(toast.id);
                clearInterval(progressInterval);
            }, duration);
        },

        dismissToast(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        },

        timeAgo(dateStr) {
            const now = new Date();
            const date = new Date(dateStr);
            const diff = Math.floor((now - date) / 1000);

            if (diff < 10) return 'Just now';
            if (diff < 60) return diff + 's ago';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            return Math.floor(diff / 86400) + 'd ago';
        },

        // Public method to manually trigger a toast (for flash messages, etc.)
        show(title, message, type = 'default', options = {}) {
            this.addToast({
                id: 'manual-' + Date.now(),
                title: title,
                message: message,
                type: type,
                created_at: new Date().toISOString(),
                actionUrl: options.actionUrl || null,
                actionLabel: options.actionLabel || null,
                duration: options.duration || null,
                persistent: options.persistent || false,
            });
        },
    }));
});
</script>
