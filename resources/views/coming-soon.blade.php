<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - OptiCrew</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
            50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.8); }
        }
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-2xl mx-auto">
        <!-- Animated Icon -->
        <div class="mb-8 float-animation">
            <div class="inline-block p-8 bg-white rounded-full pulse-glow">
                <i class="fas fa-tools text-blue-500 text-8xl"></i>
            </div>
        </div>

        <!-- Main Heading -->
        <h1 class="text-5xl md:text-6xl font-bold text-gray-800 mb-4">
            Coming Soon!
        </h1>

        <!-- Subheading -->
        <p class="text-xl md:text-2xl text-gray-600 mb-6">
            We're Working on Something Amazing
        </p>

        <!-- Description -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-6 mb-8 shadow-lg">
            <p class="text-gray-700 text-lg leading-relaxed">
                This feature is currently under development. Our team is hard at work building something special for you. Check back soon!
            </p>
        </div>

        <!-- Progress Indicators -->
        <div class="flex justify-center gap-4 mb-8">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-check text-white text-2xl"></i>
                </div>
                <p class="text-sm text-gray-600">Planning</p>
            </div>
            <div class="flex items-center">
                <div class="w-12 h-1 bg-blue-300"></div>
            </div>
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mb-2 animate-pulse">
                    <i class="fas fa-code text-white text-2xl"></i>
                </div>
                <p class="text-sm text-gray-600 font-semibold">Development</p>
            </div>
            <div class="flex items-center">
                <div class="w-12 h-1 bg-gray-300"></div>
            </div>
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-rocket text-gray-500 text-2xl"></i>
                </div>
                <p class="text-sm text-gray-600">Launch</p>
            </div>
        </div>

        <!-- Back Button -->
        <button onclick="window.history.back()" class="inline-flex items-center gap-2 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transform transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
            <i class="fas fa-arrow-left"></i>
            <span>Go Back</span>
        </button>

        <!-- Additional Info -->
        <div class="mt-8 text-gray-500 text-sm">
            <p>Need immediate assistance? Contact support</p>
            <p class="mt-2">
                <a href="mailto:support@opticrew.com" class="text-blue-600 hover:text-blue-800 underline">
                    support@opticrew.com
                </a>
            </p>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="fixed top-10 left-10 text-blue-200 opacity-20">
        <i class="fas fa-cog text-6xl animate-spin" style="animation-duration: 10s;"></i>
    </div>
    <div class="fixed bottom-10 right-10 text-purple-200 opacity-20">
        <i class="fas fa-wrench text-6xl" style="animation: float 4s ease-in-out infinite;"></i>
    </div>
    <div class="fixed top-1/2 right-20 text-indigo-200 opacity-20">
        <i class="fas fa-hammer text-5xl" style="animation: float 5s ease-in-out infinite; animation-delay: 1s;"></i>
    </div>
</body>
</html>
