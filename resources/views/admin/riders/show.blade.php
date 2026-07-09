<x-admin-layout>
    <x-slot name="header">Rider Detail</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <nav class="text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.riders.index') }}" class="hover:text-slate-600">Riders</a>
                <span class="mx-1">/</span>
                <span class="text-slate-500">#{{ $rider->rider_id }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900">{{ $rider->full_name }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.riders.edit', $rider->rider_id) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Edit</a>
            <a href="{{ route('admin.riders.index') }}" class="hover:bg-slate-100 text-slate-600 text-sm px-3 py-2 rounded-lg transition-colors">&larr; Back</a>
        </div>
    </div>

    <div class="max-w-2xl bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Full Name</p>
                <p class="text-slate-900 font-medium mt-0.5">{{ $rider->full_name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Phone</p>
                <p class="text-slate-900 font-medium mt-0.5">{{ $rider->phone }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Vehicle Type</p>
                <p class="text-slate-900 font-medium mt-0.5 capitalize">{{ strtolower($rider->vehicle_type) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Branch</p>
                <p class="text-slate-900 font-medium mt-0.5">{{ $rider->branch_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide">Active</p>
                <p class="mt-0.5">
                    @if($rider->active_flag === 'Y')
                        <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Yes</span>
                    @else
                        <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">No</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</x-admin-layout>
