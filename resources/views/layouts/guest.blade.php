<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Casino Fortuna') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-red-900 via-gray-900 to-black pt-6 sm:pt-0">
        <div class="mb-4">
            <a href="/" class="flex items-center gap-2 text-3xl font-bold text-amber-400">
                🎰 Casino Fortuna
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-2 px-6 py-6 bg-white shadow-xl overflow-hidden sm:rounded-2xl border-t-4 border-amber-500">
            {{ $slot }}
        </div>
    </div>
</body>
</html>