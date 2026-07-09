<x-customer-layout>
    <x-slot name="header">Track Parcel</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <nav class="text-xs text-slate-400 mb-1">
                <a href="{{ route('customer.parcels.index') }}" class="hover:text-slate-600">My Parcels</a>
                <span class="mx-1">/</span>
                <span class="text-slate-500">{{ $parcel->tracking_code }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 font-mono">{{ $parcel->tracking_code }}</h1>
        </div>
        <a href="{{ route('customer.parcels.index') }}" class="hover:bg-slate-100 text-slate-600 text-sm px-3 py-1.5 rounded-lg transition-colors">&larr; My Parcels</a>
    </div>

    <div class="max-w-3xl space-y-6">

        @php
            $steps       = ['BOOKED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED'];
            $stepLabels  = ['Booked', 'In Transit', 'Out for Delivery', 'Delivered'];
            $isReturned  = $parcel->current_status === 'RETURNED';
            $currentStep = array_search($parcel->current_status, $steps);
            if ($currentStep === false) $currentStep = -1;
        @endphp

        {{-- Status Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide mb-1">Tracking Code</p>
                    <p class="text-2xl font-mono font-bold text-indigo-600">{{ $parcel->tracking_code }}</p>
                </div>
                <x-status-badge :status="$parcel->current_status" size="lg" />
            </div>

            {{-- Progress Stepper --}}
            @if($isReturned)
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
                    <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    This parcel was returned and could not be delivered.
                </div>
            @else
                <div class="relative">
                    <div class="flex items-center">
                        @foreach($steps as $i => $step)
                            @php $done = $i <= $currentStep; @endphp

                            <div class="flex flex-col items-center flex-1 {{ $i > 0 ? 'relative' : '' }}">
                                @if($i > 0)
                                    <div class="absolute left-0 right-1/2 top-4 h-0.5 {{ $i <= $currentStep ? 'bg-indigo-600' : 'bg-slate-200' }} -translate-y-1/2"></div>
                                @endif
                                @if($i < count($steps) - 1)
                                    <div class="absolute left-1/2 right-0 top-4 h-0.5 {{ $i < $currentStep ? 'bg-indigo-600' : 'bg-slate-200' }} -translate-y-1/2"></div>
                                @endif

                                <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center border-2
                                    {{ $done ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-300' }}">
                                    @if($done)
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs text-center font-medium {{ $done ? 'text-indigo-700' : 'text-slate-400' }}">
                                    {{ $stepLabels[$i] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Parcel Info --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4">Parcel Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Sender</p>
                    <p class="text-slate-900 font-medium mt-0.5">{{ $parcel->sender_name }}</p>
                    <p class="text-slate-500">{{ $parcel->sender_phone }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Receiver</p>
                    <p class="text-slate-900 font-medium mt-0.5">{{ $parcel->receiver_name }}</p>
                    <p class="text-slate-500">{{ $parcel->receiver_phone }}</p>
                    @if($parcel->receiver_address)
                        <p class="text-slate-500 text-xs mt-0.5">{{ $parcel->receiver_address }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Origin</p>
                    <p class="text-slate-900 font-medium mt-0.5">{{ $parcel->origin_branch }}</p>
                    <p class="text-slate-500">{{ $parcel->origin_city }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Destination</p>
                    <p class="text-slate-900 font-medium mt-0.5">{{ $parcel->dest_branch }}</p>
                    <p class="text-slate-500">{{ $parcel->dest_city }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Weight</p>
                    <p class="text-slate-900 font-medium mt-0.5">{{ $parcel->weight_kg }} kg</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Booked At</p>
                    <p class="text-slate-900 font-medium mt-0.5">{{ $parcel->booked_at }}</p>
                </div>
                @if($parcel->delivered_at)
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-wide">Delivered At</p>
                    <p class="text-emerald-700 font-medium mt-0.5">{{ $parcel->delivered_at }}</p>
                </div>
                @endif
            </div>
        </div>

        @include('partials.parcel-timeline', ['history' => $history])

    </div>
</x-customer-layout>
