<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Rider #{{ $rider->rider_id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.riders.update', $rider->rider_id) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="full_name" value="Full Name *" />
                        <x-text-input id="full_name" name="full_name" type="text" class="mt-1 block w-full"
                            value="{{ old('full_name', $rider->full_name) }}" required />
                        <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="phone" value="Phone *" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                            value="{{ old('phone', $rider->phone) }}" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="vehicle_type" value="Vehicle Type *" />
                        <select id="vehicle_type" name="vehicle_type" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach(['bicycle','motorcycle','van'] as $v)
                                <option value="{{ $v }}"
                                    {{ old('vehicle_type', $rider->vehicle_type) === $v ? 'selected' : '' }}>
                                    {{ ucfirst($v) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="assigned_branch_id" value="Branch *" />
                        <select id="assigned_branch_id" name="assigned_branch_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}"
                                    {{ old('assigned_branch_id', $rider->assigned_branch_id) == $b->branch_id ? 'selected' : '' }}>
                                    {{ $b->branch_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('assigned_branch_id')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="active_flag" value="Active *" />
                        <select id="active_flag" name="active_flag" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Y" {{ old('active_flag', $rider->active_flag) === 'Y' ? 'selected' : '' }}>Yes</option>
                            <option value="N" {{ old('active_flag', $rider->active_flag) === 'N' ? 'selected' : '' }}>No</option>
                        </select>
                        <x-input-error :messages="$errors->get('active_flag')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Save Changes</x-primary-button>
                        <a href="{{ route('admin.riders.index') }}" class="text-gray-600 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
