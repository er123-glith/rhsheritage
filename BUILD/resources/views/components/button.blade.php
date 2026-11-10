@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])

@php
$base = 'inline-flex items-center justify-center gap-2 rounded-md font-body font-semibold transition-all duration-160 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500 disabled:cursor-not-allowed disabled:bg-neutral-100 disabled:text-neutral-300 disabled:border-transparent';

$variants = [
    'primary'   => 'bg-primary-700 text-white hover:bg-primary-800 active:scale-[.98]',
    'secondary' => 'bg-neutral-50 text-neutral-700 hover:bg-neutral-100',
    'inverted'  => 'bg-neutral-700 text-white hover:bg-neutral-800',
    'outlined'  => 'border border-neutral-200 text-neutral-700 hover:bg-neutral-50',
    'gold'      => 'bg-gold-500 text-neutral-700 hover:bg-gold-600 font-bold',
    'danger'    => 'bg-danger text-white hover:brightness-90',
];

$sizes = [
    'lg'   => 'h-[52px] px-6 text-sm',
    'md'   => 'h-11 px-5 text-sm',
    'sm'   => 'h-9 px-4 text-xs',
    'icon' => 'h-11 w-11',
];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->class([$base, $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md']]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$base, $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md']]) }}>
        {{ $slot }}
    </button>
@endif
