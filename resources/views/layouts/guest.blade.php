<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PAYDUE') }}</title>

        <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-gista-600 via-gista-700 to-gista-900 px-4">
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" wire:navigate>
                    <h1 class="text-4xl font-bold text-white tracking-tight">PAYDUE</h1>
                </a>
            </div>

            <!-- Form Card -->
            <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl p-6">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
