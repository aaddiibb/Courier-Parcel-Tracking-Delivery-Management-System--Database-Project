<x-admin-layout>
    <x-slot name="header">Rider Performance</x-slot>

    @php
    $barColor = function ($rate) {
        if ($rate === null) return 'bg-slate-300';
        if ($rate >= 80) return 'bg-emerald-500';
        if ($rate >= 60) return 'bg-amber-500';
        return 'bg-red-500';
    };
    $rateTextColor = function ($rate) {
        if ($rate === null) return 'text-slate-500';
        if ($rate >= 80) return 'text-emerald-700';
        if ($rate >= 60) return 'text-amber-700';
        return 'text-red-700';
    };
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Rider Performance</h1>
            <p class="mt-1 text-sm text-slate-500">Leaderboard ranked by delivery success rate</p>
        </div>
        <a href="{{ route('admin.analytics') }}" class="hover:bg-slate-100 text-slate-600 text-sm px-3 py-1.5 rounded-lg transition-colors">&larr; Overview</a>
    </div>

    <div class="space-y-6">

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Leaderboard</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Rank</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Rider</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Vehicle</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Branch</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Attempts</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Deliveries</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-48">Success Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($riders as $i => $r)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 py-3 text-slate-400 font-medium">#{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $r->full_name }}</td>
                        <td class="px-4 py-3 text-slate-500 capitalize">{{ strtolower($r->vehicle_type) }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $r->branch_name }}</td>
                        <td class="px-4 py-3 text-slate-900 text-right">{{ $r->total_attempts }}</td>
                        <td class="px-4 py-3 text-slate-900 text-right">{{ $r->parcels_handled }}</td>
                        <td class="px-4 py-3">
                            @php $rate = $r->success_rate !== null ? (float) $r->success_rate : null; @endphp
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $barColor($rate) }}" style="width: {{ $rate ?? 0 }}%"></div>
                                </div>
                                <span class="text-xs font-semibold {{ $rateTextColor($rate) }} w-16 text-right shrink-0">
                                    {{ $rate !== null ? $rate.'%' : 'No data' }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl border border-red-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-red-200 bg-red-50/50">
                <h3 class="text-sm font-semibold text-red-800 uppercase tracking-wide">Needs Attention</h3>
                <p class="text-xs text-red-700/80 mt-0.5">Riders with 3+ attempts and a success rate under 60%.</p>
            </div>
            @if(empty($needsAttention))
                <div class="py-12 text-center text-slate-400">
                    <p class="text-sm">No riders currently need attention. 🎉</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($needsAttention as $r)
                    <div class="px-5 py-3.5 flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-900">{{ $r->full_name }}</span>
                        <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">
                            {{ $r->rate }}%
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-admin-layout>
