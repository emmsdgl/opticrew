<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <footer class="bg-[#121629] text-gray-300 px-12 md:px-16 py-6">
        <!-- Main Container -->
        <div class="max-w-7xl mx-auto">
            <!-- Top Grid -->
            <div class="grid grid-cols-1 mt-12 mb-10 md:grid-cols-6 gap-10 md:gap-16">
                <!-- Left Section -->
                <div class="md:col-span-2 space-y-8">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-lg font-semibold">Fin-noys</h2>
                    </div>
                    <p class="text-sm">The latest updates, discounts, and services, sent to your directly to your inbox.</p>

                    <!-- Email Form -->
                    <form
                        class="flex items-center bg-[#121629] pr-3 p-2 rounded-full overflow-hidden shadow-sm w-full max-w-sm ring-1 ring-inset ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        <input type="email" placeholder="Enter your email" required
                            class="flex-grow min-w-[80px] px-4 py-1 rounded-3xl border-0 bg-transparent text-white shadow-sm text-sm focus:outline-none placeholder-gray-400" />
                        <button type="submit"
                            class="bg-blue-700 text-white px-5 py-2 text-sm font-medium z-10 rounded-full hover:bg-gray-800 transition">
                            Submit
                        </button>
                    </form>

                    <p class="text-xs text-gray-500 max-w-sm">
                        By subscribing you agree to with our Privacy Policy and provide consent to receive updates from Fin-noys.
                    </p>
                </div>

                <!-- Right Section -->
                <div class="md:col-span-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-8 text-sm">
                    <div>
                        <h3 class="font-semibold mb-4">Company</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="hover:underline">Founders</a></li>
                            <li><a href="#" class="hover:underline">Services</a></li>
                            <li><a href="#" class="hover:underline">Pricing</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4">About</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="hover:underline">Fin-noys</a></li>
                            <li><a href="#" class="hover:underline">Developers</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4">Support</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="hover:underline">Help Center</a></li>
                            <li><a href="#" class="hover:underline">Contact List</a></li>
                            <li><a href="#" class="hover:underline">Documentation</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4">Legal</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="hover:underline">Terms & Conditions</a></li>
                            <li><a href="#" class="hover:underline">Privacy Policy</a></li>
                            <li><a href="#" class="hover:underline">Cookies</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="flex flex-col md:flex-row items-center justify-between border-t border-gray-700 pt-6">
                <div id="trademark" class="text-xs text-gray-500 mb-4 md:mb-0">
                    © 2025 Fin-noys-OptiCrew All rights reserved.
                </div>
                <div id="socials" class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-github fa-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
