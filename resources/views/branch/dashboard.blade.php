<x-branch-layout :branch-name="$branchName">
    <x-slot name="header">Dashboard</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">{{ $branchName }}</h1>
        <p class="mt-1 text-sm text-slate-500">Branch overview and recent activity</p>
    </div>

    <div class="space-y-6">

        {{-- ── Stat cards ───────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-indigo-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900">{{ $totalParcels }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Parcels at This Branch</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900">{{ $todaysBookings }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Today's Bookings</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-amber-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10m0 0h1m11 0h1m-7 0a2 2 0 100 4 2 2 0 000-4zm7 0a2 2 0 100 4 2 2 0 000-4zM3 16h14M9 6h5l3 4H9V6z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900">{{ $pendingDeliveries }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Out for Delivery</div>
                </div>
            </div>

        </div>

        {{-- ── Status breakdown ────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-5 py-4 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Parcels by Status</h3>
            </div>
            <div class="p-5 grid grid-cols-2 sm:grid-cols-5 gap-4">
                @foreach($statusOptions as $status)
                    <div class="text-center">
                        <div class="text-2xl font-bold text-slate-900">{{ $countsByStatus[$status] }}</div>
                        <x-status-badge :status="$status" class="mt-1 inline-flex" />
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Recent parcels ──────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Recent Parcels at This Branch</h3>
                <a href="{{ route('branch.parcels.index') }}"
                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800">View all &rarr;</a>
            </div>
            @if(empty($recentParcels))
                <div class="py-12 text-center text-slate-400">
                    <p class="text-sm">No parcels for this branch yet.</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Code</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Sender</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">From</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">To</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Booked</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentParcels as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('branch.parcels.show', $p->parcel_id) }}"
                                   class="font-mono text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                    {{ $p->tracking_code }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-900">{{ $p->sender_name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p->origin_city }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p->destination_city }}</td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$p->current_status" />
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-400 whitespace-nowrap">{{ $p->booked_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>
</x-branch-layout>
