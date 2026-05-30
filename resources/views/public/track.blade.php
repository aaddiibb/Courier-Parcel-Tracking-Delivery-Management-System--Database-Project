<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Parcel — Courier DB</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-white border-b border-gray-200 py-4 px-6 flex justify-between items-center">
        <h1 class="text-xl font-bold text-indigo-700">Courier DB</h1>
        <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:underline">Admin Login</a>
    </header>

    <div class="max-w-2xl mx-auto py-16 px-4">

        <h2 class="text-3xl font-bold text-gray-900 mb-2 text-center">Track Your Parcel</h2>
        <p class="text-gray-500 text-center mb-8">Enter your tracking code to see real-time delivery status.</p>

        <form method="POST" action="{{ route('track') }}" class="flex gap-2 mb-10">
            @csrf
            <input type="text" name="tracking_code"
                   value="{{ old('tracking_code', isset($parcel) ? $parcel->tracking_code : '') }}"
                   placeholder="e.g. CDB202501001"
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('tracking_code') border-red-400 @enderror">
            <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-indigo-700">
                Track
            </button>
        </form>
        @error('tracking_code')
            <p class="text-red-500 text-sm -mt-8 mb-6">{{ $message }}</p>
        @enderror

        @if(isset($parcel) && $parcel === null && request()->isMethod('post'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                No parcel found with that tracking code.
            </div>
        @endif

        @if(isset($parcel) && $parcel)
            {{-- Parcel Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium mb-1">Tracking Code</p>
                        <p class="text-2xl font-mono font-bold text-indigo-700">{{ $parcel->tracking_code }}</p>
                    </div>
                    <div class="text-right">
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
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $cls }}">
                            {{ $parcel->current_status }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium">From</p>
                        <p class="text-gray-900">{{ $parcel->origin_branch }} — {{ $parcel->origin_city }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium">To</p>
                        <p class="text-gray-900">{{ $parcel->dest_branch }} — {{ $parcel->dest_city }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium">Receiver</p>
                        <p class="text-gray-900">{{ $parcel->receiver_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium">Weight</p>
                        <p class="text-gray-900">{{ $parcel->weight_kg }} kg</p>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            @if(count($history) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Shipment Timeline</h3>
                <div class="space-y-0">
                    @foreach($history as $i => $h)
                        @php $cls = $colors[$h->status] ?? 'bg-gray-100 text-gray-800'; @endphp
                        <div class="flex gap-4 {{ !$loop->last ? 'pb-5' : '' }} relative">
                            {{-- Vertical line --}}
                            @if(!$loop->last)
                                <div class="absolute left-[11px] top-6 bottom-0 w-0.5 bg-gray-200"></div>
                            @endif
                            {{-- Dot --}}
                            <div class="shrink-0 mt-0.5">
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
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $cls }}">{{ $h->status }}</span>
                                @if($h->remarks)
                                    <span class="ml-2 text-gray-700">{{ $h->remarks }}</span>
                                @endif
                                <p class="text-gray-400 text-xs mt-0.5">{{ $h->changed_at }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif

    </div>
</body>
</html>
