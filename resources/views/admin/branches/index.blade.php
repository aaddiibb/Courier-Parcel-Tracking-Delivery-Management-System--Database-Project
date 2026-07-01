<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Branches</h2>
            <a href="{{ route('admin.branches.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                + New Branch
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($branches as $b)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $b->branch_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $b->branch_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $b->city }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $b->phone ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $b->manager_name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('admin.branches.show', $b->branch_id) }}" class="text-blue-600 hover:underline">View</a>
                                <a href="{{ route('admin.branches.edit', $b->branch_id) }}" class="text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.branches.destroy', $b->branch_id) }}" class="inline"
                                      onsubmit="return confirm('Delete this branch?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
