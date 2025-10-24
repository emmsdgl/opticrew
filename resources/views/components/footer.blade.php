<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>

<body class="bg-white dark:bg-gray-900">

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
                        The latest updates, discounts, and services, sent to your directly to your inbox.
                    </p>

                    <!-- Email Form -->
                    <form
                        class="flex items-center bg-white dark:bg-[#121629] pr-3 p-2 rounded-full overflow-hidden shadow-sm w-full max-w-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus-within:ring-2 focus-within:ring-blue-500 dark:focus-within:ring-indigo-500 transition-all">
                        <input type="email" placeholder="Enter your email" required
                            class="flex-grow min-w-[80px] px-4 py-1 rounded-3xl border-0 bg-transparent text-gray-900 dark:text-white shadow-sm text-sm focus:outline-none placeholder-gray-400 dark:placeholder-gray-500" />
                        <button type="submit"
                            class="bg-blue-600 dark:bg-blue-700 text-white px-5 py-2 text-sm font-medium z-10 rounded-full hover:bg-blue-700 dark:hover:bg-blue-800 transition">
                            Submit
                        </button>
                    </form>

                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm">
                        By subscribing you agree to with our Privacy Policy and provide consent to receive updates from
                        Fin-noys.
                    </p>
                </div>

                <!-- Right Section -->
                <div class="md:col-span-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-8 text-sm">
                    <div>
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-white">Company</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Founders</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Services</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Pricing</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-white">About</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Fin-noys</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Developers</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-white">Support</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Help Center</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Contact List</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Documentation</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4 text-gray-900 dark:text-white">Legal</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Terms & Conditions</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:underline transition">Cookies</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div
                class="flex flex-col md:flex-row items-center justify-between border-t border-gray-300 dark:border-gray-700 pt-6">
                <div id="trademark" class="text-xs text-gray-500 dark:text-gray-400 mb-4 md:mb-0">
                    © 2025 Fin-noys-OptiCrew All rights reserved.
                </div>
                <div id="socials" class="flex space-x-4">
                    <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white transition">
                        <i class="fab fa-facebook-f fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white transition">
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white transition">
                        <i class="fab fa-twitter fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white transition">
                        <i class="fab fa-github fa-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>