<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Book New Parcel</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.parcels.store') }}">
                    @csrf

                    {{-- Sender --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sender (Customer)</label>
                        <select name="sender_customer_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('sender_customer_id') border-red-500 @enderror">
                            <option value="">— Select customer —</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->customer_id }}" {{ old('sender_customer_id') == $c->customer_id ? 'selected' : '' }}>
                                    {{ $c->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('sender_customer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Receiver --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Receiver</label>
                        <select name="receiver_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('receiver_id') border-red-500 @enderror">
                            <option value="">— Select receiver —</option>
                            @foreach($receivers as $r)
                                <option value="{{ $r->receiver_id }}" {{ old('receiver_id') == $r->receiver_id ? 'selected' : '' }}>
                                    {{ $r->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('receiver_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Origin Branch --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Origin Branch</label>
                        <select name="origin_branch_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('origin_branch_id') border-red-500 @enderror">
                            <option value="">— Select origin branch —</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}" {{ old('origin_branch_id') == $b->branch_id ? 'selected' : '' }}>
                                    {{ $b->branch_name }} ({{ $b->city }})
                                </option>
                            @endforeach
                        </select>
                        @error('origin_branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Destination Branch --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Destination Branch</label>
                        <select name="destination_branch_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('destination_branch_id') border-red-500 @enderror">
                            <option value="">— Select destination branch —</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}" {{ old('destination_branch_id') == $b->branch_id ? 'selected' : '' }}>
                                    {{ $b->branch_name }} ({{ $b->city }})
                                </option>
                            @endforeach
                        </select>
                        @error('destination_branch_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Weight --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                        <input type="number" name="weight_kg" step="0.1" min="0.1" max="50"
                               value="{{ old('weight_kg') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('weight_kg') border-red-500 @enderror">
                        @error('weight_kg')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Assigned Rider (optional) --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign Rider (optional)</label>
                        <select name="assigned_rider_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">— None —</option>
                            @foreach($riders as $r)
                                <option value="{{ $r->rider_id }}" {{ old('assigned_rider_id') == $r->rider_id ? 'selected' : '' }}>
                                    {{ $r->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                            Book Parcel
                        </button>
                        <a href="{{ route('admin.parcels.index') }}" class="px-6 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
