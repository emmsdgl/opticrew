@props([
    'user',
    'stats' => [],
    'coverUploadRoute' => null,
])

@php
    $coverRoute = $coverUploadRoute;
    if (!$coverRoute) {
        $role = $user->role ?? 'client';
        $coverRoute = match($role) {
            'admin' => route('admin.profile.upload-cover', [], false),
            'employee' => route('employee.profile.upload-cover', [], false),
            default => route('client.profile.upload-cover', [], false),
        };
    }

    $pictureRoute = match($user->role ?? 'client') {
        'admin' => route('admin.profile.upload-picture'),
        'employee' => route('employee.profile.upload-picture'),
        default => route('client.profile.upload-picture'),
    };

    $formId = 'cover-form-' . ($user->id ?? uniqid());
    $picFormId = 'pic-form-' . ($user->id ?? uniqid());
    $coverUrl = $user->cover_photo ? asset($user->cover_photo) : '';
    $picUrl = $user->profile_picture ? asset($user->profile_picture) : '';
@endphp

<div class="w-full"
     x-data="{
         editing: false,
         coverPreview: '{{ $coverUrl }}',
         coverFile: null,
         picPreview: '{{ $picUrl }}',
         picFile: null,

         handleCoverSelect(e) {
             const file = e.target.files[0];
             if (!file || !file.type.startsWith('image/') || file.size > 5 * 1024 * 1024) return;
             this.coverFile = file;
             const reader = new FileReader();
             reader.onload = (ev) => { this.coverPreview = ev.target.result; };
             reader.readAsDataURL(file);
             this.$nextTick(() => document.getElementById('{{ $formId }}').submit());
         },

         handlePicSelect(e) {
             const file = e.target.files[0];
             if (!file || !file.type.startsWith('image/') || file.size > 2 * 1024 * 1024) return;
             this.picFile = file;
             const reader = new FileReader();
             reader.onload = (ev) => { this.picPreview = ev.target.result; };
             reader.readAsDataURL(file);
             this.$nextTick(() => document.getElementById('{{ $picFormId }}').submit());
         },
     }"
     @profile-edit-toggled.window="editing = $event.detail.editing">

    {{-- Cover Photo --}}
    <div class="h-36 bg-gradient-to-br from-blue-400 via-blue-500 to-indigo-500 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>

        <template x-if="coverPreview">
            <img :src="coverPreview" alt="Cover Photo" class="absolute inset-0 w-full h-full object-cover">
        </template>

        {{-- Edit overlay — only when editing is active --}}
        <div x-show="editing" x-cloak
             class="absolute inset-0 bg-black/40 flex items-center justify-center transition-opacity duration-200"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <label for="{{ $formId }}-input" class="cursor-pointer flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/20 backdrop-blur-sm text-white text-xs font-medium hover:bg-white/30 transition-colors">
                <i class="fa-solid fa-camera"></i>
                <span x-text="coverPreview ? 'Change Cover' : 'Add Cover'"></span>
            </label>
        </div>

        {{-- Hidden cover upload form --}}
        <form method="POST" action="{{ $coverRoute }}" enctype="multipart/form-data" id="{{ $formId }}">
            @csrf
            <input type="file" id="{{ $formId }}-input" name="cover_photo" accept="image/*" class="hidden"
                   @change="handleCoverSelect($event)">
        </form>
    </div>

    {{-- Avatar --}}
    <div class="flex justify-center -mt-12 relative z-10">
        <div class="relative"
             x-data="{ avatarHover: false }"
             @mouseenter="avatarHover = true"
             @mouseleave="avatarHover = false">
            <div class="p-[3px] rounded-full bg-gradient-to-br from-blue-400 via-blue-500 to-indigo-500 shadow-lg">
                <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 ring-2 ring-white dark:ring-[#1E293B] relative">
                    @php
                        $pcNameParts = explode(' ', trim($user->name ?? ''));
                        $pcInitials = strtoupper(substr($pcNameParts[0] ?? '', 0, 1) . substr(end($pcNameParts) ?: '', 0, 1));
                        if (strlen($pcInitials) < 1) $pcInitials = '?';
                    @endphp
                    <template x-if="picPreview">
                        <img :src="picPreview" alt="{{ $user->name }}" class="w-full h-full object-cover"
                            x-on:error="picPreview = ''">
                    </template>
                    <template x-if="!picPreview">
                        <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">{{ $pcInitials }}</span>
                        </div>
                    </template>

                    {{-- Avatar edit overlay --}}
                    <label x-show="editing && avatarHover" x-cloak
                           for="{{ $picFormId }}-input"
                           class="absolute inset-0 bg-black/50 flex items-center justify-center cursor-pointer transition-opacity duration-150 rounded-full"
                           x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                           x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <i class="fa-solid fa-camera text-white text-sm"></i>
                    </label>
                </div>
            </div>

            {{-- Floating Icon --}}
            <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-blue-500 border-2 border-white dark:border-[#1E293B] shadow-md flex items-center justify-center">
                <i class="fa-solid fa-check text-[10px] text-white"></i>
            </div>
        </div>
    </div>

    {{-- Hidden profile picture upload form --}}
    <form method="POST" action="{{ $pictureRoute }}" enctype="multipart/form-data" id="{{ $picFormId }}">
        @csrf
        <input type="file" id="{{ $picFormId }}-input" name="profile_picture" accept="image/*" class="hidden"
               @change="handlePicSelect($event)">
    </form>

    {{-- Body --}}
    <div class="px-6 pt-3 pb-2 text-center">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" title="{{ $user->email }}">{{ $user->email }}</p>

        {{-- Role Badge --}}
        @php
            $roleLabel = match($user->role) {
                'admin' => 'Admin',
                'employee' => 'Employee',
                'external_client' => 'Client',
                'applicant' => 'Applicant',
                default => ucfirst($user->role ?? 'User'),
            };
            $roleColor = match($user->role) {
                'admin' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                'employee' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
                'external_client' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                'applicant' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
                default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
            };
        @endphp
        <span class="inline-block mt-2 px-3 py-0.5 rounded-full text-[10px] font-semibold {{ $roleColor }}">{{ $roleLabel }}</span>

        {{ $slot ?? '' }}
        @if(count($stats) > 0)
            <div class="border-t border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-{{ count($stats) }} divide-x divide-gray-100 dark:divide-gray-700">
                    @foreach($stats as $stat)
                        <div class="flex flex-col items-center px-2 gap-0.5">
                            <span class="font-bold text-base {{ $stat['color'] ?? 'text-gray-900 dark:text-white' }}">{{ $stat['value'] }}</span>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
