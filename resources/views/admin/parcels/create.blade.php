<x-admin-layout>
    <x-slot name="header">Book New Parcel</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-2 text-sm text-slate-400 mb-4">
            <a href="{{ route('admin.parcels.index') }}" class="hover:text-slate-600">Parcels</a>
            <span>/</span>
            <span class="text-slate-600">Book New Parcel</span>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 mb-6">Book New Parcel</h1>

        <form method="POST" action="{{ route('admin.parcels.store') }}" class="bg-white rounded-xl border border-slate-200 shadow-sm">
            @csrf
            <div class="p-6 md:p-8 space-y-5">

                {{-- Sender --}}
                <div>
                    <x-input-label for="sender_customer_id" value="Sender (Customer)" />
                    <select id="sender_customer_id" name="sender_customer_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">— Select customer —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->customer_id }}" {{ old('sender_customer_id') == $c->customer_id ? 'selected' : '' }}>
                                {{ $c->full_name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('sender_customer_id')" />
                </div>

                {{-- Receiver --}}
                <div>
                    <x-input-label for="receiver_id" value="Receiver" />
                    <select id="receiver_id" name="receiver_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">— Select receiver —</option>
                        @foreach($receivers as $r)
                            <option value="{{ $r->receiver_id }}" {{ old('receiver_id') == $r->receiver_id ? 'selected' : '' }}>
                                {{ $r->full_name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('receiver_id')" />
                </div>

                {{-- Origin / Destination --}}
                <div class="grid grid-cols-2 gap-4 items-start">
                    <div>
                        <x-input-label for="origin_branch_id" value="Origin Branch" />
                        <select id="origin_branch_id" name="origin_branch_id"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">— Select —</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}" {{ old('origin_branch_id') == $b->branch_id ? 'selected' : '' }}>
                                    {{ $b->branch_name }} ({{ $b->city }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('origin_branch_id')" />
                    </div>
                    <div>
                        <x-input-label for="destination_branch_id" value="Destination Branch" />
                        <select id="destination_branch_id" name="destination_branch_id"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">— Select —</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}" {{ old('destination_branch_id') == $b->branch_id ? 'selected' : '' }}>
                                    {{ $b->branch_name }} ({{ $b->city }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('destination_branch_id')" />
                    </div>
                </div>

                {{-- Weight --}}
                <div>
                    <x-input-label for="weight_kg" value="Weight (kg)" />
                    <x-text-input id="weight_kg" name="weight_kg" type="number" step="0.1" min="0.1" max="50"
                           value="{{ old('weight_kg') }}" />
                    <x-input-error :messages="$errors->get('weight_kg')" />
                </div>

                {{-- Assigned Rider (optional) --}}
                <div>
                    <x-input-label for="assigned_rider_id" value="Assign Rider (optional)" />
                    <select id="assigned_rider_id" name="assigned_rider_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">— None —</option>
                        @foreach($riders as $r)
                            <option value="{{ $r->rider_id }}" {{ old('assigned_rider_id') == $r->rider_id ? 'selected' : '' }}>
                                {{ $r->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-xl flex items-center justify-between">
                <a href="{{ route('admin.parcels.index') }}"
                   class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2 rounded-lg transition-colors">Cancel</a>
                <x-primary-button>Book Parcel</x-primary-button>
            </div>
        </form>
    </div>
</x-admin-layout>
