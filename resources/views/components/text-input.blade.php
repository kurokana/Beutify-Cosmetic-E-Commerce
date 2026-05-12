@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200 focus:border-rose-400 focus:ring-rose-200 rounded-lg shadow-light']) }}>
