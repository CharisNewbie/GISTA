@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200 focus:border-gista-500 focus:ring-gista-500 rounded-xl shadow-sm text-xs transition duration-150']) }}>
