<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lab Demo — Aggregate Functions &amp; HAVING (Labs 11 &amp; 12)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8 bg-amber-50 border border-amber-200 rounded-lg px-6 py-4">
                <h3 class="text-base font-semibold text-amber-800 mb-1">Purpose of this page</h3>
                <p class="text-sm text-amber-700">
                    This page demonstrates Oracle aggregate functions (COUNT, SUM, AVG, MAX, MIN)
                    and conditional aggregates using SUM(CASE WHEN ...). The final four demos
                    show the HAVING clause — the only way to filter on aggregate results — including
                    multi-condition HAVING with computed expressions.
                </p>
            </div>

            @foreach($demos as $demo)
                @include('lab._demo_block', ['demo' => $demo])
            @endforeach

        </div>
    </div>
</x-app-layout>
