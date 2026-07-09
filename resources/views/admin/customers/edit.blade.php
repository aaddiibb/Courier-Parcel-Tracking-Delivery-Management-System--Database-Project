<x-admin-layout>
    <x-slot name="header">Edit Customer</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-2 text-sm text-slate-400 mb-4">
            <a href="{{ route('admin.customers.index') }}" class="hover:text-slate-600">Customers</a>
            <span>/</span>
            <span class="text-slate-600">Edit #{{ $customer->customer_id }}</span>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 mb-6">Edit Customer #{{ $customer->customer_id }}</h1>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <form id="edit-form" method="POST" action="{{ route('admin.customers.update', $customer->customer_id) }}">
                @csrf @method('PUT')
                <div class="p-6 md:p-8 space-y-5">

                    <div>
                        <x-input-label for="full_name" value="Full Name *" />
                        <x-text-input id="full_name" name="full_name" type="text"
                            value="{{ old('full_name', $customer->full_name) }}" required />
                        <x-input-error :messages="$errors->get('full_name')" />
                    </div>

                    <div>
                        <x-input-label for="phone" value="Phone *" />
                        <x-text-input id="phone" name="phone" type="text"
                            value="{{ old('phone', $customer->phone) }}" required />
                        <x-input-error :messages="$errors->get('phone')" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email"
                            value="{{ old('email', $customer->email) }}" />
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <x-input-label for="address" value="Address" />
                        <textarea id="address" name="address" rows="3"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-none">{{ old('address', $customer->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" />
                    </div>

                </div>
            </form>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-xl flex items-center justify-between">
                <form method="POST" action="{{ route('admin.customers.destroy', $customer->customer_id) }}"
                      onsubmit="return confirm('Delete this customer?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-sm font-medium px-4 py-2 rounded-lg transition-colors">Delete</button>
                </form>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.customers.index') }}"
                       class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2 rounded-lg transition-colors">Cancel</a>
                    <x-primary-button form="edit-form">Save Changes</x-primary-button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
