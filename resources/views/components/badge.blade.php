@props(['type' => 'default', 'label' => null])

@php
$styles = match($type) {
    'discount' => 'bg-gold-200 text-gold-900 border border-gold-300',
    'new'      => 'bg-primary-100 text-primary-900',
    'curated'  => 'bg-primary-700 text-white',
    'warning'  => 'bg-warning-bg text-warning',
    'out'      => 'bg-neutral-200 text-neutral-600',
    default    => 'bg-primary-50 text-primary-700',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded-sm font-body text-xs font-semibold tracking-wide {$styles}"]) }}>
    {{ $label ?? $slot }}
</span>
