<x-customer-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add a Receiver</h2>
    </x-slot>

    <div class="max-w-xl bg-white rounded-xl shadow-sm border border-orange-100 p-6">
        <form method="POST" action="{{ route('customer.receivers.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm @error('full_name') border-red-400 @enderror">
                @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm @error('phone') border-red-400 @enderror">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="3"
                          class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm @error('address') border-red-400 @enderror">{{ old('address') }}</textarea>
                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                    Save Receiver
                </button>
                <a href="{{ route('customer.receivers.index') }}" class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-customer-layout>
