<button {{ $attributes->merge(['type' => 'button', 'class' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-sm font-medium px-4 py-2 rounded-lg transition-colors']) }}>
    {{ $slot }}
</button>
