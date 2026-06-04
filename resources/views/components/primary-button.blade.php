<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-gold to-gold-light border border-transparent rounded-lg font-semibold text-sm text-dark-primary shadow-gold-sm hover:shadow-gold-md hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-gold/40 focus:ring-offset-2 focus:ring-offset-dark-primary active:brightness-95 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
