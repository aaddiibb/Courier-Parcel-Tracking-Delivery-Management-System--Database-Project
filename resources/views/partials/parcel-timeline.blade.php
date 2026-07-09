<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-5">Shipment Timeline</h3>

    @if(empty($history))
        <p class="text-sm text-slate-400 italic">No status updates recorded yet.</p>
    @else
        <div class="flow-root">
            <ul class="-mb-8">
                @foreach($history as $event)
                <li>
                    <div class="relative pb-8">
                        @if(!$loop->last)
                            <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-slate-200"></span>
                        @endif
                        <div class="relative flex items-start gap-3">
                            {{-- Dot --}}
                            <div class="w-8 h-8 rounded-full flex items-center justify-center ring-4 ring-white shrink-0
                                {{ $event->status === 'DELIVERED' ? 'bg-emerald-500' : ($event->status === 'RETURNED' ? 'bg-red-500' : 'bg-indigo-500') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </div>
                            {{-- Content --}}
                            <div class="flex-1 min-w-0 pt-1.5">
                                <div class="flex items-center justify-between">
                                    <x-status-badge :status="$event->status" />
                                    <span class="text-xs text-slate-400">{{ $event->changed_at }}</span>
                                </div>
                                @if($event->remarks)
                                    <p class="mt-1 text-xs text-slate-500">{{ $event->remarks }}</p>
                                @endif
                                @if($event->changed_by)
                                    <p class="mt-0.5 text-xs text-slate-400">by {{ $event->changed_by }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
