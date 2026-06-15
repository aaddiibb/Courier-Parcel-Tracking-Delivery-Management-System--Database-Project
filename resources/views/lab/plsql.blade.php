<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lab Demo — PL/SQL Basics (Lab 11)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8 bg-violet-50 border border-violet-200 rounded-lg px-6 py-4">
                <h3 class="text-base font-semibold text-violet-800 mb-1">Purpose of this page</h3>
                <p class="text-sm text-violet-700">
                    Covers Lab 11 — anonymous PL/SQL blocks only (stored procedures are Day 10).
                    Topics: block structure (DECLARE / BEGIN / EXCEPTION / END), arithmetic operators,
                    comparison and logical operators, the := assignment operator, %TYPE and %ROWTYPE anchors,
                    named exception handlers (NO_DATA_FOUND, TOO_MANY_ROWS), user-defined exceptions,
                    and an explicit cursor with OPEN / FETCH / CLOSE.
                </p>
                <p class="mt-2 text-xs text-violet-600">
                    <strong>Output capture:</strong> DBMS_OUTPUT cannot be read from PHP/OCI8 directly.
                    Each block also inserts its output lines into a global temporary table
                    <code class="bg-violet-100 px-1 rounded">plsql_log</code>
                    (ON COMMIT PRESERVE ROWS), which is then SELECTed here.
                    The SQL panel shows the clean educational version; the executed version additionally
                    writes to plsql_log.
                </p>
            </div>

            @foreach($demos as $demo)
                <div class="mb-10 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $demo['title'] }}</h3>
                                <span class="inline-block mt-1 text-xs font-medium text-violet-700 bg-violet-100 rounded px-2 py-0.5">
                                    {{ $demo['subTopic'] }}
                                </span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ $demo['explanation'] }}</p>
                    </div>

                    {{-- SQL source --}}
                    <div class="px-6 py-4 border-b border-gray-200">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">PL/SQL</p>
                        <pre class="bg-gray-900 text-green-300 rounded p-4 text-sm font-mono overflow-x-auto whitespace-pre-wrap">{{ $demo['displaySql'] }}</pre>
                    </div>

                    {{-- Output --}}
                    <div class="px-6 py-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                            DBMS_OUTPUT
                            @if(!$demo['error'])
                                <span class="ml-2 text-gray-400 font-normal normal-case">
                                    {{ count($demo['output']) }} line(s)
                                </span>
                            @endif
                        </p>

                        @if($demo['error'])
                            <div class="bg-red-50 border border-red-200 rounded p-4">
                                <p class="text-xs font-semibold text-red-700 mb-1">Execution error</p>
                                <pre class="text-xs text-red-600 whitespace-pre-wrap font-mono">{{ $demo['error'] }}</pre>
                                <p class="mt-2 text-xs text-red-500">
                                    Make sure <code>00-logging-table.sql</code> has been run via SQL*Plus first.
                                </p>
                            </div>
                        @elseif(count($demo['output']) === 0)
                            <p class="text-sm text-gray-400 italic">No output captured.</p>
                        @else
                            <ul class="bg-gray-900 rounded p-4 space-y-1">
                                @foreach($demo['output'] as $line)
                                    <li class="text-sm font-mono text-amber-300">{{ $line }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                </div>
            @endforeach

        </div>
    </div>
</x-app-layout>
