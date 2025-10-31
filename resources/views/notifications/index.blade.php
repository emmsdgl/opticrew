<x-layouts.general-employer :title="'Notifications'">
    <section class="flex flex-col w-full gap-6 p-4 md:p-6 min-h-[calc(100vh-4rem)]">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @if($unreadCount > 0)
                        You have {{ $unreadCount }} unread notification{{ $unreadCount > 1 ? 's' : '' }}
                    @else
                        All caught up!
                    @endif
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                @if($unreadCount > 0)
                    <button onclick="markAllAsRead()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Mark All as Read
                    </button>
                @endif

                <!-- Test Button (Remove in Production) -->
                <a href="{{ route('notifications.test') }}" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    Create Test Notifications
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Notifications List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            @forelse($notifications as $notification)
                <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0 {{ $notification->isUnread() ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
                    <div class="p-4 md:p-6">
                        <div class="flex items-start justify-between gap-4">
                            <!-- Notification Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <!-- Unread Indicator -->
                                    @if($notification->isUnread())
                                        <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                    @endif

                                    <!-- Title -->
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{ $notification->title }}
                                    </h3>

                                    <!-- Type Badge -->
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($notification->type === 'appointment_approved') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                        @elseif($notification->type === 'task_assigned') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                                        @elseif($notification->type === 'schedule_updated') bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400
                                        @elseif($notification->type === 'payment_received') bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                    </span>
                                </div>

                                <!-- Message -->
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $notification->message }}
                                </p>

                                <!-- Additional Data (if available) -->
                                @if($notification->data && count($notification->data) > 0)
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                        @foreach($notification->data as $key => $value)
                                            <span class="mr-3">
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                {{ is_array($value) ? json_encode($value) : $value }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Timestamp -->
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-start gap-2">
                                @if($notification->isUnread())
                                    <button onclick="markAsRead({{ $notification->id }})"
                                            class="p-2 text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                            title="Mark as read">
                                        <i class="fi fi-rr-check text-sm"></i>
                                    </button>
                                @endif

                                <button onclick="deleteNotification({{ $notification->id }})"
                                        class="p-2 text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                        title="Delete">
                                    <i class="fi fi-rr-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 text-gray-400 dark:text-gray-600">
                        <i class="fi fi-rr-bell text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No notifications</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        You don't have any notifications yet.
                    </p>
                    <a href="{{ route('notifications.test') }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium">
                        Create Test Notifications
                    </a>
                </div>
            @endforelse
        </div>
    </section>

    <script>
        // Mark single notification as read
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to update UI
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Mark all notifications as read
        function markAllAsRead() {
            fetch('/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to update UI
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Delete notification
        function deleteNotification(notificationId) {
            if (!confirm('Are you sure you want to delete this notification?')) {
                return;
            }

            fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to update UI
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</x-layouts.general-employer>
