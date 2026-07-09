<x-admin-layout>
    <x-slot name="header">Branches</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Branches</h1>
            <p class="mt-1 text-sm text-slate-500">{{ count($branches) }} branch{{ count($branches) === 1 ? '' : 'es' }}</p>
        </div>
        <a href="{{ route('admin.branches.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + New Branch
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @if(empty($branches))
            <div class="py-12 text-center text-slate-400">
                <p class="text-sm">No branches found.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">ID</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Branch Name</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">City</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Phone</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Manager</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($branches as $b)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 py-3 text-slate-500">{{ $b->branch_id }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $b->branch_name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $b->city }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $b->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $b->manager_name ?? '—' }}</td>
                        <td class="px-4 py-3 space-x-3">
                            <a href="{{ route('admin.branches.show', $b->branch_id) }}" class="text-slate-500 hover:text-slate-800">View</a>
                            <a href="{{ route('admin.branches.edit', $b->branch_id) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                            <form method="POST" action="{{ route('admin.branches.destroy', $b->branch_id) }}" class="inline"
                                  onsubmit="return confirm('Delete this branch?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-admin-layout>
