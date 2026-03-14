    <footer class="bg-gray-50 dark:bg-[#121629] text-gray-700 dark:text-gray-300 px-12 md:px-16 py-6 transition-colors duration-300">
        <!-- Main Container -->
        <div class="max-w-7xl mx-auto">
            <!-- Top Grid -->
            <div class="grid grid-cols-1 mt-12 mb-10 md:grid-cols-6 gap-10 md:gap-16">
                <!-- Left Section -->
                <div class="md:col-span-2 space-y-8">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Fin-noys</h2>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        The latest updates, discounts, and services, accessed in your own personalized account.
                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm">
                        By creating an account, you agree to with our Privacy Policy and provide consent to receive updates from
                        Fin-noys.
                    </p>
                </div>

                <!-- Right Section -->
                <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-12 text-sm">

                <div>
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-white">About</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ route('about') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Fin-noys</a></li>
                            <li><a href="{{ route('recruitment') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Careers in Fin-noys</a></li>
                            <li><a href="{{ route('castcrew') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">CastCrew</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-white">Support</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ route('contact') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Contact</a></li>
                            <li><a href="{{ route('documentation') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Documentation</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-white">Legal</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ route('termscondition') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Terms & Conditions</a></li>
                            <li><a href="{{ route('privacypolicy') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div
                class="flex flex-col md:flex-row items-center justify-between border-t border-gray-300 dark:border-gray-700 pt-6">
                <div id="trademark" class="text-xs text-gray-500 dark:text-gray-400 mb-4 md:mb-0">
                    © 2025 Fin-noys-CastCrew All rights reserved.
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