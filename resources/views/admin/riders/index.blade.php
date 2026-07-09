<x-admin-layout>
    <x-slot name="header">Riders</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Riders</h1>
            <p class="mt-1 text-sm text-slate-500">{{ count($riders) }} rider{{ count($riders) === 1 ? '' : 's' }}</p>
        </div>
        <a href="{{ route('admin.riders.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + New Rider
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @if(empty($riders))
            <div class="py-12 text-center text-slate-400">
                <p class="text-sm">No riders found.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">ID</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Name</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Phone</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Vehicle</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Branch</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Active</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($riders as $r)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 py-3 text-slate-500">{{ $r->rider_id }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $r->full_name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $r->phone }}</td>
                        <td class="px-4 py-3 text-slate-500 capitalize">{{ strtolower($r->vehicle_type) }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $r->branch_name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($r->active_flag === 'Y')
                                <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Yes</span>
                            @else
                                <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 space-x-3">
                            <a href="{{ route('admin.riders.show', $r->rider_id) }}" class="text-slate-500 hover:text-slate-800">View</a>
                            <a href="{{ route('admin.riders.edit', $r->rider_id) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                            <form method="POST" action="{{ route('admin.riders.destroy', $r->rider_id) }}" class="inline"
                                  onsubmit="return confirm('Delete this rider?')">
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
