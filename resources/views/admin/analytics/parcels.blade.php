<x-admin-layout>
    <x-slot name="header">Parcel Intelligence</x-slot>

    @php
    $funnelColor = [
        'BOOKED'           => 'bg-slate-400',
        'IN_TRANSIT'       => 'bg-blue-500',
        'OUT_FOR_DELIVERY' => 'bg-amber-500',
        'DELIVERED'        => 'bg-emerald-500',
        'RETURNED'         => 'bg-red-500',
    ];
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Parcel Intelligence</h1>
            <p class="mt-1 text-sm text-slate-500">Status funnel, stuck parcels, and weight-band pricing review</p>
        </div>
        <a href="{{ route('admin.analytics') }}" class="hover:bg-slate-100 text-slate-600 text-sm px-3 py-1.5 rounded-lg transition-colors">&larr; Overview</a>
    </div>

    <div class="space-y-6">

        {{-- ── Status funnel ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-5 py-4 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Volume Funnel</h3>
            </div>
            <div class="p-5 space-y-3">
                @foreach($funnel as $f)
                <div class="flex items-center gap-3">
                    <span class="w-36 shrink-0"><x-status-badge :status="$f->current_status" /></span>
                    <div class="flex-1 bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $funnelColor[$f->current_status] ?? 'bg-slate-400' }}" style="width: {{ $f->pct }}%"></div>
                    </div>
                    <span class="w-10 text-xs font-semibold text-slate-700 text-right shrink-0">{{ $f->cnt }}</span>
                    <span class="w-12 text-xs text-slate-400 text-right shrink-0">{{ $f->pct }}%</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Stuck parcels ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border {{ count($stuckParcels) > 0 ? 'border-red-200' : 'border-slate-200' }} shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b {{ count($stuckParcels) > 0 ? 'border-red-200 bg-red-50/50' : 'border-slate-200' }}">
                <h3 class="text-sm font-semibold {{ count($stuckParcels) > 0 ? 'text-red-800' : 'text-slate-700' }} uppercase tracking-wide">Stuck Parcels</h3>
                <p class="text-xs {{ count($stuckParcels) > 0 ? 'text-red-700/80' : 'text-slate-400' }} mt-0.5">In transit or out for delivery for more than 3 days.</p>
            </div>

            @if(empty($stuckParcels))
                <div class="py-12 text-center text-slate-400">
                    <p class="text-sm">No stuck parcels right now. 🎉</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Code</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Sender</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">From</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">To</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Days in System</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($stuckParcels as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">{{ $p->tracking_code }}</td>
                            <td class="px-4 py-3 text-slate-900">{{ $p->sender }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p->origin }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $p->destination }}</td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$p->current_status" />
                            </td>
                            <td class="px-4 py-3 text-right font-semibold {{ $p->days_in_system > 5 ? 'text-red-600' : 'text-slate-700' }}">{{ $p->days_in_system }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ── Weight distribution ──────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Weight Distribution</h3>
                <p class="text-xs text-slate-400 mt-0.5">For pricing review.</p>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Weight Band</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Parcels</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Avg. Fee</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($weightBands as $w)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $w->weight_band }}</td>
                        <td class="px-4 py-3 text-slate-900 text-right">{{ $w->cnt }}</td>
                        <td class="px-4 py-3 text-slate-900 text-right">৳{{ number_format($w->avg_fee, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-admin-layout>
