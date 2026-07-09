<x-admin-layout>
    <x-slot name="header">Operations Monitor</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Operations Monitor</h1>
        <p class="mt-1 text-sm text-slate-500">PL/SQL cursor-driven monitoring: transit ages, weight compliance, fee integrity</p>
    </div>

    <div class="space-y-10">

        {{-- ================================================================
             SECTION 1: Active Shipments Monitor  (sp_intransit_monitor)
             Explicit cursor with OPEN/FETCH/CLOSE, %ROWTYPE, %NOTFOUND,
             %ROWCOUNT, SYSDATE arithmetic.
             ================================================================ --}}
        <section>
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Active Shipments Monitor</h3>
                @if($transitSummary)
                <div class="flex items-center gap-4 text-sm text-slate-500">
                    <span><span class="font-semibold text-slate-800">{{ $transitSummary['count'] }}</span> shipment(s)</span>
                    <span><span class="font-semibold text-slate-800">{{ $transitSummary['total_kg'] }} kg</span> total load</span>
                </div>
                @endif
            </div>

            @if(count($transitRows) > 0)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tracking Code</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Sender</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Origin</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Destination</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Weight (kg)</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Days in Transit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transitRows as $row)
                        @php $days = (int) $row['days']; @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono text-indigo-600">{{ $row['tracking'] }}</td>
                            <td class="px-4 py-3 text-slate-900">{{ $row['sender'] }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $row['origin'] }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $row['dest'] }}</td>
                            <td class="px-4 py-3 text-right text-slate-900">{{ $row['weight'] }}</td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-semibold {{ $days >= 3 ? 'text-red-600' : ($days >= 1 ? 'text-amber-700' : 'text-emerald-700') }}">
                                    {{ $days }}d
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-6 py-5 text-sm text-slate-400">
                No parcels are currently in transit.
            </div>
            @endif
        </section>

        {{-- ================================================================
             SECTION 2: Weight Compliance Scan  (sp_weight_violation_scan)
             User-defined exception (weight_limit_exceeded > 50 kg),
             NO_DATA_FOUND for empty active-parcel set.
             ================================================================ --}}
        <section>
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4 pb-2 border-b border-slate-200">Weight Compliance Scan</h3>

            @if($weightInfo)
            <div class="bg-slate-50 border border-slate-200 rounded-lg px-5 py-4 text-sm text-slate-500">
                {{ $weightInfo }}
            </div>
            @elseif(count($weightRows) > 0)
            @php $violations = collect($weightRows)->where('status', 'VIOLATION'); @endphp

            @if($violations->count() > 0)
            <div class="mb-3 bg-red-50 border border-red-200 rounded-lg px-5 py-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                <span class="text-sm font-semibold text-red-700">
                    {{ $violations->count() }} parcel(s) exceed the 50 kg weight limit
                </span>
            </div>
            @else
            <div class="mb-3 bg-emerald-50 border border-emerald-200 rounded-lg px-5 py-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                <span class="text-sm font-semibold text-emerald-700">All active parcels are within the 50 kg weight limit.</span>
            </div>
            @endif

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Compliance</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tracking Code</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Sender</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Weight (kg)</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Days Active</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($weightRows as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $row['status'] === 'VIOLATION' ? 'bg-red-50/60' : '' }}">
                            <td class="px-4 py-3">
                                @if($row['status'] === 'VIOLATION')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Over Limit</span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Compliant</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-indigo-600">{{ $row['tracking'] }}</td>
                            <td class="px-4 py-3 text-slate-900">{{ $row['sender'] }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $row['pstatus'] }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $row['status'] === 'VIOLATION' ? 'text-red-700' : 'text-slate-800' }}">
                                {{ $row['weight'] }}
                            </td>
                            <td class="px-4 py-3 text-right text-slate-600">{{ $row['days'] }}d</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-6 py-5 text-sm text-slate-400">
                No active parcels found.
            </div>
            @endif
        </section>

        {{-- ================================================================
             SECTION 3: Fee Integrity Audit  (sp_parcel_cost_audit)
             %TYPE, %ROWTYPE, arithmetic (50 + weight*20), comparison
             operators, NO_DATA_FOUND for missing fee records.
             ================================================================ --}}
        <section>
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4 pb-2 border-b border-slate-200">Fee Integrity Audit</h3>

            @if(count($costRows) > 0)
            @php
                $mismatches = collect($costRows)->where('status', 'MISMATCH');
                $noFee      = collect($costRows)->where('status', 'NO-FEE');
            @endphp

            @if($mismatches->count() > 0 || $noFee->count() > 0)
            <div class="mb-3 bg-amber-50 border border-amber-200 rounded-lg px-5 py-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                <span class="text-sm font-semibold text-amber-700">
                    {{ $mismatches->count() }} fee mismatch(es) and {{ $noFee->count() }} missing fee record(s) detected.
                </span>
            </div>
            @else
            <div class="mb-3 bg-emerald-50 border border-emerald-200 rounded-lg px-5 py-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                <span class="text-sm font-semibold text-emerald-700">All active parcel fees match the formula: ৳50 + weight × ৳20.</span>
            </div>
            @endif

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Audit</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tracking Code</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Sender</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Weight (kg)</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Expected (৳)</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Stored (৳)</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Paid</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($costRows as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $row['status'] !== 'OK' ? 'bg-amber-50/60' : '' }}">
                            <td class="px-4 py-3">
                                @if($row['status'] === 'OK')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">OK</span>
                                @elseif($row['status'] === 'MISMATCH')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Mismatch</span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">No Fee</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-indigo-600">{{ $row['tracking'] }}</td>
                            <td class="px-4 py-3 text-slate-900">{{ $row['sender'] }}</td>
                            <td class="px-4 py-3 text-right text-slate-600">{{ $row['weight'] }}</td>
                            <td class="px-4 py-3 text-right text-slate-800">{{ $row['expected'] }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $row['status'] === 'MISMATCH' ? 'text-amber-700' : 'text-slate-800' }}">
                                {{ $row['stored'] }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($row['paid'] === 'Y')
                                    <span class="text-emerald-600 font-semibold">Yes</span>
                                @else
                                    <span class="text-slate-400">No</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-6 py-5 text-sm text-slate-400">
                No active parcels to audit.
            </div>
            @endif
        </section>

    </div>
</x-admin-layout>
