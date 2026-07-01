<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-gray-100">

{{-- ── Top navbar ───────────────────────────────────────────────────────── --}}
<nav class="bg-gray-900 shadow-md">
    <div class="mx-auto px-6">
        <div class="flex items-center h-14 gap-6">

            {{-- Brand --}}
            <a href="{{ route('admin.dashboard') }}"
               class="text-white font-bold text-base tracking-tight shrink-0 mr-2">
                Courier Admin
            </a>

            {{-- Nav links --}}
            @php
            $navItems = [
                ['route' => 'admin.dashboard',       'label' => 'Dashboard', 'pattern' => 'admin.dashboard'],
                ['route' => 'admin.customers.index', 'label' => 'Customers', 'pattern' => 'admin.customers.*'],
                ['route' => 'admin.branches.index',  'label' => 'Branches',  'pattern' => 'admin.branches.*'],
                ['route' => 'admin.riders.index',    'label' => 'Riders',    'pattern' => 'admin.riders.*'],
                ['route' => 'admin.parcels.index',   'label' => 'Parcels',   'pattern' => 'admin.parcels.*'],
            ];
            @endphp

            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="text-sm font-medium px-3 py-1.5 rounded-md transition-colors
                          {{ $active
                             ? 'bg-indigo-600 text-white'
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach

            {{-- Lab Demos dropdown --}}
            @php $labActive = request()->is('lab/*'); @endphp
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-1 text-sm font-medium px-3 py-1.5 rounded-md transition-colors
                               {{ $labActive ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    Lab Demos
                    <svg class="w-3.5 h-3.5 transition-transform duration-150"
                         :class="{ 'rotate-180': open }"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-cloak
                     class="absolute left-0 top-full mt-1 w-52 bg-gray-800 rounded-lg shadow-lg overflow-hidden z-50 border border-gray-700">
                    @php
                    $labLinks = [
                        ['/lab/joins',      'Joins (Labs 9 & 10)'],
                        ['/lab/aggregates', 'Aggregates (Labs 11-12)'],
                        ['/lab/subqueries', 'Subqueries (Labs 13-14)'],
                        ['/lab/plsql',      'PL/SQL Basics (Lab 11)'],
                    ];
                    @endphp
                    @foreach($labLinks as [$url, $label])
                        <a href="{{ $url }}"
                           class="block px-4 py-2.5 text-sm transition-colors
                                  {{ request()->is(ltrim($url, '/'))
                                     ? 'text-white font-medium bg-gray-700'
                                     : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Spacer --}}
            <div class="flex-1"></div>

            {{-- User + logout --}}
            <div class="flex items-center gap-4 shrink-0">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-medium text-gray-200">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-xs text-gray-400 hover:text-white transition-colors px-3 py-1.5 rounded-md hover:bg-gray-800">
                        Log Out
                    </button>
                </form>
            </div>

        </div>
    </div>
</nav>

{{-- ── Page header ─────────────────────────────────────────────────────── --}}
@isset($header)
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-6 py-4">{{ $header }}</div>
    </header>
@endisset

{{-- ── Main content ────────────────────────────────────────────────────── --}}
<main>
    {{ $slot }}
</main>

</body>
</html>
