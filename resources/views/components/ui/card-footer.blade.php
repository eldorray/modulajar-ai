@props([])

<div {{ $attributes->merge(['class' => 'mt-4 pt-4 border-t border-[hsl(var(--border))]']) }}>
    {{ $slot }}
</div>
