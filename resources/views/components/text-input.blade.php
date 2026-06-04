@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-dark-tertiary border-border-subtle text-warm-white focus:border-gold focus:ring-gold/30 rounded-lg shadow-sm placeholder:text-warm-muted']) }}>
