@props([])

<div {{ $attributes->merge(['class' => '']) }}>
    {{ $slot }}
</div>
