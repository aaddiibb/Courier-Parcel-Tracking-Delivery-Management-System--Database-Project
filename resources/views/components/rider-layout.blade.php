<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Rider</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-slate-50">

<div class="min-h-screen flex flex-col">

    {{-- ── Top bar ─────────────────────────────────────────────────────── --}}
    <header class="bg-slate-900 text-white sticky top-0 z-10 shadow-md">
        <div class="px-4 py-3 flex items-center justify-between">
            <a href="{{ route('rider.dashboard') }}" class="flex items-center gap-2 font-bold text-base">
                <div class="w-7 h-7 bg-indigo-500 rounded-md flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                    </svg>
                </div>
                CourierDB
            </a>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-medium text-slate-200">
                        {{ auth()->user()->name }}
                        <span class="ml-1 px-1.5 py-0.5 text-[10px] font-semibold rounded bg-slate-700 text-slate-300 align-middle">Rider</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-slate-300 hover:text-white px-2 py-1">Log Out</button>
                </form>
            </div>
        </div>
        @isset($header)
            <div class="px-4 pb-3 font-semibold text-lg text-white">{{ $header }}</div>
        @endisset
    </header>

    {{-- ── Main ────────────────────────────────────────────────────────── --}}
    <main class="flex-1 px-4 py-4 pb-24 max-w-xl mx-auto w-full">
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg px-4 py-3">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg px-4 py-3">
                {{ session('error') }}
            </div>
        @endif
        {{ $slot }}
    </main>

    {{-- ── Bottom nav (mobile-friendly, large tap targets) ────────────── --}}
    <nav class="fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 flex shadow-lg">
        <a href="{{ route('rider.dashboard') }}"
           class="flex-1 flex flex-col items-center justify-center gap-1 py-3 text-xs font-medium transition-colors
                  {{ request()->routeIs('rider.dashboard') ? 'text-indigo-600' : 'text-slate-400' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
            </svg>
            My Deliveries
        </a>
    </nav>

</div>

</body>
</html>
