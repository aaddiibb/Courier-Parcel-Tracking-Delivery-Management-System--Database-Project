<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Parcel: <span class="font-mono text-indigo-700">{{ $parcel->tracking_code }}</span>
            </h2>
            <a href="{{ route('admin.parcels.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Parcels</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Parcel Details --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Parcel Details</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium text-gray-500">Tracking Code</span><p class="font-mono font-bold text-indigo-700">{{ $parcel->tracking_code }}</p></div>
                    <div>
                        <span class="font-medium text-gray-500">Status</span>
                        @php
                            $colors = [
                                'BOOKED'           => 'bg-blue-100 text-blue-800',
                                'IN_TRANSIT'       => 'bg-yellow-100 text-yellow-800',
                                'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
                                'DELIVERED'        => 'bg-green-100 text-green-800',
                                'RETURNED'         => 'bg-red-100 text-red-800',
                            ];
                            $cls = $colors[$parcel->current_status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <p><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $cls }}">{{ $parcel->current_status }}</span></p>
                    </div>
                    <div><span class="font-medium text-gray-500">Sender</span><p>{{ $parcel->sender_name }} — {{ $parcel->sender_phone }}</p></div>
                    <div><span class="font-medium text-gray-500">Receiver</span><p>{{ $parcel->receiver_name }} — {{ $parcel->receiver_phone }}</p></div>
                    <div><span class="font-medium text-gray-500">Origin</span><p>{{ $parcel->origin_branch }} ({{ $parcel->origin_city }})</p></div>
                    <div><span class="font-medium text-gray-500">Destination</span><p>{{ $parcel->dest_branch }} ({{ $parcel->dest_city }})</p></div>
                    <div><span class="font-medium text-gray-500">Weight</span><p>{{ $parcel->weight_kg }} kg</p></div>
                    <div><span class="font-medium text-gray-500">Assigned Rider</span><p>{{ $parcel->rider_name ?? '—' }}</p></div>
                    <div><span class="font-medium text-gray-500">Booked At</span><p>{{ $parcel->booked_at }}</p></div>
                    <div><span class="font-medium text-gray-500">Delivered At</span><p>{{ $parcel->delivered_at ?? '—' }}</p></div>
                </div>
                @if($fee)
                    <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-3 gap-4 text-sm">
                        <div><span class="font-medium text-gray-500">Base Fee</span><p>৳{{ $fee->base_amount }}</p></div>
                        <div><span class="font-medium text-gray-500">Weight Charge</span><p>৳{{ $fee->weight_charge }}</p></div>
                        <div>
                            <span class="font-medium text-gray-500">Total</span>
                            <p class="font-bold text-gray-900">৳{{ $fee->total_amount }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Paid</span>
                            <p>
                                @if($fee->paid_flag === 'Y')
                                    <span class="px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">YES</span>
                                @else
                                    <span class="px-2 text-xs font-semibold rounded-full bg-red-100 text-red-800">NO</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Update Status --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h3>
                <form method="POST" action="{{ route('admin.parcels.updateStatus', $parcel->parcel_id) }}" class="flex gap-3 items-end flex-wrap">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Status</label>
                        <select name="new_status" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($statuses as $s)
                                <option value="{{ $s }}" {{ $parcel->current_status === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (optional)</label>
                        <input type="text" name="remarks" maxlength="200"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="e.g. Out with rider John">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                        Update
                    </button>
                </form>
            </div>

            {{-- Status History --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Status History</h3>
                @if(empty($history))
                    <p class="text-gray-500 text-sm">No history yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($history as $h)
                            <div class="flex gap-4 items-start text-sm">
                                <div class="w-32 text-gray-400 shrink-0">{{ $h->changed_at }}</div>
                                <div>
                                    @php $cls = $colors[$h->status] ?? 'bg-gray-100 text-gray-800'; @endphp
                                    <span class="px-2 text-xs font-semibold rounded-full {{ $cls }}">{{ $h->status }}</span>
                                    @if($h->remarks)
                                        <span class="ml-2 text-gray-600">{{ $h->remarks }}</span>
                                    @endif
                                    <span class="ml-2 text-gray-400">by {{ $h->changed_by }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Delivery Attempts --}}
            @if(count($attempts) > 0)
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Delivery Attempts</h3>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Attempted At</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rider</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Outcome</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($attempts as $a)
                        <tr>
                            <td class="px-4 py-2 text-gray-500">{{ $a->attempted_at }}</td>
                            <td class="px-4 py-2 text-gray-900">{{ $a->rider_name ?? '—' }}</td>
                            <td class="px-4 py-2">
                                @if($a->success_flag === 'Y')
                                    <span class="px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">Success</span>
                                @else
                                    <span class="px-2 text-xs font-semibold rounded-full bg-red-100 text-red-800">Failed</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-500">{{ $a->failure_reason ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
