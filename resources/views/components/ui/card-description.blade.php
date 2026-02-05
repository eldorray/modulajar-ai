@props([])

<p {{ $attributes->merge(['class' => 'text-sm text-[hsl(var(--muted-foreground))] mt-1']) }}>
    {{ $slot }}
</p>
