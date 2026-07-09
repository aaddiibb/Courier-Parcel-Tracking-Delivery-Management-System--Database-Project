<x-admin-layout>
    <x-slot name="header">Parcels</x-slot>

    @php
    $activeFilters = array_filter($filters, fn ($v) => $v !== null && $v !== '');
    $statusOptions = ['BOOKED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'RETURNED'];
    $sortOptions = [
        'booked_at'      => 'Booked Date',
        'tracking_code'  => 'Tracking Code',
        'current_status' => 'Status',
        'weight_kg'      => 'Weight',
        'total_amount'   => 'Fee',
    ];
    $branchById = collect($branches)->keyBy('branch_id');
    $riderById  = collect($riders)->keyBy('rider_id');

    $filterLabels = [
        'tracking'      => fn ($v) => 'Tracking: '.$v,
        'status'        => fn ($v) => 'Status: '.str_replace('_', ' ', $v),
        'origin_branch' => fn ($v) => 'From: '.($branchById[$v]->branch_name ?? $v),
        'dest_branch'   => fn ($v) => 'To: '.($branchById[$v]->branch_name ?? $v),
        'rider_id'      => fn ($v) => 'Rider: '.($riderById[$v]->full_name ?? $v),
        'weight_min'    => fn ($v) => 'Min weight: '.$v.' kg',
        'weight_max'    => fn ($v) => 'Max weight: '.$v.' kg',
        'date_from'     => fn ($v) => 'From date: '.$v,
        'date_to'       => fn ($v) => 'To date: '.$v,
    ];

    $removeFilterUrl = function ($key) use ($activeFilters) {
        $remaining = collect($activeFilters)->except([$key, 'page'])->toArray();
        return route('admin.parcels.index').(count($remaining) ? '?'.http_build_query($remaining) : '');
    };

    $totalPages = max(1, (int) ceil($total / $perPage));
    $pageUrl = function ($page) use ($activeFilters) {
        $params = collect($activeFilters)->except('page')->toArray();
        $params['page'] = $page;
        return route('admin.parcels.index').'?'.http_build_query($params);
    };
    $rangeStart = $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
    $rangeEnd = min($currentPage * $perPage, $total);
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Parcels</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $total }} parcel{{ $total === 1 ? '' : 's' }} across all branches</p>
        </div>
        <a href="{{ route('admin.parcels.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + Book a Parcel
        </a>
    </div>

    {{-- ── Filter panel (always visible) ───────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-5">
        <form method="GET" action="{{ route('admin.parcels.index') }}" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 items-end">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tracking Code</label>
                <input type="text" name="tracking" value="{{ $filters['tracking'] ?? '' }}"
                       placeholder="e.g. CDB2026"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">Any status</option>
                    @foreach($statusOptions as $opt)
                        <option value="{{ $opt }}" @selected(($filters['status'] ?? null) === $opt)>{{ str_replace('_', ' ', $opt) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Origin Branch</label>
                <select name="origin_branch" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">Any branch</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->branch_id }}" @selected((string) ($filters['origin_branch'] ?? '') === (string) $b->branch_id)>{{ $b->branch_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Destination Branch</label>
                <select name="dest_branch" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">Any branch</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->branch_id }}" @selected((string) ($filters['dest_branch'] ?? '') === (string) $b->branch_id)>{{ $b->branch_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Rider</label>
                <select name="rider_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">Any rider</option>
                    @foreach($riders as $r)
                        <option value="{{ $r->rider_id }}" @selected((string) ($filters['rider_id'] ?? '') === (string) $r->rider_id)>{{ $r->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Min kg</label>
                    <input type="number" step="0.1" min="0" name="weight_min" value="{{ $filters['weight_min'] ?? '' }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Max kg</label>
                    <input type="number" step="0.1" min="0" name="weight_max" value="{{ $filters['weight_max'] ?? '' }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Booked From</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Booked To</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Sort By</label>
                <select name="sort" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @foreach($sortOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['sort'] ?? 'booked_at') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Direction</label>
                <select name="dir" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="desc" @selected(strtolower($filters['dir'] ?? 'desc') === 'desc')>Descending</option>
                    <option value="asc" @selected(strtolower($filters['dir'] ?? 'desc') === 'asc')>Ascending</option>
                </select>
            </div>

            <div class="flex items-center gap-3 col-span-2 md:col-span-4 lg:col-span-6">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('admin.parcels.index') }}" class="hover:bg-slate-100 text-slate-600 text-sm px-3 py-1.5 rounded-lg transition-colors">Clear</a>
            </div>

        </form>
    </div>

    {{-- ── Active filter pills ──────────────────────────────────────────── --}}
    @if(count($activeFilters))
    <div class="flex flex-wrap items-center gap-2 mb-4">
        @foreach($activeFilters as $key => $value)
            @if(isset($filterLabels[$key]))
                <a href="{{ $removeFilterUrl($key) }}"
                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                    {{ $filterLabels[$key]($value) }}
                    <span class="text-indigo-400">&times;</span>
                </a>
            @endif
        @endforeach
    </div>
    @endif

    {{-- ── Table ─────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tracking Code</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Sender</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Route</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Weight</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Booked At</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Rider</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Fee</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($parcels as $p)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">{{ $p->tracking_code }}</td>
                    <td class="px-4 py-3 text-slate-900">{{ $p->sender_name }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $p->origin_city }} → {{ $p->dest_city }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $p->weight_kg }} kg</td>
                    <td class="px-4 py-3">
                        <x-status-badge :status="$p->current_status" />
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $p->booked_at }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $p->rider_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $p->fee !== null ? '৳'.number_format($p->fee, 2) : '—' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.parcels.show', $p->parcel_id) }}" class="hover:bg-slate-100 text-slate-600 text-sm px-3 py-1.5 rounded-lg transition-colors">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="py-12 text-center text-slate-400">
                            <p class="text-sm">No parcels match these filters.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ────────────────────────────────────────────────────── --}}
    @if($total > 0)
    <div class="flex items-center justify-between mt-4 text-sm text-slate-500">
        <span>Showing {{ $rangeStart }}–{{ $rangeEnd }} of {{ $total }}</span>
        @if($totalPages > 1)
        <div class="flex items-center gap-4">
            @if($currentPage > 1)
                <a href="{{ $pageUrl($currentPage - 1) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">&larr; Previous</a>
            @else
                <span class="text-slate-300">&larr; Previous</span>
            @endif
            <span class="text-slate-400">Page {{ $currentPage }} of {{ $totalPages }}</span>
            @if($currentPage < $totalPages)
                <a href="{{ $pageUrl($currentPage + 1) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Next &rarr;</a>
            @else
                <span class="text-slate-300">Next &rarr;</span>
            @endif
        </div>
        @endif
    </div>
    @endif

</x-admin-layout>
