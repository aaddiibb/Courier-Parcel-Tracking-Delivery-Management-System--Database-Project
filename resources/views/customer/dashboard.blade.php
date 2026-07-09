<x-customer-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="space-y-6">

        {{-- Welcome banner --}}
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-xl p-6 text-white">
            <h1 class="text-xl font-bold">Welcome back, {{ Auth::user()->name }}</h1>
            <p class="text-indigo-200 text-sm mt-1">Here's what's happening with your shipments.</p>
        </div>

        @if(!$customer)
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                <strong>Note:</strong> Your account is not yet linked to any customer record. Please contact support so we can link your account.
            </div>
        @endif

        {{-- Stats Row --}}
        @php
            $total     = array_sum($stats);
            $delivered = $stats['DELIVERED'] ?? 0;
            $inTransit = ($stats['IN_TRANSIT'] ?? 0) + ($stats['OUT_FOR_DELIVERY'] ?? 0);
            $booked    = $stats['BOOKED'] ?? 0;
            $returned  = $stats['RETURNED'] ?? 0;
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Parcels</p>
                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $total }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">In Transit</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $inTransit }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Delivered</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $delivered }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Booked / Returned</p>
                <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $booked }} <span class="text-sm font-normal text-slate-400">/ {{ $returned }}</span></p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Spend</p>
                <p class="text-3xl font-bold text-amber-600 mt-1">৳{{ number_format($totalSpend, 2) }}</p>
            </div>
        </div>

        {{-- Recent Parcels --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Recent Parcels</h3>
                <a href="{{ route('customer.parcels.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                    View all &rarr;
                </a>
            </div>

            @if(empty($recentParcels))
                <div class="py-12 text-center text-slate-400">
                    <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="mt-3 text-sm">No parcels found yet.</p>
                    <p class="text-xs text-slate-400 mt-1">Your shipments will appear here once booked.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tracking Code</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Destination</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Booked</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentParcels as $p)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-3 font-mono font-semibold text-indigo-600">{{ $p->tracking_code }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $p->destination_city }}</td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="$p->current_status" />
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">{{ $p->booked_at }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('customer.parcels.show', $p->tracking_code) }}"
                                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                            Track &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Quick Track --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-1">Track a Parcel by Code</h3>
            <p class="text-slate-400 text-sm mb-4">Enter a tracking code from one of your own shipments.</p>
            <form method="POST" action="{{ route('customer.track') }}" class="flex gap-2">
                @csrf
                <input type="text" name="tracking_code" placeholder="e.g. CDB202600001"
                       class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm font-mono text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <x-primary-button type="submit">Track</x-primary-button>
            </form>
        </div>

    </div>
</x-customer-layout>
