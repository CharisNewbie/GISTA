@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-gista-500 focus:ring-gista-500 rounded-xl shadow-sm']) }}>
