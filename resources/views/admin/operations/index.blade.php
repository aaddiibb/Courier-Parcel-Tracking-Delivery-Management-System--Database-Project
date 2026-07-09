<x-admin-layout>
    <x-slot name="header">Operations</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Operations — Bulk Parcel Management</h1>
        <p class="mt-1 text-sm text-slate-500">Review stuck parcels and run bulk status corrections via the PL/SQL cursor procedure</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">

        {{-- ── Stuck parcels preview ────────────────────────────────────── --}}
        <div class="lg:col-span-3 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Stuck Parcels Preview</h3>
                <p class="text-xs text-slate-400 mt-0.5">In transit or out for delivery for more than 3 days, grouped by branch and status.</p>
            </div>
            @if(empty($preview))
                <div class="py-12 text-center text-slate-400">
                    <p class="text-sm">Nothing is currently stuck. 🎉</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Branch</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Stuck Count</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Avg. Days Stuck</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($preview as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $row->branch_name }}</td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$row->current_status" />
                            </td>
                            <td class="px-4 py-3 text-slate-900 text-right">{{ $row->stuck_count }}</td>
                            <td class="px-4 py-3 font-semibold text-red-600 text-right">{{ $row->avg_days_stuck }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- ── Bulk update form ─────────────────────────────────────────── --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-amber-200 shadow-sm overflow-hidden" x-data="{ status: 'RETURNED' }">
            <div class="px-5 py-4 border-b border-amber-200 bg-amber-50/50">
                <h3 class="text-sm font-semibold text-amber-800 uppercase tracking-wide">Bulk Update Stuck Parcels</h3>
                <p class="text-xs text-amber-700/80 mt-0.5">Calls a PL/SQL cursor procedure. This action cannot be undone.</p>
            </div>

            <form method="POST" action="{{ route('admin.operations.bulkUpdate') }}" class="p-5 space-y-4"
                  onsubmit="return confirm('This will call a PL/SQL cursor procedure and update every matching parcel. It cannot be undone. Continue?')">
                @csrf

                <div>
                    <x-input-label value="Branch" />
                    <select name="branch_id" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">Select branch</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->branch_id }}" {{ old('branch_id') == $b->branch_id ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label value="Days Threshold" />
                    <x-text-input type="number" name="days_threshold" min="1" :value="old('days_threshold', 3)" required class="w-full" />
                </div>

                <div>
                    <x-input-label value="Target Status" />
                    <div class="flex flex-col gap-1.5 mt-1.5">
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="radio" name="new_status" value="RETURNED" x-model="status"
                                   class="text-indigo-600 focus:ring-indigo-500" {{ old('new_status', 'RETURNED') === 'RETURNED' ? 'checked' : '' }}>
                            RETURNED
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="radio" name="new_status" value="DELIVERED" x-model="status"
                                   class="text-indigo-600 focus:ring-indigo-500" {{ old('new_status') === 'DELIVERED' ? 'checked' : '' }}>
                            DELIVERED
                        </label>
                    </div>
                </div>

                <div class="text-xs rounded-lg px-3 py-2"
                     :class="status === 'DELIVERED' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200'">
                    <template x-if="status === 'RETURNED'">
                        <span>Valid from both IN_TRANSIT and OUT_FOR_DELIVERY — every matching stuck parcel at this branch will update.</span>
                    </template>
                    <template x-if="status === 'DELIVERED'">
                        <span>Only valid from OUT_FOR_DELIVERY. Matching IN_TRANSIT parcels will be skipped (not an error) — the transition isn't legal from that status, so the reported count may be lower than the preview above.</span>
                    </template>
                </div>

                <x-primary-button type="submit" class="w-full flex items-center justify-center">
                    Run Bulk Update
                </x-primary-button>
            </form>
        </div>

    </div>
</x-admin-layout>
