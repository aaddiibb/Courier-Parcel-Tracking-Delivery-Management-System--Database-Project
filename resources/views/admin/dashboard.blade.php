<x-admin-layout>
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

    <div class="py-8 px-6 space-y-6">

        {{-- ── Stat cards ───────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalParcels }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Total Parcels</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-yellow-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10m0 0h1m11 0h1m-7 0a2 2 0 100 4 2 2 0 000-4zm7 0a2 2 0 100 4 2 2 0 000-4zM3 16h14M9 6h5l3 4H9V6z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $inTransit }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">In Transit</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">৳{{ number_format($todaysRevenue, 0) }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Today's Revenue</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $activeRiders }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Active Riders</div>
                </div>
            </div>

        </div>

        {{-- ── Lower panels ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

            {{-- Status filter --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">Parcels by Status</h3>
                </div>
                <div class="px-5 pt-4">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-end gap-2">
                        <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                        <input type="hidden" name="date_to" value="{{ $dateTo }}">
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                            <select name="status"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($statusOptions as $opt)
                                    <option value="{{ $opt }}" @selected($selectedStatus === $opt)>
                                        {{ str_replace('_', ' ', $opt) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            Filter
                        </button>
                    </form>
                </div>
                <div class="p-5 flex-1 flex flex-col items-center justify-center text-center">
                    @php $cls = $statusColors[$selectedStatus] ?? 'bg-gray-100 text-gray-800'; @endphp
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $cls }} mb-3">
                        {{ str_replace('_', ' ', $selectedStatus) }}
                    </span>
                    <div class="text-5xl font-bold text-gray-900">{{ $statusCount }}</div>
                    <div class="text-xs text-gray-500 mt-1">parcel{{ $statusCount === 1 ? '' : 's' }}</div>
                </div>
            </div>

            {{-- Date-range filter --}}
            <div class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">
                        Parcels Booked {{ $dateFrom }} &rarr; {{ $dateTo }}
                    </h3>
                    <a href="{{ route('admin.parcels.index') }}"
                       class="text-xs font-medium text-indigo-600 hover:text-indigo-800">View all →</a>
                </div>
                <div class="px-5 pt-4">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-end gap-2">
                        <input type="hidden" name="status" value="{{ $selectedStatus }}">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Booked From</label>
                            <input type="date" name="date_from" value="{{ $dateFrom }}"
                                   class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Booked To</label>
                            <input type="date" name="date_to" value="{{ $dateTo }}"
                                   class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            Filter
                        </button>
                    </form>
                </div>
                @if(empty($recentParcels))
                    <div class="px-5 py-12 text-center text-sm text-gray-400">
                        No parcels booked in this date range.
                    </div>
                @else
                <div class="overflow-x-auto flex-1">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Code</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Sender</th>
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
                                    <a href="{{ route('admin.parcels.show', $p->parcel_id) }}"
                                       class="font-mono text-xs font-semibold text-indigo-600 hover:text-indigo-900 hover:underline">
                                        {{ $p->tracking_code }}
                                    </a>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-800">{{ $p->sender_name }}</td>
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
    </div>
</x-admin-layout>
