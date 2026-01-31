@extends('components.layouts.general-landing')

@section('title', 'Contact Us')
@push ('styles')
<style>
    body {
                background-image: none;
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
            }
    
            /* Remove background image in dark mode */
            .dark body {
                background-image: none;
                background-color: #1f2937;
            }

</style>
@endpush

@section('content')
<div class="container mx-auto px-6 py-16">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-4xl font-bold text-blue-950 dark:text-white mb-4">
            Get in Touch
        </h1>
        <p class="text-md text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
            Have questions about our cleaning services? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-12 max-w-6xl mx-auto">
        <!-- Contact Form -->
        <div class="frosted-card rounded-2xl p-8">
            <h2 class="text-xl font-bold text-blue-950 dark:text-white mb-6">Send us a Message</h2>
            <form class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Full Name
                    </label>
                    <input type="text" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email Address
                    </label>
                    <input type="email" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Phone Number
                    </label>
                    <input type="tel"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Message
                    </label>
                    <textarea rows="5" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                    Send Message
                </button>
            </form>
        </div>

        <!-- Contact Information -->
        <div class="space-y-8">
            <div class="frosted-card rounded-2xl p-8">
                <h2 class="text-xl font-bold text-blue-950 dark:text-white mb-6">Contact Information</h2>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Address</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                123 Cleaning Street<br>
                                Helsinki, Finland
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-phone text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Phone</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                +358 123 456 789
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-envelope text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Email</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                info@finnoys.com
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Business Hours</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Monday - Friday: 8:00 AM - 6:00 PM<br>
                                Saturday: 9:00 AM - 4:00 PM<br>
                                Sunday: Closed
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>
</div>
@endsection
