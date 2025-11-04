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

    // Get user role for routing
    $userRole = auth()->user()->role;
    $updateRoute = $userRole === 'admin' ? route('admin.profile.update') :
                   ($userRole === 'employee' ? route('employee.profile.update') : route('client.profile.update'));
    $uploadRoute = $userRole === 'admin' ? route('admin.profile.upload-picture') :
                   ($userRole === 'employee' ? route('employee.profile.upload-picture') : route('client.profile.upload-picture'));
@endphp

<div class="w-full {{ $currentSize['card'] }} mx-auto rounded-3xl transition-all duration-300 p-4"
     x-data="{
         editing: false,
         name: '{{ $name }}',
         username: '{{ $username }}',
         email: '{{ $email }}',
         phone: '{{ $phone }}',
         location: '{{ $location }}',
         toggleEdit() {
             if (this.editing) {
                 // Save changes
                 this.$refs.profileForm.submit();
             } else {
                 // Enter edit mode
                 this.editing = true;
             }
         }
     }">
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

    <!-- Profile Picture Upload (Shown in Edit Mode) -->
    <div x-show="editing" x-cloak class="mb-6 bg-purple-50 dark:bg-purple-900/20 rounded-2xl p-4">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Update Profile Picture</h3>
        <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="file"
                   name="profile_picture"
                   accept="image/*"
                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-lg file:border-0
                          file:text-sm file:font-semibold
                          file:bg-blue-50 file:text-blue-700
                          hover:file:bg-blue-100
                          dark:file:bg-blue-900 dark:file:text-blue-200
                          dark:hover:file:bg-blue-800"
                   required>
            <button type="submit"
                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm">
                Upload Picture
            </button>
        </form>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">JPG, PNG or GIF. Max size 2MB.</p>
    </div>

    <!-- Profile Update Form -->
    <form x-ref="profileForm" method="POST" action="{{ $updateRoute }}">
        @csrf

        <!-- Contact Information Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
            <!-- Name (Hidden input, displayed in greeting) -->
            <input type="hidden" name="name" x-model="name">

            <!-- Email -->
            <div class="group rounded-2xl p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Email</div>
                        <input x-show="editing"
                               type="email"
                               name="email"
                               x-model="email"
                               class="w-full text-sm font-medium px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <div x-show="!editing" class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" x-text="email"></div>
                    </div>
                </div>
            </div>

            <!-- Phone -->
            <div class="group rounded-2xl p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Phone</div>
                        <input x-show="editing"
                               type="text"
                               name="phone"
                               x-model="phone"
                               placeholder="+358 XX XXX XXXX"
                               class="w-full text-sm font-medium px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <div x-show="!editing" class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="phone || '+358 XX XXX XXXX'"></div>
                    </div>
                </div>
            </div>

            <!-- Username (Editable) -->
            <div class="group rounded-2xl p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/40 transition-colors">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                            <path d="M240 192C240 147.8 275.8 112 320 112C364.2 112 400 147.8 400 192C400 236.2 364.2 272 320 272C275.8 272 240 236.2 240 192zM448 192C448 121.3 390.7 64 320 64C249.3 64 192 121.3 192 192C192 262.7 249.3 320 320 320C390.7 320 448 262.7 448 192zM144 544C144 473.3 201.3 416 272 416L368 416C438.7 416 496 473.3 496 544L496 552C496 565.3 506.7 576 520 576C533.3 576 544 565.3 544 552L544 544C544 446.8 465.2 368 368 368L272 368C174.8 368 96 446.8 96 544L96 552C96 565.3 106.7 576 120 576C133.3 576 144 565.3 144 552L144 544z"/></svg>                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Username</div>
                        <input x-show="editing"
                               type="text"
                               name="username"
                               x-model="username"
                               placeholder="Enter username"
                               class="w-full text-sm font-medium px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <div x-show="!editing" class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="username || 'Add a Username'"></div>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="group rounded-2xl p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 w-full">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/40 transition-colors">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Location</div>
                        <input x-show="editing"
                               type="text"
                               name="location"
                               x-model="location"
                               class="w-full text-sm font-medium px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <div x-show="!editing" class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="location || 'Add a location'"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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