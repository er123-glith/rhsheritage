@props(['status'])

@php
use App\Enums\OrderStatus;

$enum = $status instanceof OrderStatus ? $status : OrderStatus::tryFrom($status);
$label = $enum ? $enum->label() : (string)$status;
$colors = $enum ? $enum->colorTokens() : ['text' => 'text-neutral-700', 'bg' => 'bg-neutral-100'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-1 rounded-full font-body text-xs font-semibold {$colors['bg']} {$colors['text']}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full mr-1.5 currentColor bg-current opacity-75"></span>
    {{ $label }}
</span>
