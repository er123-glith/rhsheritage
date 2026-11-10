@props(['amount' => 0, 'size' => 'md', 'strike' => false])

@php
$formatted = 'Rp' . number_format((float) $amount, 0, ',', '.');

$classes = match($size) {
    'xl'     => 'font-body font-bold text-2xl sm:text-3xl text-neutral-700',
    'lg'     => 'font-body font-bold text-xl text-neutral-700',
    'md'     => 'font-body font-semibold text-base sm:text-lg text-neutral-700',
    'sm'     => 'font-body font-semibold text-sm text-neutral-700',
    'strike' => 'font-body text-xs sm:text-sm text-neutral-400 line-through',
    'sale'   => 'font-body font-bold text-base sm:text-lg text-danger',
    default  => 'font-body font-semibold text-base text-neutral-700',
};

if ($strike) {
    $classes = 'font-body text-xs sm:text-sm text-neutral-400 line-through';
}
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $formatted }}</span>
