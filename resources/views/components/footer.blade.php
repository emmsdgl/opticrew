    <footer class="bg-gray-50 dark:bg-[#121629] text-gray-700 dark:text-gray-300 px-8 md:px-16 py-12 transition-colors duration-300">
        <div class="max-w-7xl mx-auto">

            <!-- Top Section: Logo + Nav Links -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-10 mb-10">

                <!-- Logo & Description -->
                <div class="md:col-span-2 space-y-4">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/icons/finnoys-text-logo-light.svg') }}" alt="Fin-noys" class="h-6 w-auto block dark:hidden">
                        <img src="{{ asset('images/icons/finnoys-text-logo-ondark.svg') }}" alt="Fin-noys" class="h-6 w-auto hidden dark:block">
                    </a>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-sm">
                        {{ __('common.footer.description') }}
                    </p>
                    <!-- Social Icons -->
                    <div class="flex items-center gap-3 pt-2">
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 transition-all duration-200">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-blue-500 hover:text-white dark:hover:bg-blue-500 transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-green-500 hover:text-white dark:hover:bg-green-500 transition-all duration-200">
                            <i class="fab fa-whatsapp text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Nav Columns -->
                <div class="md:col-span-3 grid grid-cols-2 sm:grid-cols-3 gap-8 text-sm">
                    <!-- About -->
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-900 dark:text-white mb-4">{{ __('common.footer.about') }}</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ route('about') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('common.footer.finnoys') }}</a></li>
                            <li><a href="{{ route('recruitment') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('common.footer.careers') }}</a></li>
                            <li><a href="{{ route('castcrew') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('common.footer.castcrew') }}</a></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-900 dark:text-white mb-4">{{ __('common.footer.support') }}</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ route('contact') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('common.footer.contact') }}</a></li>
                            <li><a href="{{ route('documentation') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">How to Use</a></li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-900 dark:text-white mb-4">{{ __('common.footer.legal') }}</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ route('termscondition') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('common.footer.terms') }}</a></li>
                            <li><a href="{{ route('privacypolicy') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('common.footer.privacy') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-200 dark:border-gray-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    {{ __('common.footer.copyright') }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 max-w-md text-center sm:text-right">
                    {{ __('common.footer.consent') }}
                </p>
            </div>
        </div>
    </footer>