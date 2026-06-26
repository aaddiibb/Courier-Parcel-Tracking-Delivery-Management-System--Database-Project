<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Operations Monitor</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            {{-- ================================================================
                 SECTION 1: Active Shipments Monitor  (sp_intransit_monitor)
                 Explicit cursor with OPEN/FETCH/CLOSE, %ROWTYPE, %NOTFOUND,
                 %ROWCOUNT, SYSDATE arithmetic.
                 ================================================================ --}}
            <section>
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">Active Shipments Monitor</h3>
                    @if($transitSummary)
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span><span class="font-semibold text-gray-800">{{ $transitSummary['count'] }}</span> shipment(s)</span>
                        <span><span class="font-semibold text-gray-800">{{ $transitSummary['total_kg'] }} kg</span> total load</span>
                    </div>
                    @endif
                </div>

                @if(count($transitRows) > 0)
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Origin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destination</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Weight (kg)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Days in Transit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($transitRows as $row)
                            @php $days = (int) $row['days']; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-mono text-indigo-700">{{ $row['tracking'] }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900">{{ $row['sender'] }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $row['origin'] }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $row['dest'] }}</td>
                                <td class="px-6 py-3 text-sm text-right text-gray-800">{{ $row['weight'] }}</td>
                                <td class="px-6 py-3 text-sm text-right">
                                    <span class="font-semibold {{ $days >= 3 ? 'text-red-600' : ($days >= 1 ? 'text-yellow-700' : 'text-green-700') }}">
                                        {{ $days }}d
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="bg-white shadow-sm rounded-lg px-6 py-5 text-sm text-gray-400">
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
                <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Weight Compliance Scan</h3>

                @if($weightInfo)
                <div class="bg-gray-50 border border-gray-200 rounded-lg px-5 py-4 text-sm text-gray-500">
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
                <div class="mb-3 bg-green-50 border border-green-200 rounded-lg px-5 py-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                    <span class="text-sm font-semibold text-green-700">All active parcels are within the 50 kg weight limit.</span>
                </div>
                @endif

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compliance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Weight (kg)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Days Active</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($weightRows as $row)
                            <tr class="hover:bg-gray-50 {{ $row['status'] === 'VIOLATION' ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-3 text-sm">
                                    @if($row['status'] === 'VIOLATION')
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">Over Limit</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Compliant</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm font-mono text-indigo-700">{{ $row['tracking'] }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900">{{ $row['sender'] }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $row['pstatus'] }}</td>
                                <td class="px-6 py-3 text-sm text-right font-semibold {{ $row['status'] === 'VIOLATION' ? 'text-red-700' : 'text-gray-800' }}">
                                    {{ $row['weight'] }}
                                </td>
                                <td class="px-6 py-3 text-sm text-right text-gray-600">{{ $row['days'] }}d</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="bg-white shadow-sm rounded-lg px-6 py-5 text-sm text-gray-400">
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
                <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Fee Integrity Audit</h3>

                @if(count($costRows) > 0)
                @php
                    $mismatches = collect($costRows)->where('status', 'MISMATCH');
                    $noFee      = collect($costRows)->where('status', 'NO-FEE');
                @endphp

                @if($mismatches->count() > 0 || $noFee->count() > 0)
                <div class="mb-3 bg-orange-50 border border-orange-200 rounded-lg px-5 py-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-orange-500 inline-block"></span>
                    <span class="text-sm font-semibold text-orange-700">
                        {{ $mismatches->count() }} fee mismatch(es) and {{ $noFee->count() }} missing fee record(s) detected.
                    </span>
                </div>
                @else
                <div class="mb-3 bg-green-50 border border-green-200 rounded-lg px-5 py-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                    <span class="text-sm font-semibold text-green-700">All active parcel fees match the formula: ৳50 + weight × ৳20.</span>
                </div>
                @endif

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Audit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sender</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Weight (kg)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Expected (৳)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stored (৳)</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Paid</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($costRows as $row)
                            <tr class="hover:bg-gray-50 {{ $row['status'] !== 'OK' ? 'bg-orange-50' : '' }}">
                                <td class="px-6 py-3 text-sm">
                                    @if($row['status'] === 'OK')
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">OK</span>
                                    @elseif($row['status'] === 'MISMATCH')
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">Mismatch</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">No Fee</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm font-mono text-indigo-700">{{ $row['tracking'] }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900">{{ $row['sender'] }}</td>
                                <td class="px-6 py-3 text-sm text-right text-gray-600">{{ $row['weight'] }}</td>
                                <td class="px-6 py-3 text-sm text-right text-gray-800">{{ $row['expected'] }}</td>
                                <td class="px-6 py-3 text-sm text-right font-semibold {{ $row['status'] === 'MISMATCH' ? 'text-orange-700' : 'text-gray-800' }}">
                                    {{ $row['stored'] }}
                                </td>
                                <td class="px-6 py-3 text-sm text-center">
                                    @if($row['paid'] === 'Y')
                                        <span class="text-green-600 font-semibold">Yes</span>
                                    @else
                                        <span class="text-gray-400">No</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="bg-white shadow-sm rounded-lg px-6 py-5 text-sm text-gray-400">
                    No active parcels to audit.
                </div>
                @endif
            </section>

        </div>
    </div>
</x-app-layout>
