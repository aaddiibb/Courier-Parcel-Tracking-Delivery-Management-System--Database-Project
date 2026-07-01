<x-customer-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Shipments</h2>
            <span class="text-sm text-gray-500">{{ count($parcels) }} shipment{{ count($parcels) !== 1 ? 's' : '' }}</span>
        </div>
    </x-slot>

            @if(empty($parcels))
                {{-- Empty state --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 py-16 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-700">No parcels found</h3>
                    <p class="mt-1 text-sm text-gray-500">You have not sent any parcels yet, or your account is not linked to a customer record.</p>
                    <a href="{{ route('customer.dashboard') }}"
                       class="mt-6 inline-block px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        Back to Dashboard
                    </a>
                </div>

            @else
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tracking Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Receiver</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Destination</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Booked</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Delivered</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($parcels as $p)
                                    @php
                                        $colors = [
                                            'BOOKED'           => 'bg-blue-100 text-blue-800',
                                            'IN_TRANSIT'       => 'bg-yellow-100 text-yellow-800',
                                            'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
                                            'DELIVERED'        => 'bg-green-100 text-green-800',
                                            'RETURNED'         => 'bg-red-100 text-red-800',
                                        ];
                                        $cls = $colors[$p->current_status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-mono font-semibold text-indigo-700">{{ $p->tracking_code }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700">{{ $p->receiver_name }}</td>
                                        <td class="px-6 py-4 text-gray-700">
                                            {{ $p->dest_branch }}<br>
                                            <span class="text-xs text-gray-400">{{ $p->destination_city }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $cls }}">
                                                {{ str_replace('_', ' ', $p->current_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $p->booked_at }}</td>
                                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $p->delivered_at ?? '—' }}</td>
                                        <td class="px-6 py-4 text-right">
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
            @endif
</x-customer-layout>
