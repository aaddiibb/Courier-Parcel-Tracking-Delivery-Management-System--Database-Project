<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Branch Manager</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-teal-50/40">

<div class="min-h-screen flex">

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside class="w-64 shrink-0 bg-white border-r border-teal-100 flex flex-col">
        <a href="{{ route('branch.dashboard') }}" class="px-6 py-5 border-b border-teal-100">
            <span class="text-lg font-bold text-teal-700">🏢 Branch Manager</span>
        </a>

        @php
        $navItems = [
            ['route' => 'branch.dashboard',      'label' => 'Dashboard',          'pattern' => 'branch.dashboard',      'icon' => '🏠'],
            ['route' => 'branch.parcels.index',  'label' => 'Parcels',            'pattern' => 'branch.parcels.*',      'icon' => '📦'],
        ];
        @endphp

        <nav class="flex-1 px-3 py-4 space-y-1">
            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ $active ? 'bg-teal-100 text-teal-800' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-800' }}">
                    <span>{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="px-4 py-4 border-t border-teal-100">
            <div class="mb-2">
                <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-gray-500 hover:text-teal-800 transition-colors">
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main ────────────────────────────────────────────────────────── --}}
    <div class="flex-1 min-w-0">
        <header class="bg-white border-b border-teal-100">
            <div class="px-8 py-5 flex items-center justify-between">
                <div>
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>
                <div class="text-sm font-medium text-teal-700">
                    {{ $branchName ?? 'Branch' }} — Branch Manager
                </div>
            </div>
        </header>

        <main class="p-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>

</div>

</body>
</html>
