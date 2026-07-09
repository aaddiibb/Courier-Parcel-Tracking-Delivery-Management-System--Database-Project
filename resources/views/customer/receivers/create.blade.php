<x-customer-layout>
    <x-slot name="header">Add a Receiver</x-slot>

    <div class="mb-6">
        <nav class="text-xs text-slate-400 mb-1">
            <a href="{{ route('customer.receivers.index') }}" class="hover:text-slate-600">My Addresses</a>
            <span class="mx-1">/</span>
            <span class="text-slate-500">Add a Receiver</span>
        </nav>
        <h1 class="text-2xl font-bold text-slate-900">Add a Receiver</h1>
    </div>

    <div class="max-w-xl bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('customer.receivers.store') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label value="Full Name" />
                <x-text-input type="text" name="full_name" :value="old('full_name')" class="w-full" />
                <x-input-error :messages="$errors->get('full_name')" />
            </div>

            <div>
                <x-input-label value="Phone" />
                <x-text-input type="text" name="phone" :value="old('phone')" class="w-full" />
                <x-input-error :messages="$errors->get('phone')" />
            </div>

            <div>
                <x-input-label value="Address" />
                <textarea name="address" rows="3"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('address') border-red-400 @enderror">{{ old('address') }}</textarea>
                <x-input-error :messages="$errors->get('address')" />
            </div>

            <div class="flex gap-3 pt-4 border-t border-slate-100">
                <x-primary-button type="submit">Save Receiver</x-primary-button>
                <a href="{{ route('customer.receivers.index') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-customer-layout>
