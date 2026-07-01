<x-customer-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Addresses</h2>
            <a href="{{ route('customer.receivers.create') }}"
               class="px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                + Add Receiver
            </a>
        </div>
    </x-slot>

    @if(empty($receivers))
        <div class="bg-white rounded-xl shadow-sm border border-orange-100 py-16 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-700">No addresses saved yet</h3>
            <p class="mt-1 text-sm text-gray-500">Add a receiver so you can book parcels to them quickly.</p>
            <a href="{{ route('customer.receivers.create') }}"
               class="mt-6 inline-block px-5 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                Add Your First Receiver
            </a>
        </div>
    @else
        <div class="bg-white shadow-sm rounded-xl border border-orange-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($receivers as $r)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $r->full_name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $r->phone }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $r->address ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-customer-layout>
