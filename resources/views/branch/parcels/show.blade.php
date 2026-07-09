<x-branch-layout :branch-name="$branchName">
    <x-slot name="header">Parcel Detail</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <nav class="text-xs text-slate-400 mb-1">
                <a href="{{ route('branch.parcels.index') }}" class="hover:text-slate-600">Parcels</a>
                <span class="mx-1">/</span>
                <span class="text-slate-500">{{ $parcel->tracking_code }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 font-mono">{{ $parcel->tracking_code }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <x-status-badge :status="$parcel->current_status" size="lg" />
            <a href="{{ route('branch.parcels.index') }}" class="hover:bg-slate-100 text-slate-600 text-sm px-3 py-1.5 rounded-lg transition-colors">&larr; Back</a>
        </div>
    </div>

    <div class="max-w-4xl space-y-6">

        {{-- Parcel Details --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4">Parcel Details</h3>
            <div class="grid grid-cols-2 gap-5 text-sm">
                <div><p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Tracking Code</p><p class="font-mono font-semibold text-indigo-600">{{ $parcel->tracking_code }}</p></div>
                <div><p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Status</p><x-status-badge :status="$parcel->current_status" /></div>
                <div><p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Sender</p><p class="text-slate-900">{{ $parcel->sender_name }} — {{ $parcel->sender_phone }}</p></div>
                <div><p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Receiver</p><p class="text-slate-900">{{ $parcel->receiver_name }} — {{ $parcel->receiver_phone }}</p></div>
                <div><p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Origin</p><p class="text-slate-900">{{ $parcel->origin_branch }} ({{ $parcel->origin_city }})</p></div>
                <div><p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Destination</p><p class="text-slate-900">{{ $parcel->dest_branch }} ({{ $parcel->dest_city }})</p></div>
                <div><p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Weight</p><p class="text-slate-900">{{ $parcel->weight_kg }} kg</p></div>
                <div><p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Assigned Rider</p><p class="text-slate-900">{{ $parcel->rider_name ?? '—' }}</p></div>
                <div><p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Booked At</p><p class="text-slate-900">{{ $parcel->booked_at }}</p></div>
                <div><p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Delivered At</p><p class="text-slate-900">{{ $parcel->delivered_at ?? '—' }}</p></div>
            </div>
        </div>

        {{-- Update Status --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4">Update Status</h3>
            <form method="POST" action="{{ route('branch.parcels.updateStatus', $parcel->parcel_id) }}" class="flex gap-3 items-end flex-wrap">
                @csrf
                <div>
                    <x-input-label value="New Status" />
                    <select name="new_status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ $parcel->current_status === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <x-input-label value="Remarks (optional)" />
                    <x-text-input type="text" name="remarks" maxlength="200" class="w-full" placeholder="e.g. Out with rider John" />
                </div>
                <x-primary-button type="submit">Update</x-primary-button>
            </form>
        </div>

        {{-- Status History --}}
        @include('partials.parcel-timeline')

        {{-- Delivery Attempts --}}
        @if(count($attempts) > 0)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200">
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
</x-branch-layout>
