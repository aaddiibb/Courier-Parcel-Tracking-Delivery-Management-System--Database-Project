<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rider #{{ $rider->rider_id }}</h2>
            <div class="space-x-2">
                <a href="{{ route('admin.riders.edit', $rider->rider_id) }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Edit</a>
                <a href="{{ route('admin.riders.index') }}" class="text-gray-600 hover:underline text-sm">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $rider->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $rider->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Vehicle Type</dt>
                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $rider->vehicle_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Branch</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $rider->branch_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Active</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($rider->active_flag === 'Y')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">No</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
