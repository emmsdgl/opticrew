@props([
    'name' => 'John Doe',
    'greeting' => 'Good Morning',
    'subtitle' => 'Continue your learning to achieve your target!',
    'avatar' => null,
    'status' => 'Out of office',
    'statusIcon' => '☕',
    'email' => null,
    'phone' => null,
    'username' => null,
    'location' => null,
    'showContactForm' => false,
    'size' => 'default' // 'sm', 'default', 'lg'
])

@php
    $sizes = [
        'sm' => ['card' => 'max-w-sm', 'avatar' => 'w-32h-24', 'greeting' => 'text-xl'],
        'default' => ['card' => 'max-w-lg', 'avatar' => 'w-32 h-32', 'greeting' => 'text-2xl'],
        'lg' => ['card' => 'max-w-2xl', 'avatar' => 'w-40 h-40', 'greeting' => 'text-3xl'],
    ];
    $currentSize = $sizes[$size] ?? $sizes['default'];
@endphp

<div class="w-full {{ $currentSize['card'] }} mx-auto rounded-3xl transition-all duration-300 p-4">
    <!-- Avatar Section with Animated Ring -->
    <div class="flex justify-center mb-6">
        <div class="relative">
            <!-- Animated Progress Ring -->
            <svg class="absolute -inset-3 w-[calc(100%+24px)] h-[calc(100%+24px)]" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="45" fill="none" stroke="#e5e7eb" stroke-width="2" class="dark:stroke-gray-700"/>
                <circle cx="50" cy="50" r="45" fill="none" stroke="url(#gradient)" stroke-width="3" 
                        stroke-dasharray="283" stroke-dashoffset="70" stroke-linecap="round"
                        class="profile-progress-ring" transform="rotate(-90 50 50)"/>
                <defs>
                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#8B5CF6;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#EC4899;stop-opacity:1" />
                    </linearGradient>
                </defs>
            </svg>
            
            <!-- Avatar -->
            <div class="{{ $currentSize['avatar'] }} rounded-full bg-purple-100 dark:bg-purple-900/30 overflow-hidden shadow-xl relative">
                @if($avatar)
                    <img src="{{ $avatar }}" alt="{{ $name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-purple-600 dark:text-purple-400 text-5xl">
                        {{ strtoupper(substr($name, 0, 1)) }}
                    </div>
                @endif
            </div>
            
            <!-- Status Badge -->
            <div class="absolute -top-2 -right-2 bg-purple-600 dark:bg-purple-500 rounded-full p-2.5 shadow-lg animate-bounce-slow">
                <span class="text-xl">{{ $statusIcon }}</span>
            </div>
        </div>
    </div>

    <!-- Greeting Section -->
    <div class="text-center mb-6">
        <h1 class="{{ $currentSize['greeting'] }} font-bold text-gray-900 dark:text-white mb-2">
            {{ $greeting }} {{ explode(' ', $name)[0] }} 🔥
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            {{ $subtitle }}
        </p>
        
        <!-- Status Badge -->

            <div class="inline-flex grid-cols-2 items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded-full px-4 py-2">
                <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full"></span>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $status }}</span>
            </div>
    </div>

    <!-- Contact Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
        <!-- Email -->
        <div class="group rounded-2xl p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 cursor-pointer">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Email</div>
                    @if($email)
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $email }}</div>
                    @else
                        <div class="text-sm text-gray-400 dark:text-gray-500">example.john.doe@gmail.com</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Phone -->
        <div class="group rounded-2xl p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 cursor-pointer">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Phone</div>
                    @if($phone)
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $phone }}</div>
                    @else
                        <div class="text-sm text-gray-400 dark:text-gray-500">+1 (415) 209-6798</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Username -->
        <div class="group rounded-2xl p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 cursor-pointer">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/40 transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.069 18.874c-4.023 0-5.82-1.979-5.82-3.464 0-.765.561-1.296 1.333-1.296 1.723 0 1.273 2.477 4.487 2.477 1.641 0 2.55-.895 2.55-1.811 0-.551-.269-1.16-1.354-1.429l-3.576-.895c-2.88-.724-3.403-2.286-3.403-3.751 0-3.047 2.861-4.191 5.549-4.191 2.471 0 5.393 1.373 5.393 3.199 0 .784-.688 1.24-1.453 1.24-1.469 0-1.198-2.037-4.164-2.037-1.469 0-2.292.664-2.292 1.617s1.153 1.258 2.157 1.487l2.637.587c2.891.649 3.624 2.346 3.624 3.944 0 2.476-1.902 4.324-5.722 4.324m11.084-4.882l-.029.135-.044-.24c.015.045.044.074.059.12.12-.675.181-1.363.181-2.052 0-1.529-.301-3.047-.898-4.512-.569-1.348-1.395-2.562-2.427-3.596-1.049-1.033-2.247-1.856-3.595-2.426-1.318-.631-2.801-.93-4.512-.898l.179.119c-.119-.074-.209-.179-.314-.224-1.743 0-3.496.494-4.944 1.483-1.275.871-2.317 2.087-2.969 3.446-.614 1.274-.929 2.638-.929 4.033 0 1.783.479 3.496 1.482 4.961.871 1.275 2.087 2.317 3.446 2.984 1.274.614 2.638.914 4.033.914 1.783 0 3.481-.479 4.961-1.467 1.275-.871 2.317-2.087 2.969-3.446.614-1.274.929-2.638.929-4.033 0-.119-.015-.239-.03-.359l-.029-.135z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Username</div>
                    @if($username)
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $username }}</div>
                    @else
                        <div class="text-sm text-gray-400 dark:text-gray-500">Add a Username</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Location -->
        <div class="group rounded-2xl p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 cursor-pointer w-full">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/40 transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Location</div>
                    @if($location)
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $location }}</div>
                    @else
                        <div class="text-sm text-gray-400 dark:text-gray-500">Add a location</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
<div class="w-full flex justify-center items-center">
    <div class="w-1/3 flex justify-center items-center py-4 rounded-lg">
        <x-button 
            label="Edit Profile" 
            color="blue" 
            size="md"
            icon='<i class="fa-solid fa-pen"></i>'
            x-on:click="toggleEdit"
            x-text="editing ? 'Save Changes' : 'Edit Profile'"
        />
    </div>
</div>
</div>

<style>
@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-bounce-slow {
    animation: bounce-slow 2s ease-in-out infinite;
}

.profile-progress-ring {
    transition: stroke-dashoffset 0.5s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate the progress ring on load
    const progressRings = document.querySelectorAll('.profile-progress-ring');
    progressRings.forEach(ring => {
        const offset = ring.getAttribute('stroke-dashoffset');
        ring.style.strokeDashoffset = '283';
        setTimeout(() => {
            ring.style.strokeDashoffset = offset;
        }, 100);
    });
});
</script>