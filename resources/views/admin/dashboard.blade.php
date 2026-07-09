<x-admin-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">Overview of parcels, revenue, and fleet activity</p>
        </div>
        <span class="text-sm text-slate-500">{{ now()->format('D, d M Y') }}</span>
    </div>

    <div class="space-y-6">

        {{-- ── Stat cards ───────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Parcels</p>
                        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalParcels }}</p>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-indigo-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">In Transit</p>
                        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $inTransit }}</p>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Today's Revenue</p>
                        <p class="mt-1 text-3xl font-bold text-amber-600">৳{{ number_format($todaysRevenue, 0) }}</p>
                    </div>
                    <div class="p-2 bg-amber-50 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-amber-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Active Riders</p>
                        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $activeRiders }}</p>
                    </div>
                    <div class="p-2 bg-emerald-50 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-emerald-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Lower panels ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

            {{-- Status filter --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Parcels by Status</h3>
                </div>
                <div class="px-5 pt-4">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-end gap-2">
                        <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                        <input type="hidden" name="date_to" value="{{ $dateTo }}">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                            <select name="status"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                @foreach($statusOptions as $opt)
                                    <option value="{{ $opt }}" @selected($selectedStatus === $opt)>
                                        {{ str_replace('_', ' ', $opt) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                            Filter
                        </button>
                    </form>
                </div>
                <div class="p-5 flex-1 flex flex-col items-center justify-center text-center">
                    <x-status-badge :status="$selectedStatus" class="mb-3" />
                    <div class="text-5xl font-bold text-slate-900">{{ $statusCount }}</div>
                    <div class="text-xs text-slate-500 mt-1">parcel{{ $statusCount === 1 ? '' : 's' }}</div>
                </div>
            </div>

            {{-- Date-range filter --}}
            <div class="lg:col-span-3 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">
                        Parcels Booked {{ $dateFrom }} &rarr; {{ $dateTo }}
                    </h3>
                    <a href="{{ route('admin.parcels.index') }}"
                       class="text-xs font-medium text-indigo-600 hover:text-indigo-800">View all →</a>
                </div>
                <div class="px-5 pt-4">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-end gap-2">
                        <input type="hidden" name="status" value="{{ $selectedStatus }}">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Booked From</label>
                            <input type="date" name="date_from" value="{{ $dateFrom }}"
                                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Booked To</label>
                            <input type="date" name="date_to" value="{{ $dateTo }}"
                                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                            Filter
                        </button>
                    </form>
                </div>
                @if(empty($recentParcels))
                    <div class="py-12 text-center text-slate-400">
                        <p class="text-sm">No parcels booked in this date range.</p>
                    </div>
                @else
                <div class="overflow-x-auto flex-1 mt-4">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200">
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Code</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Sender</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">To</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Booked</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentParcels as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.parcels.show', $p->parcel_id) }}"
                                       class="font-mono text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ $p->tracking_code }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-slate-900">{{ $p->sender_name }}</td>
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
    </div>
</x-admin-layout>
