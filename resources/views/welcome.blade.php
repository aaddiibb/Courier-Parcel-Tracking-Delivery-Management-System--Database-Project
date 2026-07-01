<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Courier DB') }} — Parcel Tracking &amp; Delivery</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    <header class="bg-white border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
            <span class="text-xl font-bold text-indigo-700">📦 Courier DB</span>
            <nav class="flex items-center gap-3 text-sm">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="px-4 py-1.5 border border-gray-300 rounded-lg hover:border-gray-400 transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-1.5 text-gray-700 hover:text-indigo-700 transition-colors">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="px-4 py-1.5 border border-gray-300 rounded-lg hover:border-gray-400 transition-colors">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-16">

        {{-- Welcome --}}
        <div class="text-center mb-14">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">
                Courier Parcel Tracking &amp; Delivery Management System
            </h1>
            <p class="text-gray-500 max-w-xl mx-auto mb-6">
                Book, route, and deliver parcels across our branch network — log in to get started.
            </p>
            <a href="{{ route('login') }}"
               class="inline-block bg-indigo-700 text-white font-semibold px-6 py-3 rounded-lg text-sm hover:bg-indigo-800 transition-colors">
                Log in to continue
            </a>
        </div>

        {{-- What we do --}}
        <div class="grid sm:grid-cols-3 gap-4 text-center">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="text-3xl mb-2">📮</div>
                <h3 class="font-semibold text-gray-800 mb-1">Book Parcels</h3>
                <p class="text-sm text-gray-500">Customers book, we handle the rest.</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="text-3xl mb-2">📍</div>
                <h3 class="font-semibold text-gray-800 mb-1">Real-time Tracking</h3>
                <p class="text-sm text-gray-500">Know exactly where your parcel is.</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="text-3xl mb-2">🏢</div>
                <h3 class="font-semibold text-gray-800 mb-1">Branch Network</h3>
                <p class="text-sm text-gray-500">Delivered across major cities.</p>
            </div>
        </div>

    </main>

</body>
</html>
