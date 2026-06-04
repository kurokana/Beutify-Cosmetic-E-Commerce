@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-gold text-start text-base font-medium text-gold bg-gold/10 focus:outline-none focus:text-gold-light focus:bg-gold/15 focus:border-gold-light transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-warm-gray hover:text-gold hover:bg-dark-tertiary hover:border-gold/50 focus:outline-none focus:text-gold focus:bg-dark-tertiary focus:border-gold/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
