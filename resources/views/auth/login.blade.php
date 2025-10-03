<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiCrew - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="h-screen overflow-hidden">
    <!-- Two Column Layout -->
    <div class="flex h-full">
        <!-- Left Column - Marketing/Branding Section -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-900 via-purple-800 to-indigo-900 items-center justify-center p-12">
            <div class="text-center">
                <h1 class="text-5xl font-bold text-white mb-6 leading-tight">
                    One-stop booking<br/>for a spotless space.
                </h1>
                <p class="text-indigo-200 text-lg">
                    Streamline your cleaning crew management with intelligent optimization
                </p>
            </div>
        </div>

        <!-- Right Column - Login Form -->
        <div class="w-full lg:w-1/2 bg-gray-50 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-indigo-900">Fin-noys</h1>
                </div>

                <!-- Login Form -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Log In</h2>
                    <p class="text-gray-600 mb-6">Welcome back! Please enter your details.</p>

                    <form method="POST" action="{{ route('login') }}">
                    @csrf

                        <!-- Email/Username Input -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">
                                Email (Adam) / Username (AdamBalona)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </span>
                                <input 
                                    name="email"
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                                    id="email" 
                                    type="text" 
                                    placeholder="Enter your email or username"
                                >
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                                Password
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </span>
                                <input 
                                    name="password" 
                                    class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                                    id="password" 
                                    type="password" 
                                    placeholder="Enter your password"
                                >
                                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <input 
                                    id="remember-me" 
                                    type="checkbox" 
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                >
                                <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                    Remember Me
                                </label>
                            </div>
                            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">
                                Forgot Password?
                            </a>
                        </div>

                        <!-- Login Button -->
                        <button 
                            type="submit" 
                            class="w-full bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-200"
                        >
                            Log In
                        </button>

                        <!-- Create Account Link -->
                        <div class="text-center mt-4">
                            <p class="text-sm text-gray-600">
                                Don't have an account? 
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                    Create Account
                                </a>
                            </p>
                        </div>

                        <!-- Terms and Conditions Disclaimer -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-xs text-gray-500 text-center">
                                By logging in, you agree to our 
                                <a href="#" class="text-indigo-600 hover:underline">Terms and Conditions</a> 
                                and 
                                <a href="#" class="text-indigo-600 hover:underline">Privacy Policy</a>.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
