<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports & Analytics</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            {{-- ================================================================
                 SECTION 1: Operations Overview
                 ================================================================ --}}
            <section>
                <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Operations Overview</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Status Breakdown --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Parcels by Status</h4>
                        </div>
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Count</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @php
                                    $statusColors = [
                                        'BOOKED'           => 'bg-blue-100 text-blue-800',
                                        'IN_TRANSIT'       => 'bg-yellow-100 text-yellow-800',
                                        'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
                                        'DELIVERED'        => 'bg-green-100 text-green-800',
                                        'RETURNED'         => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                @foreach($statusBreakdown as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 text-sm">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$row->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $row->status }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-right font-medium text-gray-800">{{ $row->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Revenue Summary --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Revenue Summary</h4>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <div class="flex justify-between items-center px-5 py-4">
                                <span class="text-sm text-gray-600">Collected (Paid)</span>
                                <span class="text-sm font-semibold text-green-700">৳ {{ number_format($revenue->paid_total, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center px-5 py-4">
                                <span class="text-sm text-gray-600">Outstanding (Unpaid)</span>
                                <span class="text-sm font-semibold text-red-600">৳ {{ number_format($revenue->unpaid_total, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center px-5 py-4 bg-gray-50">
                                <span class="text-sm font-semibold text-gray-700">Total Billed</span>
                                <span class="text-sm font-bold text-gray-900">৳ {{ number_format($revenue->grand_total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            {{-- ================================================================
                 SECTION 2: Branch Performance
                 ================================================================ --}}
            <section>
                <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Branch Performance</h3>
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">City</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Parcels Sent</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue (৳)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg Weight (kg)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($branchPerformance as $b)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $b->branch_name }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $b->city }}</td>
                                <td class="px-6 py-3 text-sm text-right text-gray-800">{{ $b->parcel_count }}</td>
                                <td class="px-6 py-3 text-sm text-right font-medium text-gray-900">{{ number_format($b->revenue, 2) }}</td>
                                <td class="px-6 py-3 text-sm text-right text-gray-600">{{ $b->avg_weight_kg }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(count($highVolumeBranches) > 0)
                <div class="mt-3 bg-indigo-50 border border-indigo-100 rounded-lg px-5 py-3">
                    <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wide mb-1">High-Volume Branches (&gt; 4 parcels)</p>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($highVolumeBranches as $b)
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-1 rounded-full">
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
                <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Rider Performance</h3>
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rider</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Deliveries</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Successful</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Success Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($riderLeaderboard as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $r->full_name }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $r->branch_name ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-right text-gray-800">{{ $r->total_attempts }}</td>
                                <td class="px-6 py-3 text-sm text-right text-gray-800">{{ $r->successes }}</td>
                                <td class="px-6 py-3 text-sm text-right">
                                    @if($r->success_rate_pct !== null)
                                        @php $rate = (float) $r->success_rate_pct; @endphp
                                        <span class="font-semibold {{ $rate >= 80 ? 'text-green-700' : ($rate >= 50 ? 'text-yellow-700' : 'text-red-600') }}">
                                            {{ $rate }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(count($underperformingRiders) > 0)
                <div class="mt-3 bg-red-50 border border-red-100 rounded-lg px-5 py-3">
                    <p class="text-xs font-semibold text-red-700 uppercase tracking-wide mb-2">Riders Needing Attention (success rate &lt; 60%, min 3 attempts)</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($underperformingRiders as $r)
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full">
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
                <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Customer Activity</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Frequent Customers --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Repeat Customers (&gt; 2 shipments)</h4>
                        </div>
                        @if(count($frequentCustomers) > 0)
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Shipments</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($frequentCustomers as $c)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $c->full_name }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-500">{{ $c->phone }}</td>
                                    <td class="px-5 py-3 text-sm text-right font-semibold text-indigo-700">{{ $c->parcel_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="px-5 py-4 text-sm text-gray-400">No customers with more than 2 shipments yet.</p>
                        @endif
                    </div>

                    {{-- Customers with active (in-transit) shipments --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Customers with Active Shipments</h4>
                        </div>
                        @if(count($customersWithActiveShipments) > 0)
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($customersWithActiveShipments as $c)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $c->full_name }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-500">{{ $c->phone }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="px-5 py-4 text-sm text-gray-400">No customers currently have in-transit shipments.</p>
                        @endif
                    </div>

                </div>
            </section>

            {{-- ================================================================
                 SECTION 5: Alerts
                 ================================================================ --}}
            <section>
                <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Alerts</h3>
                <div class="space-y-6">

                    {{-- Heavy Parcels --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span>
                            <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Above-Average Weight Parcels</h4>
                        </div>
                        @if(count($heavyParcels) > 0)
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sender</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Weight (kg)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($heavyParcels as $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-mono text-indigo-700">{{ $p->tracking_code }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $p->sender }}</td>
                                    <td class="px-6 py-3 text-sm text-right font-semibold text-orange-700">{{ $p->weight_kg }}</td>
                                    <td class="px-6 py-3 text-sm">
                                        @php $cls = $statusColors[$p->current_status] ?? 'bg-gray-100 text-gray-700'; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $cls }}">{{ $p->current_status }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="px-5 py-4 text-sm text-gray-400">All parcels are at or below average weight.</p>
                        @endif
                    </div>

                    {{-- Inactive Branches --}}
                    @if(count($inactiveBranches) > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-5 py-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                            <h4 class="text-sm font-semibold text-yellow-800 uppercase tracking-wide">Branches with No Outgoing Parcels</h4>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach($inactiveBranches as $b)
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $b->branch_name }} ({{ $b->city }})
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </section>

        </div>
    </div>
</x-app-layout>
