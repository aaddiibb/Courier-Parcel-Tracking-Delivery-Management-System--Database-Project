@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg px-4 py-3 text-sm']) }}>
        {{ $status }}
    </div>
@endif
