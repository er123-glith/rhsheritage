@props([
    'order',
    'context' => 'customer',
])

<x-order.progress-timeline :order="$order" :context="$context" />
