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
        'sm' => ['card' => 'max-w-sm', 'avatar' => 'w-24 h-24', 'greeting' => 'text-xl'],
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
         uploadFormId: 'upload-form-' + Date.now(),
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
            <div id="profile-avatar" class="{{ $currentSize['avatar'] }} rounded-full bg-purple-100 dark:bg-purple-900/30 overflow-hidden shadow-xl relative">
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
        <div class="inline-flex items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded-full px-4 py-2">
            <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full"></span>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $status }}</span>
        </div>
    </div>

    <!-- Profile Picture Upload (Shown in Edit Mode) -->
    <div x-show="editing" x-cloak class="mb-6 rounded-2xl p-4">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Update Profile Picture</h3>
        
        <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" :id="uploadFormId">
            @csrf
            
            <!-- Drag and Drop Area -->
            <div :id="'drop-zone-' + uploadFormId" 
                 class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer bg-gray-50 dark:bg-gray-900">
                
                <!-- Upload Icon -->
                <div class="mb-3 flex justify-center">
                    <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>

                <!-- Upload Text -->
                <div :id="'upload-text-' + uploadFormId">
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                        Drag & drop <span class="text-blue-600 dark:text-blue-400 font-semibold">images</span>, or 
                        <span class="text-blue-600 dark:text-blue-400 font-semibold">videos</span>
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        or <button type="button" @click="$refs.fileInput.click()" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">browse files</button> on your computer
                    </p>
                </div>

                <!-- File Preview (Hidden by default) -->
                <div :id="'file-preview-' + uploadFormId" class="hidden">
                    <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-3">
                            <img :id="'preview-image-' + uploadFormId" src="" alt="Preview" class="w-10 h-10 rounded object-cover">
                            <div class="text-left">
                                <p :id="'file-name-' + uploadFormId" class="text-xs font-medium text-gray-900 dark:text-white"></p>
                                <p :id="'file-size-' + uploadFormId" class="text-xs text-gray-500 dark:text-gray-400"></p>
                            </div>
                        </div>
                        <button type="button" @click="window.removeUploadFile(uploadFormId)" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Hidden File Input -->
                <input type="file"
                       x-ref="fileInput"
                       name="profile_picture"
                       accept="image/*"
                       class="hidden"
                       @change="window.handleUploadFileSelect(uploadFormId, $event.target)">
            </div>

            <!-- Upload Button -->
            <div class="mt-3 flex justify-center">
                <button type="submit" :id="'upload-button-' + uploadFormId" disabled
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors text-sm">
                    Upload Picture
                </button>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">JPG, PNG or GIF. Max size 2MB.</p>
        </form>
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
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">Email</div>
                        <input x-show="editing"
                               type="email"
                               name="email"
                               x-model="email"
                               class="w-full text-sm font-medium px-3 py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:border-blue-400 dark:focus:border-blue-500 transition-colors">
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
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">Phone</div>
                        <input x-show="editing"
                               type="text"
                               name="phone"
                               x-model="phone"
                               placeholder="+358 XX XXX XXXX"
                               class="w-full text-sm font-medium px-3 py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-400 dark:focus:border-blue-500 transition-colors">
                        <div x-show="!editing" class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="phone || '+358 XX XXX XXXX'"></div>
                    </div>
                </div>
            </div>

            <!-- Username (Editable) -->
            <div class="group rounded-2xl p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">Username</div>
                        <input x-show="editing"
                               type="text"
                               name="username"
                               x-model="username"
                               placeholder="Enter username"
                               class="w-full text-sm font-medium px-3 py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-400 dark:focus:border-blue-500 transition-colors">
                        <div x-show="!editing" class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="username || 'Add a Username'"></div>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="group rounded-2xl p-4 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 w-full">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1.5">Location</div>
                        <input x-show="editing"
                               type="text"
                               name="location"
                               x-model="location"
                               placeholder="Enter location"
                               class="w-full text-sm font-medium px-3 py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-blue-400 dark:focus:border-blue-500 transition-colors">
                        <div x-show="!editing" class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="location || 'Add a location'"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="w-full flex justify-center items-center">
        <div class="flex justify-center items-center w-full rounded-lg my-4">
            <x-button 
                label="Edit Profile" 
                color="blue" 
                size="save-edit-profile"
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
// Global functions for file upload handling
window.handleUploadFileSelect = function(formId, input) {
    const file = input.files[0];
    
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImage = document.getElementById('preview-image-' + formId);
            const profileAvatar = document.getElementById('profile-avatar');
            
            if (previewImage) {
                previewImage.src = e.target.result;
            }
            
            // Update the main avatar preview
            if (profileAvatar) {
                profileAvatar.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
            }
        };
        reader.readAsDataURL(file);

        // Update file info
        const fileName = document.getElementById('file-name-' + formId);
        const fileSize = document.getElementById('file-size-' + formId);
        
        if (fileName) fileName.textContent = file.name;
        if (fileSize) fileSize.textContent = window.formatFileSize(file.size) + ' • ' + file.type.split('/')[1].toUpperCase();

        // Show preview, hide upload text
        const uploadText = document.getElementById('upload-text-' + formId);
        const filePreview = document.getElementById('file-preview-' + formId);
        
        if (uploadText) uploadText.classList.add('hidden');
        if (filePreview) filePreview.classList.remove('hidden');

        // Enable upload button
        const uploadButton = document.getElementById('upload-button-' + formId);
        if (uploadButton) uploadButton.disabled = false;
    }
};

window.removeUploadFile = function(formId) {
    const form = document.getElementById(formId);
    const fileInput = form ? form.querySelector('input[type="file"]') : null;
    
    if (fileInput) fileInput.value = '';
    
    const uploadText = document.getElementById('upload-text-' + formId);
    const filePreview = document.getElementById('file-preview-' + formId);
    const uploadButton = document.getElementById('upload-button-' + formId);
    
    if (uploadText) uploadText.classList.remove('hidden');
    if (filePreview) filePreview.classList.add('hidden');
    if (uploadButton) uploadButton.disabled = true;
};

window.formatFileSize = function(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

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

    // Setup drag and drop for all upload zones
    document.querySelectorAll('[id^="drop-zone-"]').forEach(dropZone => {
        const formId = dropZone.id.replace('drop-zone-', '');
        const form = document.getElementById(formId);
        const fileInput = form ? form.querySelector('input[type="file"]') : null;

        if (!fileInput) return;

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        // Highlight drop zone when dragging over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, function() {
                dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, function() {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
            }, false);
        });

        // Handle dropped files
        dropZone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                window.handleUploadFileSelect(formId, fileInput);
            }
        }, false);
    });
});
</script>