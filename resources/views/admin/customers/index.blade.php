<x-admin-layout>
    <x-slot name="header">Customers</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Customers</h1>
            <p class="mt-1 text-sm text-slate-500">{{ count($customers) }} result{{ count($customers) === 1 ? '' : 's' }}</p>
        </div>
        <a href="{{ route('admin.customers.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            + New Customer
        </a>
    </div>

    <div class="space-y-6">

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[220px]">
                    <x-input-label value="Search" />
                    <x-text-input type="text" name="search" :value="$filters['search'] ?? ''" placeholder="Name, phone, or email" class="w-full" />
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-700 pb-2">
                    <input type="checkbox" name="active_only" value="1" @checked(!empty($filters['active_only']))
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Has active parcels
                </label>
                <x-primary-button type="submit">Filter</x-primary-button>
                @if(!empty($filters['search']) || !empty($filters['active_only']))
                    <a href="{{ route('admin.customers.index') }}" class="text-sm text-slate-500 hover:text-slate-700 pb-2">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            @if(empty($customers))
                <div class="py-12 text-center text-slate-400">
                    <p class="text-sm">No customers found.</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">ID</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Name</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Phone</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Email</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($customers as $c)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 text-slate-500">{{ $c->customer_id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $c->full_name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $c->phone }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $c->email ?? '—' }}</td>
                            <td class="px-4 py-3 space-x-3">
                                <a href="{{ route('admin.customers.show', $c->customer_id) }}" class="text-slate-500 hover:text-slate-800">View</a>
                                <a href="{{ route('admin.customers.edit', $c->customer_id) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                <form method="POST" action="{{ route('admin.customers.destroy', $c->customer_id) }}" class="inline"
                                      onsubmit="return confirm('Delete this customer?')">
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

    </div>
</x-admin-layout>
