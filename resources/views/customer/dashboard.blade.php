<x-customer-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Welcome back, {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="space-y-6">

            @if(!$customer)
                {{-- Account not yet linked to Oracle customer record --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                    <strong>Note:</strong> Your account is not yet linked to any customer record. Please contact support so we can link your account.
                </div>
            @endif

            {{-- Stats Row --}}
            @php
                $total     = array_sum($stats);
                $delivered = $stats['DELIVERED'] ?? 0;
                $inTransit = ($stats['IN_TRANSIT'] ?? 0) + ($stats['OUT_FOR_DELIVERY'] ?? 0);
                $booked    = $stats['BOOKED'] ?? 0;
                $returned  = $stats['RETURNED'] ?? 0;
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Parcels</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">In Transit</p>
                    <p class="text-3xl font-bold text-yellow-500 mt-1">{{ $inTransit }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Delivered</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $delivered }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Booked / Returned</p>
                    <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $booked }} <span class="text-sm font-normal text-gray-400">/ {{ $returned }}</span></p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Spend</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">৳{{ number_format($totalSpend, 2) }}</p>
                </div>
            </div>

            {{-- Recent Parcels --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-800">Recent Parcels</h3>
                    <a href="{{ route('customer.parcels.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        View all &rarr;
                    </a>
                </div>

                @if(empty($recentParcels))
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="mt-3 text-sm text-gray-500">No parcels found yet.</p>
                        <p class="text-xs text-gray-400 mt-1">Your shipments will appear here once booked.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                                <tr>
                                    <th class="px-6 py-3 text-left">Tracking Code</th>
                                    <th class="px-6 py-3 text-left">Destination</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                    <th class="px-6 py-3 text-left">Booked</th>
                                    <th class="px-6 py-3 text-left"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentParcels as $p)
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
                                        <td class="px-6 py-4 font-mono font-semibold text-indigo-700">{{ $p->tracking_code }}</td>
                                        <td class="px-6 py-4 text-gray-700">{{ $p->destination_city }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $cls }}">
                                                {{ str_replace('_', ' ', $p->current_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500">{{ $p->booked_at }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('customer.parcels.show', $p->tracking_code) }}"
                                               class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                                Track &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Quick Track --}}
            <div class="bg-indigo-700 rounded-xl p-6 text-white">
                <h3 class="text-base font-semibold mb-1">Track a Parcel by Code</h3>
                <p class="text-indigo-200 text-sm mb-4">Enter a tracking code from one of your own shipments.</p>
                <form method="POST" action="{{ route('customer.track') }}" class="flex gap-2">
                    @csrf
                    <input type="text" name="tracking_code" placeholder="e.g. CDB202600001"
                           class="flex-1 rounded-lg px-4 py-2 text-gray-900 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <button type="submit"
                            class="bg-white text-indigo-700 font-semibold px-5 py-2 rounded-lg text-sm hover:bg-indigo-50 transition-colors">
                        Track
                    </button>
                </form>
            </div>

    </div>
</x-customer-layout>
