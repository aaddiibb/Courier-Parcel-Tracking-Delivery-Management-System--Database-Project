<x-branch-layout :branch-name="$branchName">
    <x-slot name="header">Parcels</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Parcels at My Branch</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $branchName }} — origin or destination</p>
    </div>

    <div class="space-y-6">

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <form method="GET" action="{{ route('branch.parcels.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <x-input-label value="Status" />
                    <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">All statuses</option>
                        @foreach($statusOptions as $opt)
                            <option value="{{ $opt }}" @selected($status === $opt)>{{ str_replace('_', ' ', $opt) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <x-input-label value="Search" />
                    <x-text-input type="text" name="search" :value="$search" placeholder="Tracking code or sender name" class="w-full" />
                </div>
                <div>
                    <x-input-label value="Booked From" />
                    <x-text-input type="date" name="date_from" :value="$dateFrom" />
                </div>
                <div>
                    <x-input-label value="Booked To" />
                    <x-text-input type="date" name="date_to" :value="$dateTo" />
                </div>
                <div>
                    <x-input-label value="Sort By" />
                    <select name="sort" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="booked_at" @selected($sort === 'booked_at')>Booked Date</option>
                        <option value="current_status" @selected($sort === 'current_status')>Status</option>
                    </select>
                </div>
                <div>
                    <x-input-label value="Direction" />
                    <select name="dir" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="desc" @selected($dir === 'desc')>Descending</option>
                        <option value="asc" @selected($dir === 'asc')>Ascending</option>
                    </select>
                </div>
                <x-primary-button type="submit">Filter</x-primary-button>
                @if($status || $search || $dateFrom || $dateTo)
                    <a href="{{ route('branch.parcels.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            @if(empty($parcels))
                <div class="py-12 text-center text-slate-400">
                    <p class="text-sm">No parcels found for this branch.</p>
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
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Rider</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($parcels as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">{{ $p->tracking_code }}</td>
                            <td class="px-4 py-3 text-slate-900">{{ $p->sender_name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p->origin_city }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p->destination_city }}</td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$p->current_status" />
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-400 whitespace-nowrap">{{ $p->booked_at }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p->rider_name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('branch.parcels.show', $p->parcel_id) }}"
                                   class="text-indigo-600 hover:text-indigo-800 hover:underline">View</a>
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
