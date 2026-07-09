<x-admin-layout>
    <x-slot name="header">New Branch</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-2 text-sm text-slate-400 mb-4">
            <a href="{{ route('admin.branches.index') }}" class="hover:text-slate-600">Branches</a>
            <span>/</span>
            <span class="text-slate-600">New Branch</span>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 mb-6">New Branch</h1>

        <form method="POST" action="{{ route('admin.branches.store') }}" class="bg-white rounded-xl border border-slate-200 shadow-sm">
            @csrf
            <div class="p-6 md:p-8 space-y-5">

                <div>
                    <x-input-label for="branch_name" value="Branch Name *" />
                    <x-text-input id="branch_name" name="branch_name" type="text"
                        value="{{ old('branch_name') }}" required />
                    <x-input-error :messages="$errors->get('branch_name')" />
                </div>

                <div>
                    <x-input-label for="city" value="City *" />
                    <x-text-input id="city" name="city" type="text"
                        value="{{ old('city') }}" required />
                    <x-input-error :messages="$errors->get('city')" />
                </div>

                <div>
                    <x-input-label for="phone" value="Phone" />
                    <x-text-input id="phone" name="phone" type="text"
                        value="{{ old('phone') }}" />
                    <x-input-error :messages="$errors->get('phone')" />
                </div>

                <div>
                    <x-input-label for="manager_name" value="Manager Name" />
                    <x-text-input id="manager_name" name="manager_name" type="text"
                        value="{{ old('manager_name') }}" />
                    <x-input-error :messages="$errors->get('manager_name')" />
                </div>

                <div>
                    <x-input-label for="address" value="Address" />
                    <textarea id="address" name="address" rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-none">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" />
                </div>

            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-xl flex items-center justify-between">
                <a href="{{ route('admin.branches.index') }}"
                   class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2 rounded-lg transition-colors">Cancel</a>
                <x-primary-button>Create Branch</x-primary-button>
            </div>
        </form>
    </div>
</x-admin-layout>
