<x-customer-layout>
    <x-slot name="header">Book a Parcel</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Book a Parcel</h1>
        <p class="mt-1 text-sm text-slate-500">Select a receiver, choose your branches, and confirm the weight</p>
    </div>

    <div class="max-w-2xl bg-white rounded-xl border border-slate-200 shadow-sm">

        @if(empty($receivers))
            <div class="m-6 mb-0 bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                You need at least one saved receiver before booking a parcel.
                <a href="{{ route('customer.receivers.create') }}" class="font-medium underline">Add one now &rarr;</a>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.parcels.store') }}" class="p-6 space-y-5">
            @csrf

            <div>
                <x-input-label value="Receiver" />
                <select name="receiver_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('receiver_id') border-red-400 @enderror">
                    <option value="">Select receiver</option>
                    @foreach($receivers as $r)
                        <option value="{{ $r->receiver_id }}" @selected(old('receiver_id') == $r->receiver_id)>
                            {{ $r->full_name }} — {{ $r->phone }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('receiver_id')" />
            </div>

            {{-- Origin → Destination --}}
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <x-input-label value="Origin Branch" />
                    <select name="origin_branch_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('origin_branch_id') border-red-400 @enderror">
                        <option value="">Select branch</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->branch_id }}" @selected(old('origin_branch_id') == $b->branch_id)>
                                {{ $b->branch_name }} ({{ $b->city }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('origin_branch_id')" />
                </div>

                <div class="pt-6 shrink-0 text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </div>

                <div class="flex-1">
                    <x-input-label value="Destination Branch" />
                    <select name="destination_branch_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('destination_branch_id') border-red-400 @enderror">
                        <option value="">Select branch</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->branch_id }}" @selected(old('destination_branch_id') == $b->branch_id)>
                                {{ $b->branch_name }} ({{ $b->city }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('destination_branch_id')" />
                </div>
            </div>

            <div>
                <x-input-label value="Weight (kg)" />
                <x-text-input type="number" step="0.1" min="0.1" max="50" name="weight_kg" :value="old('weight_kg')" class="w-full" />
                <x-input-error :messages="$errors->get('weight_kg')" />
            </div>

            <div class="flex gap-3 pt-4 border-t border-slate-100">
                <x-primary-button type="submit">Book Parcel</x-primary-button>
                <a href="{{ route('customer.dashboard') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-customer-layout>
