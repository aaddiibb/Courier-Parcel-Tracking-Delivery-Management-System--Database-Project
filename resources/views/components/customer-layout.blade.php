<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — My Account</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased bg-slate-50">

<div class="min-h-screen bg-slate-50 flex">

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside class="w-64 shrink-0 bg-white border-r border-slate-200 flex flex-col fixed inset-y-0 left-0 z-30">

        <div class="h-16 flex items-center px-6 border-b border-slate-200 shrink-0">
            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2">
                <div class="w-7 h-7 bg-indigo-600 rounded-md flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                </div>
                <span class="font-bold text-slate-900 text-base">CourierDB</span>
            </a>
        </div>

        @php
        $navItems = [
            ['route' => 'customer.dashboard',       'label' => 'My Parcels',    'pattern' => 'customer.dashboard',
             'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
            ['route' => 'customer.parcels.index',   'label' => 'All Shipments', 'pattern' => 'customer.parcels.index',
             'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z'],
            ['route' => 'customer.parcels.create',  'label' => 'Book a Parcel', 'pattern' => 'customer.parcels.create',
             'icon' => 'M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['route' => 'customer.receivers.index', 'label' => 'My Addresses',  'pattern' => 'customer.receivers.*',
             'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
        ];
        @endphp

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="{{ $active ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} flex items-center gap-3 pl-3 pr-3 py-2.5 border-l-4 text-sm font-medium transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="border-t border-slate-200 p-4 shrink-0">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-slate-900 text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-slate-400 text-xs">Customer</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 text-slate-500 hover:text-slate-900 text-xs px-2 py-1.5 rounded transition-colors hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    Sign out
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main ────────────────────────────────────────────────────────── --}}
    <div class="ml-64 flex-1 flex flex-col min-h-screen">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center px-6 sticky top-0 z-20">
            @isset($header)
                <div class="text-slate-900 font-semibold text-sm">{{ $header }}</div>
            @endisset
            <div class="ml-auto flex items-center gap-2">
                <span class="text-xs text-slate-400">{{ now()->format('D, d M Y') }}</span>
            </div>
        </header>

        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>

</div>

</body>
</html>
