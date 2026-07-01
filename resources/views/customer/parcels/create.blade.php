<x-customer-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Book a Parcel</h2>
    </x-slot>

    <div class="max-w-xl bg-white rounded-xl shadow-sm border border-orange-100 p-6">

        @if(empty($receivers))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800 mb-4">
                You need at least one saved receiver before booking a parcel.
                <a href="{{ route('customer.receivers.create') }}" class="font-medium underline">Add one now &rarr;</a>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.parcels.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Receiver</label>
                <select name="receiver_id" class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm @error('receiver_id') border-red-400 @enderror">
                    <option value="">Select receiver</option>
                    @foreach($receivers as $r)
                        <option value="{{ $r->receiver_id }}" @selected(old('receiver_id') == $r->receiver_id)>
                            {{ $r->full_name }} — {{ $r->phone }}
                        </option>
                    @endforeach
                </select>
                @error('receiver_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Origin Branch</label>
                    <select name="origin_branch_id" class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm @error('origin_branch_id') border-red-400 @enderror">
                        <option value="">Select branch</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->branch_id }}" @selected(old('origin_branch_id') == $b->branch_id)>
                                {{ $b->branch_name }} ({{ $b->city }})
                            </option>
                        @endforeach
                    </select>
                    @error('origin_branch_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destination Branch</label>
                    <select name="destination_branch_id" class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm @error('destination_branch_id') border-red-400 @enderror">
                        <option value="">Select branch</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->branch_id }}" @selected(old('destination_branch_id') == $b->branch_id)>
                                {{ $b->branch_name }} ({{ $b->city }})
                            </option>
                        @endforeach
                    </select>
                    @error('destination_branch_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                <input type="number" step="0.1" min="0.1" max="50" name="weight_kg" value="{{ old('weight_kg') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm @error('weight_kg') border-red-400 @enderror">
                @error('weight_kg') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                    Book Parcel
                </button>
                <a href="{{ route('customer.dashboard') }}" class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-customer-layout>
