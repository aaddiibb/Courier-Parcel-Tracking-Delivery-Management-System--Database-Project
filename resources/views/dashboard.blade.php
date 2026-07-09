<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="p-6 text-slate-700 text-sm">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
