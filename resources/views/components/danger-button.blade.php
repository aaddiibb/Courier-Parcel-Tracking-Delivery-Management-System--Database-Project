<button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-sm font-medium px-4 py-2 rounded-lg transition-colors']) }}>
    {{ $slot }}
</button>
