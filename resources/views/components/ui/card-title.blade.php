@props([])

<h3 {{ $attributes->merge(['class' => 'text-lg font-semibold text-[hsl(var(--foreground))]']) }}>
    {{ $slot }}
</h3>
