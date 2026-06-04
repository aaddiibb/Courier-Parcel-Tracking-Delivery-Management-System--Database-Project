<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lab Demo — Subqueries (Labs 13 &amp; 14)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8 bg-emerald-50 border border-emerald-200 rounded-lg px-6 py-4">
                <h3 class="text-base font-semibold text-emerald-800 mb-1">Purpose of this page</h3>
                <p class="text-sm text-emerald-700">
                    This page covers all major subquery forms: non-correlated scalar (runs once, returns one value),
                    non-correlated multi-row IN (runs once, returns a set), correlated EXISTS/NOT EXISTS
                    (re-evaluated per outer row), inline views in the FROM clause, and scalar subqueries
                    in the SELECT list. Each demo shows the exact SQL and the live result set.
                </p>
            </div>

            @foreach($demos as $demo)
                @include('lab._demo_block', ['demo' => $demo])
            @endforeach

        </div>
    </div>
</x-app-layout>
