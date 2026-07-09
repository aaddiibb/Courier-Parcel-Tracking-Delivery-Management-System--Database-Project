<x-admin-layout>
    <x-slot name="header">Reports & Analytics</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Reports & Analytics</h1>
        <p class="mt-1 text-sm text-slate-500">Aggregate and subquery-driven reporting across the system</p>
    </div>

    <div class="space-y-10">

        {{-- ================================================================
             SECTION 1: Operations Overview
             ================================================================ --}}
        <section>
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4 pb-2 border-b border-slate-200">Operations Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Status Breakdown --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h4 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Parcels by Status</h4>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($statusBreakdown as $row)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3">
                                    <x-status-badge :status="$row->status" />
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-slate-900">{{ $row->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Revenue Summary --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h4 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Revenue Summary</h4>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="flex justify-between items-center px-5 py-4">
                            <span class="text-sm text-slate-600">Collected (Paid)</span>
                            <span class="text-sm font-semibold text-emerald-700">৳ {{ number_format($revenue->paid_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center px-5 py-4">
                            <span class="text-sm text-slate-600">Outstanding (Unpaid)</span>
                            <span class="text-sm font-semibold text-red-600">৳ {{ number_format($revenue->unpaid_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center px-5 py-4 bg-slate-50">
                            <span class="text-sm font-semibold text-slate-700">Total Billed</span>
                            <span class="text-sm font-bold text-slate-900">৳ {{ number_format($revenue->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- ================================================================
             SECTION 2: Branch Performance
             ================================================================ --}}
        <section>
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4 pb-2 border-b border-slate-200">Branch Performance</h3>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Branch</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">City</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Parcels Sent</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Revenue (৳)</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Avg Weight (kg)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($branchPerformance as $b)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $b->branch_name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $b->city }}</td>
                            <td class="px-4 py-3 text-right text-slate-900">{{ $b->parcel_count }}</td>
                            <td class="px-4 py-3 text-right font-medium text-slate-900">{{ number_format($b->revenue, 2) }}</td>
                            <td class="px-4 py-3 text-right text-slate-500">{{ $b->avg_weight_kg }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(count($highVolumeBranches) > 0)
            <div class="mt-3 bg-indigo-50 border border-indigo-200 rounded-lg px-5 py-3">
                <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wide mb-1">High-Volume Branches (&gt; 4 parcels)</p>
                <div class="flex flex-wrap gap-2 mt-1">
                    @foreach($highVolumeBranches as $b)
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-medium px-2.5 py-1 rounded-full">
                        {{ $b->branch_name }} &mdash; {{ $b->parcel_count }} parcels
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </section>

        {{-- ================================================================
             SECTION 3: Rider Performance
             ================================================================ --}}
        <section>
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4 pb-2 border-b border-slate-200">Rider Performance</h3>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Rider</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Branch</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Deliveries</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Successful</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Success Rate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($riderLeaderboard as $r)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $r->full_name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $r->branch_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-slate-900">{{ $r->total_attempts }}</td>
                            <td class="px-4 py-3 text-right text-slate-900">{{ $r->successes }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($r->success_rate_pct !== null)
                                    @php $rate = (float) $r->success_rate_pct; @endphp
                                    <span class="font-semibold {{ $rate >= 80 ? 'text-emerald-700' : ($rate >= 50 ? 'text-amber-700' : 'text-red-600') }}">
                                        {{ $rate }}%
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(count($underperformingRiders) > 0)
            <div class="mt-3 bg-red-50 border border-red-200 rounded-lg px-5 py-3">
                <p class="text-xs font-semibold text-red-700 uppercase tracking-wide mb-2">Riders Needing Attention (success rate &lt; 60%, min 3 attempts)</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($underperformingRiders as $r)
                    <span class="bg-red-100 text-red-700 text-xs font-medium px-2.5 py-1 rounded-full">
                        {{ $r->full_name }} &mdash; {{ $r->success_rate_pct }}%
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </section>

        {{-- ================================================================
             SECTION 4: Customer Activity
             ================================================================ --}}
        <section>
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4 pb-2 border-b border-slate-200">Customer Activity</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Frequent Customers --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h4 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Repeat Customers (&gt; 2 shipments)</h4>
                    </div>
                    @if(count($frequentCustomers) > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Customer</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Phone</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Shipments</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($frequentCustomers as $c)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $c->full_name }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $c->phone }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-indigo-600">{{ $c->parcel_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="px-5 py-4 text-sm text-slate-400">No customers with more than 2 shipments yet.</p>
                    @endif
                </div>

                {{-- Customers with active (in-transit) shipments --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h4 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Customers with Active Shipments</h4>
                    </div>
                    @if(count($customersWithActiveShipments) > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Customer</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Phone</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($customersWithActiveShipments as $c)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $c->full_name }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $c->phone }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="px-5 py-4 text-sm text-slate-400">No customers currently have in-transit shipments.</p>
                    @endif
                </div>

            </div>
        </section>

        {{-- ================================================================
             SECTION 5: Alerts
             ================================================================ --}}
        <section>
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4 pb-2 border-b border-slate-200">Alerts</h3>
            <div class="space-y-6">

                {{-- Heavy Parcels --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                        <h4 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Above-Average Weight Parcels</h4>
                    </div>
                    @if(count($heavyParcels) > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tracking Code</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Sender</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Weight (kg)</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($heavyParcels as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-mono text-indigo-600">{{ $p->tracking_code }}</td>
                                <td class="px-4 py-3 text-slate-900">{{ $p->sender }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-amber-700">{{ $p->weight_kg }}</td>
                                <td class="px-4 py-3">
                                    <x-status-badge :status="$p->current_status" />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="px-5 py-4 text-sm text-slate-400">All parcels are at or below average weight.</p>
                    @endif
                </div>

                {{-- Inactive Branches --}}
                @if(count($inactiveBranches) > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-5 py-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                        <h4 class="text-sm font-semibold text-amber-800 uppercase tracking-wide">Branches with No Outgoing Parcels</h4>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($inactiveBranches as $b)
                        <span class="bg-amber-100 text-amber-800 text-xs font-medium px-2.5 py-1 rounded-full">
                            {{ $b->branch_name }} ({{ $b->city }})
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </section>

    </div>
</x-admin-layout>
