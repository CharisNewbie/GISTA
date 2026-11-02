<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

        <title>Gista - Connect. Share. Discover.</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-gista-500 via-gista-700 to-gista-900 px-6">
            <div class="w-full max-w-sm flex flex-col items-center text-center">
                <!-- Logo -->
                <h1 class="text-6xl font-extrabold text-white tracking-tight mb-3">Gista</h1>

                <!-- Tagline -->
                <p class="text-gista-100 text-lg font-medium mb-16">Connect. Share. Discover.</p>

                <!-- CTA Buttons -->
                <div class="w-full space-y-3">
                    <a href="{{ route('register') }}" class="block w-full py-3.5 rounded-xl bg-white text-gista-700 font-semibold text-center shadow-lg hover:bg-gista-50 transition">
                        Get Started
                    </a>
                    <a href="{{ route('login') }}" class="block w-full py-3.5 rounded-xl bg-white/15 text-white font-semibold text-center backdrop-blur-sm border border-white/25 hover:bg-white/25 transition">
                        Sign In
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
