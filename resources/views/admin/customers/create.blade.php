<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Customer</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.customers.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="full_name" value="Full Name *" />
                        <x-text-input id="full_name" name="full_name" type="text" class="mt-1 block w-full"
                            value="{{ old('full_name') }}" required />
                        <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="phone" value="Phone *" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                            value="{{ old('phone') }}" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            value="{{ old('email') }}" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="address" value="Address" />
                        <textarea id="address" name="address" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('address') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Create Customer</x-primary-button>
                        <a href="{{ route('admin.customers.index') }}" class="text-gray-600 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
