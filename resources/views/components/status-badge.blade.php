@props(['status', 'size' => 'sm'])
@php
$classes = match($status) {
    'BOOKED'            => 'bg-slate-100 text-slate-600',
    'IN_TRANSIT'        => 'bg-blue-100 text-blue-700',
    'OUT_FOR_DELIVERY'  => 'bg-amber-100 text-amber-700',
    'DELIVERED'         => 'bg-emerald-100 text-emerald-700',
    'RETURNED'          => 'bg-red-100 text-red-700',
    default             => 'bg-slate-100 text-slate-600',
};
$label = str_replace('_', ' ', $status);
// Size is a controlled prop, not a merged class — Tailwind utility classes
// all share the same CSS specificity, so a caller-supplied px/py/text-size
// class merged alongside these defaults would win or lose unpredictably
// depending on stylesheet generation order, not HTML order. A prop avoids
// that entirely.
$sizeCls = $size === 'lg' ? 'px-3 py-1.5 text-sm' : 'px-2.5 py-0.5 text-xs';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 $sizeCls rounded-full font-medium $classes"]) }}>
    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
    {{ $label }}
</span>
