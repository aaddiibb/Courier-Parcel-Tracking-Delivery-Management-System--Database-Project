<x-admin-layout>
    <x-slot name="header">Branch Performance</x-slot>

    @php
    $underperformers = collect($branches)->whereIn('branch_name', $underperformerNames)->values();
    $cities = collect($branches)->pluck('city')->unique()->count();
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Branch Performance</h1>
            <p class="mt-1 text-sm text-slate-500">{{ count($branches) }} branches active across {{ $cities }} cities</p>
        </div>
        <a href="{{ route('admin.analytics') }}" class="hover:bg-slate-100 text-slate-600 text-sm px-3 py-1.5 rounded-lg transition-colors">&larr; Overview</a>
    </div>

    <div class="space-y-6">

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Branch</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">City</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Parcels</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Delivered</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Returned</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Success %</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($branches as $b)
                    @php $isUnder = in_array($b->branch_name, $underperformerNames, true); @endphp
                    <tr class="{{ $isUnder ? 'bg-amber-50/60' : 'hover:bg-slate-50/80' }} transition-colors">
                        <td class="px-4 py-3 font-medium text-slate-900">
                            {{ $b->branch_name }}
                            @if($isUnder)
                                <span class="ml-2 inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Underperforming</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $b->city }}</td>
                        <td class="px-4 py-3 text-slate-900 text-right">{{ $b->total_parcels }}</td>
                        <td class="px-4 py-3 text-emerald-700 text-right">{{ $b->delivered }}</td>
                        <td class="px-4 py-3 text-red-700 text-right">{{ $b->returned }}</td>
                        <td class="px-4 py-3 text-slate-900 text-right">{{ $b->success_pct }}%</td>
                        <td class="px-4 py-3 text-slate-900 text-right">৳{{ number_format($b->revenue, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl border border-amber-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-amber-200 bg-amber-50/50">
                <h3 class="text-sm font-semibold text-amber-800 uppercase tracking-wide">Underperforming Branches</h3>
                <p class="text-xs text-amber-700/80 mt-0.5">Below the average parcel volume across all branches.</p>
            </div>
            @if($underperformers->isEmpty())
                <div class="py-12 text-center text-slate-400">
                    <p class="text-sm">No underperforming branches right now.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Branch</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">City</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Parcels</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($underperformers as $b)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $b->branch_name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $b->city }}</td>
                            <td class="px-4 py-3 text-slate-900 text-right">{{ $b->total_parcels }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</x-admin-layout>
