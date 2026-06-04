@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-warm-white']) }}>
    {{ $value ?? $slot }}
</label>
