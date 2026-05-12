@props(['messages'])

@if ($messages)
    @php
        $normalizedMessages = collect((array) $messages)
            ->flatten()
            ->filter(fn ($message) => $message !== null && $message !== '');
    @endphp
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-500 space-y-1 font-medium']) }}>
        @foreach ($normalizedMessages as $message)
            <li>{{ is_string($message) ? $message : json_encode($message) }}</li>
        @endforeach
    </ul>
@endif
