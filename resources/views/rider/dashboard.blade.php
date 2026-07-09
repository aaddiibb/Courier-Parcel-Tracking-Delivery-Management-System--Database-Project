<x-rider-layout>
    <x-slot name="header">My Deliveries</x-slot>

    @if(!$riderLinked)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg px-4 py-3">
            Your account isn't linked to a rider profile yet. Contact an admin to get set up.
        </div>
    @else

    {{-- ── Today's stats ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-emerald-600">{{ $todaysCompleted }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Delivered Today</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $todaysFailed }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Failed Today</div>
        </div>
    </div>

    {{-- ── Active / Completed tabs ─────────────────────────────────── --}}
    <div class="flex gap-2 mb-4">
        <a href="{{ route('rider.dashboard', ['view' => 'active']) }}"
           class="flex-1 text-center py-2.5 rounded-xl text-sm font-semibold transition-colors
                  {{ $mode === 'active' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-500 border border-slate-200' }}">
            Active
        </a>
        <a href="{{ route('rider.dashboard', ['view' => 'completed']) }}"
           class="flex-1 text-center py-2.5 rounded-xl text-sm font-semibold transition-colors
                  {{ $mode === 'completed' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-500 border border-slate-200' }}">
            Completed
        </a>
    </div>

    @if($mode === 'active')
        <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-3">
            Active Jobs ({{ count($activeJobs) }})
        </h3>

        @if(empty($activeJobs))
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-4 py-10 text-center text-sm text-slate-400">
                No active deliveries right now. 🎉
            </div>
        @else
            <div class="space-y-4">
                @foreach($activeJobs as $job)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono text-sm font-bold text-slate-900">{{ $job->tracking_code }}</span>
                        <x-status-badge :status="$job->current_status" />
                    </div>
                    <div class="text-base font-semibold text-slate-800">{{ $job->receiver_name }}</div>
                    <div class="text-sm text-slate-500">{{ $job->receiver_phone }}</div>
                    <div class="text-sm text-slate-500 mt-1">{{ $job->receiver_address }}</div>
                    <div class="text-xs text-slate-400 mt-2">{{ $job->weight_kg }} kg</div>

                    <a href="{{ route('rider.delivery.log', $job->parcel_id) }}"
                       class="mt-4 block w-full text-center bg-indigo-600 text-white font-semibold py-3.5 rounded-xl text-base active:bg-indigo-700 transition-colors">
                        Log Delivery Attempt
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    @else
        <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-3">
            Completed ({{ count($completedJobs) }})
        </h3>

        @if(empty($completedJobs))
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-4 py-10 text-center text-sm text-slate-400">
                No completed deliveries yet.
            </div>
        @else
            <div class="space-y-3">
                @foreach($completedJobs as $job)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-sm font-bold text-slate-900">{{ $job->tracking_code }}</span>
                        <x-status-badge :status="$job->current_status" />
                    </div>
                    <div class="text-sm text-slate-600 mt-1">{{ $job->receiver_name }}</div>
                    <div class="text-xs text-slate-400 mt-1">{{ $job->outcome_at }}</div>
                </div>
                @endforeach
            </div>
        @endif
    @endif

    @endif
</x-rider-layout>
