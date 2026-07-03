<x-branch-layout :branch-name="$branchName">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Parcels at My Branch</h2>
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

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <form method="GET" action="{{ route('branch.parcels.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                    <select name="status" class="rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                        <option value="">All statuses</option>
                        @foreach($statusOptions as $opt)
                            <option value="{{ $opt }}" @selected($status === $opt)>{{ str_replace('_', ' ', $opt) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Search</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Tracking code or sender name"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-teal-700 text-white text-sm font-medium rounded-lg hover:bg-teal-800 transition-colors">
                    Filter
                </button>
                @if($status || $search)
                    <a href="{{ route('branch.parcels.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            @if(empty($parcels))
                <div class="px-5 py-12 text-center text-sm text-gray-400">
                    No parcels found for this branch.
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
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Rider</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($parcels as $p)
                        @php $cls = $statusColors[$p->current_status] ?? 'bg-gray-100 text-gray-800'; @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-100">
                            <td class="px-5 py-3.5 font-mono text-xs font-semibold text-teal-700">{{ $p->tracking_code }}</td>
                            <td class="px-5 py-3.5 text-sm text-gray-800">{{ $p->sender_name }}</td>
                            <td class="px-5 py-3.5 text-sm text-gray-500">{{ $p->origin_city }}</td>
                            <td class="px-5 py-3.5 text-sm text-gray-500">{{ $p->destination_city }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $cls }}">
                                    {{ $p->current_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-gray-400 whitespace-nowrap">{{ $p->booked_at }}</td>
                            <td class="px-5 py-3.5 text-sm text-gray-500">{{ $p->rider_name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-sm">
                                <a href="{{ route('branch.parcels.show', $p->parcel_id) }}"
                                   class="text-teal-700 hover:text-teal-900 hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>
</x-branch-layout>
