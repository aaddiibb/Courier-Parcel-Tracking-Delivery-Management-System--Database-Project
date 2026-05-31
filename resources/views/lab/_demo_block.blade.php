<div class="mb-10 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">{{ $demo['title'] }}</h3>
        <p class="mt-1 text-sm text-gray-600">{{ $demo['explanation'] }}</p>
    </div>

    <div class="px-6 py-4 border-b border-gray-200">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">SQL</p>
        <pre class="bg-gray-900 text-green-300 rounded p-4 text-sm font-mono overflow-x-auto whitespace-pre-wrap">{{ $demo['sql'] }}</pre>
    </div>

    <div class="px-6 py-4">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
            Result
            <span class="ml-2 text-gray-400 font-normal normal-case">{{ count($demo['result']) }} row(s)</span>
        </p>

        @if(count($demo['result']) === 0)
            <p class="text-sm text-gray-400 italic">No rows returned.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(array_keys((array) $demo['result'][0]) as $col)
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                    {{ $col }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($demo['result'] as $row)
                            <tr class="hover:bg-gray-50">
                                @foreach((array) $row as $col => $val)
                                    <td class="px-4 py-2 text-gray-800 font-mono text-xs whitespace-nowrap">
                                        {{ $val ?? 'NULL' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
