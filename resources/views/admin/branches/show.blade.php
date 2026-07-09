<x-admin-layout>
    <x-slot name="header">Branch Detail</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <nav class="text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.branches.index') }}" class="hover:text-slate-600">Branches</a>
                <span class="mx-1">/</span>
                <span class="text-slate-500">#{{ $branch->branch_id }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900">{{ $branch->branch_name }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.branches.edit', $branch->branch_id) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Edit</a>
            <a href="{{ route('admin.branches.index') }}" class="hover:bg-slate-100 text-slate-600 text-sm px-3 py-2 rounded-lg transition-colors">&larr; Back</a>
        </div>
    </div>

    <div class="max-w-2xl bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Branch Name</p>
                <p class="text-slate-900 font-medium mt-0.5">{{ $branch->branch_name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">City</p>
                <p class="text-slate-900 font-medium mt-0.5">{{ $branch->city }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Phone</p>
                <p class="text-slate-900 font-medium mt-0.5">{{ $branch->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Manager</p>
                <p class="text-slate-900 font-medium mt-0.5">{{ $branch->manager_name ?? '—' }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-xs text-slate-400 uppercase tracking-wide">Address</p>
                <p class="text-slate-900 font-medium mt-0.5">{{ $branch->address ?? '—' }}</p>
            </div>
        </div>
    </div>
</x-admin-layout>
