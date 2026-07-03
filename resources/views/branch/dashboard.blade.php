<x-branch-layout :branch-name="$branchName">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    @php
    $statusColors = [
        'BOOKED'           => 'bg-blue-100 text-blue-800',
        'IN_TRANSIT'       => 'bg-yellow-100 text-yellow-800',
        'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
        'DELIVERED'        => 'bg-green-100 text-green-800',
        'RETURNED'         => 'bg-red-100 text-red-800',
    ];
    @endphp

    <div class="space-y-6">

        {{-- ── Stat cards ───────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalParcels }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Parcels at This Branch</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $todaysBookings }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Today's Bookings</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10m0 0h1m11 0h1m-7 0a2 2 0 100 4 2 2 0 000-4zm7 0a2 2 0 100 4 2 2 0 000-4zM3 16h14M9 6h5l3 4H9V6z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $pendingDeliveries }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Out for Delivery</div>
                </div>
            </div>

        </div>

        {{-- ── Status breakdown ────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Parcels by Status</h3>
            </div>
            <div class="p-5 grid grid-cols-2 sm:grid-cols-5 gap-4">
                @foreach($statusOptions as $status)
                    @php $cls = $statusColors[$status] ?? 'bg-gray-100 text-gray-800'; @endphp
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $countsByStatus[$status] }}</div>
                        <span class="mt-1 inline-block px-2 py-0.5 text-xs font-semibold rounded-full {{ $cls }}">
                            {{ str_replace('_', ' ', $status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Recent parcels ──────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Recent Parcels at This Branch</h3>
                <a href="{{ route('branch.parcels.index') }}"
                   class="text-xs font-medium text-teal-700 hover:text-teal-900">View all →</a>
            </div>
            @if(empty($recentParcels))
                <div class="px-5 py-12 text-center text-sm text-gray-400">
                    No parcels for this branch yet.
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Code</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Sender</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">From</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">To</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Booked</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentParcels as $p)
                        @php $cls = $statusColors[$p->current_status] ?? 'bg-gray-100 text-gray-800'; @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-100">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('branch.parcels.show', $p->parcel_id) }}"
                                   class="font-mono text-xs font-semibold text-teal-700 hover:text-teal-900 hover:underline">
                                    {{ $p->tracking_code }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-800">{{ $p->sender_name }}</td>
                            <td class="px-5 py-3.5 text-sm text-gray-500">{{ $p->origin_city }}</td>
                            <td class="px-5 py-3.5 text-sm text-gray-500">{{ $p->destination_city }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $cls }}">
                                    {{ $p->current_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-gray-400 whitespace-nowrap">{{ $p->booked_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>
</x-branch-layout>
