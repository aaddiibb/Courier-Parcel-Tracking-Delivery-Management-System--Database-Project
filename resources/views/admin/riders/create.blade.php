<x-admin-layout>
    <x-slot name="header">New Rider</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-2 text-sm text-slate-400 mb-4">
            <a href="{{ route('admin.riders.index') }}" class="hover:text-slate-600">Riders</a>
            <span>/</span>
            <span class="text-slate-600">New Rider</span>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 mb-6">New Rider</h1>

        <form method="POST" action="{{ route('admin.riders.store') }}" class="bg-white rounded-xl border border-slate-200 shadow-sm">
            @csrf
            <div class="p-6 md:p-8 space-y-5">

                <div>
                    <x-input-label for="full_name" value="Full Name *" />
                    <x-text-input id="full_name" name="full_name" type="text"
                        value="{{ old('full_name') }}" required />
                    <x-input-error :messages="$errors->get('full_name')" />
                </div>

                <div>
                    <x-input-label for="phone" value="Phone *" />
                    <x-text-input id="phone" name="phone" type="text"
                        value="{{ old('phone') }}" required />
                    <x-input-error :messages="$errors->get('phone')" />
                </div>

                <div>
                    <x-input-label for="vehicle_type" value="Vehicle Type *" />
                    <select id="vehicle_type" name="vehicle_type" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">— select —</option>
                        @foreach(['bicycle','motorcycle','van'] as $v)
                            <option value="{{ $v }}" {{ old('vehicle_type') === $v ? 'selected' : '' }}>
                                {{ ucfirst($v) }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('vehicle_type')" />
                </div>

                <div>
                    <x-input-label for="assigned_branch_id" value="Branch *" />
                    <select id="assigned_branch_id" name="assigned_branch_id" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">— select —</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->branch_id }}"
                                {{ old('assigned_branch_id') == $b->branch_id ? 'selected' : '' }}>
                                {{ $b->branch_name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('assigned_branch_id')" />
                </div>

                <div>
                    <x-input-label for="active_flag" value="Active *" />
                    <select id="active_flag" name="active_flag" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="Y" {{ old('active_flag') === 'Y' ? 'selected' : '' }}>Yes</option>
                        <option value="N" {{ old('active_flag') === 'N' ? 'selected' : '' }}>No</option>
                    </select>
                    <x-input-error :messages="$errors->get('active_flag')" />
                </div>

            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-xl flex items-center justify-between">
                <a href="{{ route('admin.riders.index') }}"
                   class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2 rounded-lg transition-colors">Cancel</a>
                <x-primary-button>Create Rider</x-primary-button>
            </div>
        </form>
    </div>
</x-admin-layout>
