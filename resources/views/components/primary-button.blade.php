<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-gista-600 hover:bg-gista-700 active:bg-gista-800 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-gista-500 focus:ring-offset-2 transition-all duration-150 disabled:opacity-50 disabled:pointer-events-none']) }}>
    {{ $slot }}
</button>
