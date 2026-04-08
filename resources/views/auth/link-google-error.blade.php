<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} · CastCrew</title>
    <link rel="icon" href="{{ asset('images/icons/castcrew/castcrew-pic-logo.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-8">
        <div class="text-center">
            <div class="mx-auto w-14 h-14 flex items-center justify-center rounded-full bg-red-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-semibold text-gray-900">{{ $title }}</h2>
            <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $message }}</p>
        </div>

        <div class="mt-6 space-y-3">
            <a href="{{ $dashboardUrl }}"
                class="block w-full text-center py-2 px-4 rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                Return to Dashboard
            </a>
            <a href="{{ route('employee.link-google') }}"
                class="block w-full text-center py-2 px-4 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Try a Different Google Account
            </a>
        </div>
    </div>
</div>
</body>
</html>
