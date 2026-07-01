<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Branch #{{ $branch->branch_id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.branches.update', $branch->branch_id) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="branch_name" value="Branch Name *" />
                        <x-text-input id="branch_name" name="branch_name" type="text" class="mt-1 block w-full"
                            value="{{ old('branch_name', $branch->branch_name) }}" required />
                        <x-input-error :messages="$errors->get('branch_name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="city" value="City *" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                            value="{{ old('city', $branch->city) }}" required />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="phone" value="Phone" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                            value="{{ old('phone', $branch->phone) }}" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="manager_name" value="Manager Name" />
                        <x-text-input id="manager_name" name="manager_name" type="text" class="mt-1 block w-full"
                            value="{{ old('manager_name', $branch->manager_name) }}" />
                        <x-input-error :messages="$errors->get('manager_name')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="address" value="Address" />
                        <textarea id="address" name="address" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $branch->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Save Changes</x-primary-button>
                        <a href="{{ route('admin.branches.index') }}" class="text-gray-600 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
