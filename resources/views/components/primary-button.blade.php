<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-gista-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-gista-700 focus:bg-gista-700 active:bg-gista-800 focus:outline-none focus:ring-2 focus:ring-gista-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
