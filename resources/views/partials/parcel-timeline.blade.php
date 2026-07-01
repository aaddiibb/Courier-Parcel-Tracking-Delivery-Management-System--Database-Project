@php
    $timelineColors = [
        'BOOKED'           => 'bg-blue-100 text-blue-800',
        'IN_TRANSIT'       => 'bg-yellow-100 text-yellow-800',
        'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
        'DELIVERED'        => 'bg-green-100 text-green-800',
        'RETURNED'         => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-5">Shipment Timeline</h3>

    @if(empty($history))
        <p class="text-sm text-gray-400 italic">No status updates recorded yet.</p>
    @else
        <div class="space-y-0">
            @foreach($history as $h)
                @php $hCls = $timelineColors[$h->status] ?? 'bg-gray-100 text-gray-800'; @endphp
                <div class="flex gap-4 {{ !$loop->last ? 'pb-6' : '' }} relative">
                    {{-- Connector line --}}
                    @if(!$loop->last)
                        <div class="absolute left-[11px] top-6 bottom-0 w-0.5 bg-gray-200"></div>
                    @endif

                    {{-- Dot --}}
                    <div class="shrink-0 mt-0.5 z-10">
                        @if($loop->first)
                            <div class="w-6 h-6 rounded-full bg-indigo-600 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-white"></div>
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full border-2 border-gray-300 bg-white"></div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 text-sm">
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $hCls }}">
                            {{ str_replace('_', ' ', $h->status) }}
                        </span>
                        @if($h->remarks)
                            <span class="ml-2 text-gray-600">{{ $h->remarks }}</span>
                        @endif
                        <p class="text-gray-400 text-xs mt-1">{{ $h->changed_at }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
