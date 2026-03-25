@extends('components.layouts.general-landing')

@section('title', 'Contact Us')
@push ('styles')
<style>
    body {
        background-color: #f8f9fa;
        background-image: none;
    }
    
    /* Remove background image in dark mode */
    .dark body {
        background-image: none;
        background-color: #1f2937;
    }

    /* Contact info card styling */
    .contact-info-card {
        background: none;
        border-radius: 1.5rem;
        padding: 3rem;
    }

    .dark .contact-info-card {
        background: linear-gradient(135deg, #4c1d95 0%, #5b21b6 100%);
    }

    /* Form card styling */
    .form-card {
        background: white;
        border-radius: 1.5rem;
        padding: 3rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
    }

    .dark .form-card {
        background: #1f2937;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }

    /* Button styling */
    .submit-btn {
        background: #2563eb;
        color:white;
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    /* Icon styling */
    .contact-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-8 items-start">
            
            <!-- Left Side - Contact Information -->
            <div class="contact-info-card text-blue-950">
                <div class="mb-8">
                    <p class="text-sm font-sm uppercase tracking-wider my-6 opacity-90">
                        WE'RE HERE TO HELP YOU
                    </p>
                    <h1 class="text-3xl lg:text-4xl font-bold leading-tight mb-3">
                        Discuss Your<br>
                        Cleaning Service
                        Needs
                    </h1>
                    <p class="text-md opacity-90 leading-relaxed">
                        Are you looking for top-quality cleaning solutions tailored to your needs? Reach out to us.
                    </p>
                </div>

                <div class="space-y-6 mt-12">
                    <!-- Email -->
                    <div class="flex items-start gap-4">
                        <div class="contact-icon flex-shrink-0">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm opacity-80 mb-1">E-mail</p>
                            <p class="text-sm font-medium">info@finnoys.com</p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="flex items-start gap-4">
                        <div class="contact-icon flex-shrink-0">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm opacity-80 mb-1">Phone number</p>
                            <p class="text-sm font-medium">+358 123 456 789</p>
                        </div>
                    </div>

                    <!-- Address -->
                    {{-- <div class="flex items-start gap-4">
                        <div class="contact-icon flex-shrink-0">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm opacity-80 mb-1">Address</p>
                            <p class="text-sm font-medium">123 Cleaning Street<br>Helsinki, Finland</p>
                        </div>
                    </div> --}}

                    <!-- Business Hours -->
                    <div class="flex items-start gap-4">
                        <div class="contact-icon flex-shrink-0">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm opacity-80 mb-1">Business Hours</p>
                            <p class="text-sm font-medium">Monday - Friday: 8:00 AM - 6:00 PM</p>
                            <p class="text-sm font-medium">Saturday: 9:00 AM - 4:00 PM</p>
                            <p class="text-sm font-medium">Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Contact Form -->
            <div class="form-card" x-data="{
                form: { name: '', email: '', service: '', message: '' },
                submitContact() {
                    const missing = [];
                    if (!this.form.name.trim()) missing.push('Name');
                    if (!this.form.email.trim()) missing.push('Email');
                    if (!this.form.service) missing.push('Service Type');
                    if (!this.form.message.trim()) missing.push('Detailed Concern');

                    if (missing.length > 0) {
                        if (window.showErrorDialog) window.showErrorDialog('Missing Fields', 'Please fill in the following: ' + missing.join(', '));
                        return;
                    }

                    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRe.test(this.form.email.trim())) {
                        if (window.showErrorDialog) window.showErrorDialog('Invalid Email', 'Please enter a valid email address.');
                        return;
                    }

                    if (window.showSuccessDialog) window.showSuccessDialog('Message Sent', 'Thank you for reaching out! We will get back to you shortly.');
                    this.form = { name: '', email: '', service: '', message: '' };
                }
            }">
                <form @submit.prevent="submitContact()" class="space-y-6">
                    <!-- Name -->
                    <x-material-ui.input-field
                        label="Name"
                        type="text"
                        model="form.name"
                        icon="fa-solid fa-user"
                        placeholder="e.g. Juan Dela Cruz"
                        required
                    />

                    <!-- Email -->
                    <x-material-ui.input-field
                        label="Email"
                        type="email"
                        model="form.email"
                        icon="fa-solid fa-envelope"
                        placeholder="email@example.com"
                        required
                    />

                    <!-- Industry/Service Type -->
                    <div class="relative" x-data="{ focused: false, filled: false }" x-init="$watch('form.service', v => filled = !!v)">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 z-[1] text-blue-600 dark:text-blue-600">
                            <i class="fa-solid fa-briefcase text-sm"></i>
                        </span>
                        <select x-model="form.service"
                            @focus="focused = true"
                            @blur="focused = false"
                            class="mui-input peer w-full pl-10 pr-4 pt-5 pb-2 text-sm
                                   border border-gray-400 dark:border-gray-700 rounded-xl
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   transition-all duration-200
                                   focus:outline-none focus:border-blue-500 dark:focus:border-blue-400
                                   focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)] dark:focus:shadow-[0_0_0_3px_rgba(96,165,250,0.1)]"
                            :class="!form.service ? 'text-transparent' : ''">
                            <option value="">Select a service</option>
                            <option value="residential">Residential Cleaning</option>
                            <option value="commercial">Commercial Cleaning</option>
                            <option value="industrial">Industrial Cleaning</option>
                            <option value="specialized">Specialized Services</option>
                            <option value="other">Other</option>
                        </select>
                        <label class="absolute left-10 pointer-events-none transition-all duration-200 origin-left text-gray-400 dark:text-gray-500"
                            :class="(focused || filled || form.service) ? 'top-1.5 translate-y-0 text-[11px] ' + (focused ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500') : 'top-1/2 -translate-y-1/2 text-sm'">
                            Service Type <span class="text-red-500">*</span>
                        </label>
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fa-solid fa-comment-dots text-blue-600 mr-1.5"></i>Detailed Concern <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            x-model="form.message"
                            rows="5"
                            required
                            placeholder="Tell us about your cleaning needs..."
                            class="w-full pl-4 pr-4 py-3 text-sm
                                   border border-gray-400 dark:border-gray-700 rounded-xl
                                   bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                   placeholder-gray-400 dark:placeholder-gray-500
                                   transition-all duration-200 resize-none
                                   focus:outline-none focus:border-blue-500 dark:focus:border-blue-400
                                   focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)] dark:focus:shadow-[0_0_0_3px_rgba(96,165,250,0.1)]"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="submit-btn w-full text-white text-sm font-medium py-4 px-6 rounded-full flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Get a Solution
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

