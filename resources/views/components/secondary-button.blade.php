<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-5 py-2.5 bg-dark-tertiary border border-border-subtle rounded-lg font-semibold text-sm text-warm-white shadow-sm hover:bg-dark-elevated hover:border-gold/30 focus:outline-none focus:ring-2 focus:ring-gold/30 focus:ring-offset-2 focus:ring-offset-dark-primary disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
