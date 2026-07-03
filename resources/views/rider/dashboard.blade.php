<x-rider-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-white leading-tight">My Deliveries</h2>
    </x-slot>

    @if(!$riderLinked)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded-lg px-4 py-3">
            Your account isn't linked to a rider profile yet. Contact an admin to get set up.
        </div>
    @else

    @php
    $statusColors = [
        'IN_TRANSIT'       => 'bg-yellow-100 text-yellow-800',
        'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
    ];
    @endphp

    {{-- ── Today's stats ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-3 mb-5">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $todaysCompleted }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Delivered Today</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $todaysFailed }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Failed Today</div>
        </div>
    </div>

    <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-3">
        Active Jobs ({{ count($activeJobs) }})
    </h3>

    @if(empty($activeJobs))
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-4 py-10 text-center text-sm text-slate-400">
            No active deliveries right now. 🎉
        </div>
    @else
        <div class="space-y-4">
            @foreach($activeJobs as $job)
            @php $cls = $statusColors[$job->current_status] ?? 'bg-gray-100 text-gray-800'; @endphp
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-mono text-sm font-bold text-slate-900">{{ $job->tracking_code }}</span>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $cls }}">
                        {{ str_replace('_', ' ', $job->current_status) }}
                    </span>
                </div>
                <div class="text-base font-semibold text-slate-800">{{ $job->receiver_name }}</div>
                <div class="text-sm text-slate-500">{{ $job->receiver_phone }}</div>
                <div class="text-sm text-slate-500 mt-1">{{ $job->receiver_address }}</div>
                <div class="text-xs text-slate-400 mt-2">{{ $job->weight_kg }} kg</div>

                <a href="{{ route('rider.delivery.log', $job->parcel_id) }}"
                   class="mt-4 block w-full text-center bg-slate-900 text-white font-semibold py-3.5 rounded-xl text-base active:bg-slate-700 transition-colors">
                    Log Delivery Attempt
                </a>
            </div>
            @endforeach
        </div>
    @endif

    @endif
</x-rider-layout>
