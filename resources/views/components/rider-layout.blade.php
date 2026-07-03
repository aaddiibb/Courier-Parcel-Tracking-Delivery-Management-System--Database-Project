<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Rider</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-slate-50">

<div class="min-h-screen flex flex-col">

    {{-- ── Top bar ─────────────────────────────────────────────────────── --}}
    <header class="bg-slate-900 text-white sticky top-0 z-10 shadow-md">
        <div class="px-4 py-3 flex items-center justify-between">
            <a href="{{ route('rider.dashboard') }}" class="font-bold text-base">🏍️ My Deliveries</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-slate-300 hover:text-white px-2 py-1">Log Out</button>
            </form>
        </div>
        @isset($header)
            <div class="px-4 pb-3">{{ $header }}</div>
        @endisset
    </header>

    {{-- ── Main ────────────────────────────────────────────────────────── --}}
    <main class="flex-1 px-4 py-4 pb-24 max-w-xl mx-auto w-full">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3">
                {{ session('success') }}
            </div>
        @endif
        {{ $slot }}
    </main>

    {{-- ── Bottom nav (mobile-friendly, large tap targets) ────────────── --}}
    <nav class="fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 flex shadow-lg">
        @php
        $navItems = [
            ['route' => 'rider.dashboard', 'label' => 'My Deliveries', 'pattern' => 'rider.dashboard', 'icon' => '📦'],
        ];
        @endphp
        @foreach($navItems as $item)
            @php $active = request()->routeIs($item['pattern']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex-1 flex flex-col items-center justify-center gap-1 py-3 text-xs font-medium transition-colors
                      {{ $active ? 'text-slate-900' : 'text-slate-400' }}">
                <span class="text-xl">{{ $item['icon'] }}</span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

</div>

</body>
</html>
