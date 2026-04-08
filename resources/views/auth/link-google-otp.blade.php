<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Google Account · CastCrew</title>
    <link rel="icon" href="{{ asset('images/icons/castcrew/castcrew-pic-logo.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-8">
        <div class="text-center mb-6">
            <div class="mx-auto w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.657 1.343-3 3-3s3 1.343 3 3v2m-9 4h12a2 2 0 002-2v-5a2 2 0 00-2-2H6a2 2 0 00-2 2v5a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-semibold text-gray-900">Verify Your Google Account</h2>
            <p class="mt-2 text-sm text-gray-600">
                For your security, we sent a 6-digit verification code to
                <span class="font-medium text-gray-900">{{ $googleEmail }}</span>.
                Enter the code below to finish linking your Gmail account.
            </p>
            <p class="mt-1 text-xs text-gray-500">The code will expire in 5 minutes.</p>
        </div>

        @if (session('error'))
            <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('google.link.otp.verify') }}" class="space-y-4">
            @csrf
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700">Verification Code</label>
                <input id="otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                       autocomplete="one-time-code" autofocus required
                       class="mt-1 w-full text-center tracking-[0.5em] text-2xl font-semibold border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2"
                       placeholder="------">
            </div>

            <button type="submit"
                class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Verify & Link Google Account
            </button>
        </form>

        <form method="POST" action="{{ route('google.link.otp.resend') }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full text-sm text-blue-600 hover:text-blue-800 underline">
                Didn't receive a code? Resend
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ url('/') }}" class="text-xs text-gray-500 hover:text-gray-700">Cancel and return to dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
