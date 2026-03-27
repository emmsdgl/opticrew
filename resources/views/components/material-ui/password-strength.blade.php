{{--
    Password Strength Indicator Component

    Usage:
        <x-material-ui.password-strength model="newPassword" />

    Props:
        - model: Alpine.js model name for the password field (required)
--}}

@props(['model' => 'newPassword'])

<div x-show="{{ $model }}.length > 0" x-cloak x-transition class="space-y-2.5">
    @php $uid = 'pw_' . uniqid(); @endphp
    <div x-data="{
        get pw() { return {{ $model }} || ''; },
        get score() {
            let s = 0;
            if (this.pw.length > 5) s++;
            if (this.pw.length > 8) s++;
            if (/[A-Z]/.test(this.pw)) s++;
            if (/[a-z]/.test(this.pw)) s++;
            if (/[0-9]/.test(this.pw)) s++;
            if (/[^A-Za-z0-9]/.test(this.pw)) s++;
            return s;
        },
        get level() {
            if (this.score <= 2) return 'weak';
            if (this.score <= 4) return 'medium';
            if (this.score <= 5) return 'strong';
            return 'very-strong';
        }
    }">
        {{-- Strength bar --}}
        <div class="h-1.5 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden flex gap-0.5">
            <template x-for="i in 4">
                <div class="h-full flex-1 rounded-full transition-all duration-300"
                    :class="i <= Math.min(Math.ceil(score / 1.5), 4)
                        ? (level === 'weak' ? 'bg-red-500' : level === 'medium' ? 'bg-orange-500' : level === 'strong' ? 'bg-green-500' : 'bg-emerald-500')
                        : 'bg-gray-200 dark:bg-gray-600'">
                </div>
            </template>
        </div>

        {{-- Strength label --}}
        <p class="text-xs font-medium transition-colors"
            :class="level === 'weak' ? 'text-red-500' : level === 'medium' ? 'text-orange-500' : level === 'strong' ? 'text-green-500' : 'text-emerald-500'"
            x-text="level === 'weak' ? 'Weak' : level === 'medium' ? 'Medium' : level === 'strong' ? 'Strong' : 'Very Strong'">
        </p>

        {{-- Requirement checklist --}}
        <ul class="space-y-1.5 text-xs">
            <li class="flex items-center gap-2 transition-colors"
                :class="{{ $model }}.length >= 8 ? 'text-green-500' : 'text-gray-400 dark:text-gray-500'">
                <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 transition-colors"
                    :class="{{ $model }}.length >= 8 ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600'">
                    <i class="fa-solid text-[8px] text-white" :class="{{ $model }}.length >= 8 ? 'fa-check' : 'fa-xmark'"></i>
                </div>
                At least 8 characters
            </li>
            <li class="flex items-center gap-2 transition-colors"
                :class="/[A-Z]/.test({{ $model }}) ? 'text-green-500' : 'text-gray-400 dark:text-gray-500'">
                <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 transition-colors"
                    :class="/[A-Z]/.test({{ $model }}) ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600'">
                    <i class="fa-solid text-[8px] text-white" :class="/[A-Z]/.test({{ $model }}) ? 'fa-check' : 'fa-xmark'"></i>
                </div>
                At least one uppercase letter
            </li>
            <li class="flex items-center gap-2 transition-colors"
                :class="/[a-z]/.test({{ $model }}) ? 'text-green-500' : 'text-gray-400 dark:text-gray-500'">
                <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 transition-colors"
                    :class="/[a-z]/.test({{ $model }}) ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600'">
                    <i class="fa-solid text-[8px] text-white" :class="/[a-z]/.test({{ $model }}) ? 'fa-check' : 'fa-xmark'"></i>
                </div>
                At least one lowercase letter
            </li>
            <li class="flex items-center gap-2 transition-colors"
                :class="/[0-9]/.test({{ $model }}) ? 'text-green-500' : 'text-gray-400 dark:text-gray-500'">
                <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 transition-colors"
                    :class="/[0-9]/.test({{ $model }}) ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600'">
                    <i class="fa-solid text-[8px] text-white" :class="/[0-9]/.test({{ $model }}) ? 'fa-check' : 'fa-xmark'"></i>
                </div>
                At least one number
            </li>
            <li class="flex items-center gap-2 transition-colors"
                :class="/[^A-Za-z0-9]/.test({{ $model }}) ? 'text-green-500' : 'text-gray-400 dark:text-gray-500'">
                <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 transition-colors"
                    :class="/[^A-Za-z0-9]/.test({{ $model }}) ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600'">
                    <i class="fa-solid text-[8px] text-white" :class="/[^A-Za-z0-9]/.test({{ $model }}) ? 'fa-check' : 'fa-xmark'"></i>
                </div>
                At least one special character
            </li>
        </ul>
    </div>
</div>
