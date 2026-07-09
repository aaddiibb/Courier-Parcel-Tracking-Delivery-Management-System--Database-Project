<x-admin-layout>
    <x-slot name="header">Analytics</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Analytics</h1>
        <p class="mt-1 text-sm text-slate-500">Performance overview across branches, riders, and parcels</p>
    </div>

    <div class="space-y-6">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <p class="text-sm font-medium text-slate-500">Delivery Success Rate</p>
                <p class="mt-1 text-3xl font-bold text-amber-600">{{ $successRate }}%</p>
                <p class="text-xs text-slate-400 mt-1">of all parcels ever booked</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <p class="text-sm font-medium text-slate-500">Avg. Delivery Time</p>
                <p class="mt-1 text-3xl font-bold text-slate-900">{{ $avgDeliveryDays }}</p>
                <p class="text-xs text-slate-400 mt-1">days, booked → delivered</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <p class="text-sm font-medium text-slate-500">Busiest Branch</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $busiestBranch }}</p>
                <p class="text-xs text-slate-400 mt-1">by parcels originated</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <p class="text-sm font-medium text-slate-500">Top Rider</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $topRider }}</p>
                <p class="text-xs text-slate-400 mt-1">
                    @if($topRiderRate !== null)
                        {{ $topRiderRate }}% success rate (5+ attempts)
                    @else
                        no rider has 5+ delivery attempts yet
                    @endif
                </p>
            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <a href="{{ route('admin.analytics.branches') }}"
               class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 hover:border-indigo-300 hover:shadow-md transition-all group">
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-indigo-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                </div>
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-900 group-hover:text-indigo-700">Branch Performance</h3>
                    <span class="text-indigo-600">→</span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Revenue, delivery success, and underperforming branches.</p>
            </a>

            <a href="{{ route('admin.analytics.riders') }}"
               class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 hover:border-indigo-300 hover:shadow-md transition-all group">
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-indigo-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                    </svg>
                </div>
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-900 group-hover:text-indigo-700">Rider Performance</h3>
                    <span class="text-indigo-600">→</span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Leaderboard and riders who need attention.</p>
            </a>

            <a href="{{ route('admin.analytics.parcels') }}"
               class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 hover:border-indigo-300 hover:shadow-md transition-all group">
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-indigo-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                </div>
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-900 group-hover:text-indigo-700">Parcel Intelligence</h3>
                    <span class="text-indigo-600">→</span>
                </div>
                <p class="text-sm text-slate-500 mt-1">Status funnel, stuck parcels, and weight-band pricing review.</p>
            </a>
        </div>

    </div>
</x-admin-layout>
