<x-customer-layout>
    <x-slot name="header">All Shipments</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">All Shipments</h1>
            <p class="mt-1 text-sm text-slate-500">{{ count($parcels) }} shipment{{ count($parcels) !== 1 ? 's' : '' }}</p>
        </div>
    </div>

    @if(empty($parcels))
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm py-16 text-center">
            <svg class="mx-auto h-14 w-14 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-slate-700">No parcels found</h3>
            <p class="mt-1 text-sm text-slate-500">You have not sent any parcels yet, or your account is not linked to a customer record.</p>
            <a href="{{ route('customer.dashboard') }}"
               class="mt-6 inline-block px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                Back to Dashboard
            </a>
        </div>
    @else
        @php $statusOptions = ['BOOKED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'RETURNED']; @endphp
        <div x-data="{ statusFilter: 'ALL' }">

            {{-- Status filter pills (client-side; all parcels already loaded) --}}
            <div class="flex flex-wrap gap-2 mb-4">
                <button type="button" @click="statusFilter = 'ALL'"
                        :class="statusFilter === 'ALL' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-300 hover:bg-slate-50'"
                        class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors">
                    All
                </button>
                @foreach($statusOptions as $opt)
                    <button type="button" @click="statusFilter = '{{ $opt }}'"
                            :class="statusFilter === '{{ $opt }}' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-300 hover:bg-slate-50'"
                            class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors">
                        {{ str_replace('_', ' ', $opt) }}
                    </button>
                @endforeach
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tracking Code</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Receiver</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Destination</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Booked</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Delivered</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($parcels as $p)
                                <tr x-show="statusFilter === 'ALL' || statusFilter === '{{ $p->current_status }}'" class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="font-mono font-semibold text-indigo-600">{{ $p->tracking_code }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">{{ $p->receiver_name }}</td>
                                    <td class="px-4 py-3 text-slate-700">
                                        {{ $p->dest_branch }}<br>
                                        <span class="text-xs text-slate-400">{{ $p->destination_city }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="$p->current_status" />
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $p->booked_at }}</td>
                                    <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $p->delivered_at ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('customer.parcels.show', $p->tracking_code) }}"
                                           class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                            Track
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-customer-layout>
