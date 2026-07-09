<x-admin-layout>
    <x-slot name="header">Parcel {{ $parcel->tracking_code }}</x-slot>

    <div class="max-w-4xl mx-auto">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-400 mb-1">
                    <a href="{{ route('admin.parcels.index') }}" class="hover:text-slate-600">Parcels</a>
                    <span>/</span>
                    <span class="text-slate-600">{{ $parcel->tracking_code }}</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 font-mono">{{ $parcel->tracking_code }}</h1>
            </div>
            <x-status-badge :status="$parcel->current_status" size="lg" />
        </div>

        <div class="space-y-6">

            {{-- Parcel Details --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4">Parcel Details</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-slate-500">Sender</span><p class="text-slate-900 mt-0.5">{{ $parcel->sender_name }} — {{ $parcel->sender_phone }}</p></div>
                    <div><span class="text-slate-500">Receiver</span><p class="text-slate-900 mt-0.5">{{ $parcel->receiver_name }} — {{ $parcel->receiver_phone }}</p></div>
                    <div><span class="text-slate-500">Origin</span><p class="text-slate-900 mt-0.5">{{ $parcel->origin_branch }} ({{ $parcel->origin_city }})</p></div>
                    <div><span class="text-slate-500">Destination</span><p class="text-slate-900 mt-0.5">{{ $parcel->dest_branch }} ({{ $parcel->dest_city }})</p></div>
                    <div><span class="text-slate-500">Weight</span><p class="text-slate-900 mt-0.5">{{ $parcel->weight_kg }} kg</p></div>
                    <div><span class="text-slate-500">Assigned Rider</span><p class="text-slate-900 mt-0.5">{{ $parcel->rider_name ?? '—' }}</p></div>
                    <div><span class="text-slate-500">Booked At</span><p class="text-slate-900 mt-0.5">{{ $parcel->booked_at }}</p></div>
                    <div><span class="text-slate-500">Delivered At</span><p class="text-slate-900 mt-0.5">{{ $parcel->delivered_at ?? '—' }}</p></div>
                </div>
                @if($fee)
                    <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-4 gap-4 text-sm">
                        <div><span class="text-slate-500">Base Fee</span><p class="text-slate-900 mt-0.5">৳{{ $fee->base_amount }}</p></div>
                        <div><span class="text-slate-500">Weight Charge</span><p class="text-slate-900 mt-0.5">৳{{ $fee->weight_charge }}</p></div>
                        <div>
                            <span class="text-slate-500">Total</span>
                            <p class="font-bold text-slate-900 mt-0.5">৳{{ $fee->total_amount }}</p>
                        </div>
                        <div>
                            <span class="text-slate-500">Paid</span>
                            <p class="mt-0.5">
                                @if($fee->paid_flag === 'Y')
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Yes</span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">No</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Update Status --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4">Update Status</h3>
                <form method="POST" action="{{ route('admin.parcels.updateStatus', $parcel->parcel_id) }}" class="flex gap-3 items-end flex-wrap">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">New Status</label>
                        <select name="new_status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            @foreach($statuses as $s)
                                <option value="{{ $s }}" {{ $parcel->current_status === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Remarks (optional)</label>
                        <input type="text" name="remarks" maxlength="200"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                               placeholder="e.g. Out with rider John">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        Update
                    </button>
                </form>
            </div>

            {{-- Status History --}}
            @include('partials.parcel-timeline')

            {{-- Delivery Attempts --}}
            @if(count($attempts) > 0)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Delivery Attempts</h3>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Attempted At</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Rider</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Outcome</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($attempts as $a)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 text-slate-500">{{ $a->attempted_at }}</td>
                            <td class="px-4 py-3 text-slate-900">{{ $a->rider_name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($a->success_flag === 'Y')
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Success</span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Failed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $a->failure_reason ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>
</x-admin-layout>
