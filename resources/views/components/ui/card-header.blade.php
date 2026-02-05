@props([])

<div {{ $attributes->merge(['class' => 'mb-4 pb-4 border-b border-[hsl(var(--border))]']) }}>
    {{ $slot }}
</div>
