    <footer class="bg-gray-50 dark:bg-[#121629] text-gray-700 dark:text-gray-300 px-12 md:px-16 py-6 transition-colors duration-300">
        <!-- Main Container -->
        <div class="max-w-7xl mx-auto">
            <!-- Top Grid -->
            <div class="grid grid-cols-1 mt-12 mb-10 md:grid-cols-6 gap-10 md:gap-16">
                <!-- Left Section -->
                <div class="md:col-span-2 space-y-8">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('common.footer.finnoys') }}</h2>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        {{ __('common.footer.description') }}
                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm">
                        {{ __('common.footer.consent') }}
                    </p>
                </div>

                <!-- Right Section -->
                <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-12 text-sm">

                <div>
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-white">{{ __('common.footer.about') }}</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ route('about') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">{{ __('common.footer.finnoys') }}</a></li>
                            <li><a href="{{ route('recruitment') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">{{ __('common.footer.careers') }}</a></li>
                            <li><a href="{{ route('castcrew') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">{{ __('common.footer.castcrew') }}</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-white">{{ __('common.footer.support') }}</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ route('contact') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">{{ __('common.footer.contact') }}</a></li>
                            <li><a href="{{ route('documentation') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">{{ __('common.footer.documentation') }}</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-white">{{ __('common.footer.legal') }}</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ route('termscondition') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">{{ __('common.footer.terms') }}</a></li>
                            <li><a href="{{ route('privacypolicy') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">{{ __('common.footer.privacy') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div
                class="flex flex-col md:flex-row items-center justify-between border-t border-gray-300 dark:border-gray-700 pt-6">
                <div id="trademark" class="text-xs text-gray-500 dark:text-gray-400 mb-4 md:mb-0">
                    {{ __('common.footer.copyright') }}
                </div>
                <div id="socials" class="flex space-x-4">
                    <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white transition">
                        <i class="fab fa-facebook-f fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white transition">
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white transition">
                        <i class="fab fa-whatsapp fa-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>