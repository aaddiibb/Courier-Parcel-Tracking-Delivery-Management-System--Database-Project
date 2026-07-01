<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Parcels</h2>
            <a href="{{ route('admin.parcels.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                + Book Parcel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tracking Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sender</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rider</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($parcels as $p)
                        <tr>
                            <td class="px-6 py-4 text-sm font-mono font-medium text-indigo-700">{{ $p->tracking_code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $p->sender_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $p->destination_city }}</td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $colors = [
                                        'BOOKED'           => 'bg-blue-100 text-blue-800',
                                        'IN_TRANSIT'       => 'bg-yellow-100 text-yellow-800',
                                        'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
                                        'DELIVERED'        => 'bg-green-100 text-green-800',
                                        'RETURNED'         => 'bg-red-100 text-red-800',
                                    ];
                                    $cls = $colors[$p->current_status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $cls }}">
                                    {{ $p->current_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $p->booked_at }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $p->rider_name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.parcels.show', $p->parcel_id) }}" class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
