<x-customer-layout>
    <x-slot name="header">My Addresses</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">My Addresses</h1>
            <p class="mt-1 text-sm text-slate-500">Saved receivers you can book parcels to</p>
        </div>
    </div>

    @if(empty($receivers))
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm py-16 text-center">
            <svg class="mx-auto h-14 w-14 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-slate-700">No addresses saved yet</h3>
            <p class="mt-1 text-sm text-slate-500">Add a receiver so you can book parcels to them quickly.</p>
            <a href="{{ route('customer.receivers.create') }}"
               class="mt-6 inline-block px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                Add Your First Receiver
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($receivers as $r)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                            {{ strtoupper(substr($r->full_name, 0, 2)) }}
                        </div>
                        <p class="font-semibold text-slate-900 truncate">{{ $r->full_name }}</p>
                    </div>
                    <p class="text-sm text-slate-600">{{ $r->phone }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ $r->address ?? '—' }}</p>
                </div>
            @endforeach

            <a href="{{ route('customer.receivers.create') }}"
               class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-300 p-5 text-slate-400 hover:border-indigo-400 hover:text-indigo-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium">Add Address</span>
            </a>
        </div>
    @endif
</x-customer-layout>
