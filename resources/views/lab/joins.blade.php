<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lab Demo — JOIN Operations (Labs 9 &amp; 10)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8 bg-indigo-50 border border-indigo-200 rounded-lg px-6 py-4">
                <h3 class="text-base font-semibold text-indigo-800 mb-1">Purpose of this page</h3>
                <p class="text-sm text-indigo-700">
                    This page demonstrates all eight JOIN variants covered in Labs 9 and 10.
                    Each section shows the exact SQL query executed against the live Oracle database,
                    followed by the result set. Row counts are shown next to each result heading so
                    you can sanity-check the output (e.g. the INNER JOIN result should never exceed
                    the total parcel count).
                </p>
            </div>

            @foreach($demos as $demo)
                @include('lab._demo_block', ['demo' => $demo])
            @endforeach

        </div>
    </div>
</x-app-layout>
