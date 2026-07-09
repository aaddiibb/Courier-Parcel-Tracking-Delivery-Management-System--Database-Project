<button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors']) }}>
    {{ $slot }}
</button>
