{{-- Shared profile modal editable fields: Phone (with flag dropdown), Email, Username, Location, Change Password --}}
<div class="space-y-3 text-left">
    {{-- Phone --}}
    <div class="flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fa-solid fa-phone text-xs text-blue-500 dark:text-blue-400"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Phone</p>
            <template x-if="!editing">
                <p class="text-sm font-normal text-gray-800 dark:text-gray-200 truncate" x-text="form.phone || '—'"></p>
            </template>
            <template x-if="editing">
                <div>
                    <div class="flex items-stretch">
                        <div class="relative" @click.away="phoneDropOpen = false">
                            <button type="button" @click="phoneDropOpen = !phoneDropOpen"
                                class="flex items-center gap-1.5 h-full px-2.5 py-1.5 rounded-l-lg border border-r-0 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-xs font-normal text-gray-800 dark:text-gray-200 focus:outline-none whitespace-nowrap">
                                <img :src="phonePrefix === '+63' ? '{{ asset('images/icons/philippine_flag.png') }}' : '{{ asset('images/icons/finland-flag.svg') }}'" class="h-3.5 w-auto">
                                <span x-text="phonePrefix" class="text-xs font-medium"></span>
                                <svg class="w-2.5 h-2.5 text-gray-400 transition-transform" :class="phoneDropOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="phoneDropOpen" x-cloak x-transition class="absolute left-0 top-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-50 w-52 py-1.5">
                                <button type="button" @click="setPhonePrefix('+63', 10); phoneDropOpen = false" class="w-full text-left px-3 py-2 text-xs flex items-center gap-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" :class="phonePrefix === '+63' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-200'">
                                    <img src="{{ asset('images/icons/philippine_flag.png') }}" class="h-4 w-auto" alt="PH"> <span>+63</span> <span class="text-gray-400 dark:text-gray-500 text-[10px] ml-auto">Philippines</span>
                                </button>
                                <button type="button" @click="setPhonePrefix('+358', 9); phoneDropOpen = false" class="w-full text-left px-3 py-2 text-xs flex items-center gap-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" :class="phonePrefix === '+358' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-200'">
                                    <img src="{{ asset('images/icons/finland-flag.svg') }}" class="h-4 w-auto" alt="FI"> <span>+358</span> <span class="text-gray-400 dark:text-gray-500 text-[10px] ml-auto">Finland</span>
                                </button>
                            </div>
                        </div>
                        <input type="tel" x-model="phoneLocal" @input="phoneLocal = phoneLocal.replace(/[^\d]/g, ''); if(phoneLocal.startsWith('0')) phoneLocal = phoneLocal.replace(/^0/,''); syncPhone()" @blur="validatePhoneNumber()" :placeholder="phonePrefix === '+63' ? '9XX XXX XXXX' : '4X XXX XXXX'" :maxlength="phoneMaxLen"
                            class="flex-1 text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-r-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all" :class="phoneError ? 'border-red-500 dark:border-red-500' : ''">
                    </div>
                    <p x-show="phoneError" x-text="phoneError" x-cloak class="mt-1 text-[10px] text-red-500"></p>
                </div>
            </template>
        </div>
    </div>

    {{-- Email (read-only) --}}
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-envelope text-xs text-blue-500 dark:text-blue-400"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Email</p>
            <p class="text-sm font-normal text-gray-800 dark:text-gray-200 truncate">{{ $profileUser->email ?? '—' }}</p>
        </div>
    </div>

    {{-- Username --}}
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-user-tag text-xs text-blue-500 dark:text-blue-400"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Username</p>
            <template x-if="!editing">
                <p class="text-sm font-normal text-gray-800 dark:text-gray-200 truncate" x-text="form.username || '—'"></p>
            </template>
            <template x-if="editing">
                <input type="text" x-model="form.username" placeholder="Enter username"
                    class="w-full text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
            </template>
        </div>
    </div>

    {{-- Location --}}
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-location-dot text-xs text-blue-500 dark:text-blue-400"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Location</p>
            <template x-if="!editing">
                <p class="text-sm font-normal text-gray-800 dark:text-gray-200 truncate" x-text="form.location || '—'"></p>
            </template>
            <template x-if="editing">
                <input type="text" x-model="form.location" placeholder="Enter location"
                    class="w-full text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
            </template>
        </div>
    </div>

    {{-- Change Password (edit mode only) --}}
    <template x-if="editing">
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-3">
            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 tracking-wider">Change Password</p>
            <div class="flex items-center gap-3" x-show="hasPassword">
                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-lock text-xs text-blue-500 dark:text-blue-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Current Password</p>
                    <div class="relative">
                        <input :type="showCurrentPw ? 'text' : 'password'" x-model="form.current_password" placeholder="Enter current password"
                            class="w-full text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 pr-8 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                        <button type="button" @click="showCurrentPw = !showCurrentPw" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg x-show="!showCurrentPw" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showCurrentPw" x-cloak xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-key text-xs text-blue-500 dark:text-blue-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">New Password</p>
                    <div class="relative">
                        <input :type="showNewPw ? 'text' : 'password'" x-model="form.new_password" placeholder="Enter new password"
                            class="w-full text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 pr-8 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                        <button type="button" @click="showNewPw = !showNewPw" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg x-show="!showNewPw" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showNewPw" x-cloak xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-check-double text-xs text-blue-500 dark:text-blue-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-none mb-0.5">Confirm Password</p>
                    <div class="relative">
                        <input :type="showConfirmPw ? 'text' : 'password'" x-model="form.new_password_confirmation" placeholder="Confirm new password"
                            class="w-full text-sm font-normal text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 pr-8 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all">
                        <button type="button" @click="showConfirmPw = !showConfirmPw" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg x-show="!showConfirmPw" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showConfirmPw" x-cloak xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-.722-3.25"/><path d="M2 8a10.645 10.645 0 0 0 20 0"/><path d="m20 15-1.726-2.05"/><path d="m4 15 1.726-2.05"/><path d="m9 18 .722-3.25"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <x-material-ui.password-strength model="form.new_password" />
        </div>
    </template>
</div>
