@props([
    'title' => 'Belum Ada Data',
    'message' => 'Tidak ada item yang dapat ditampilkan saat ini.',
    'actionLabel' => null,
    'actionUrl' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 px-4 text-center rounded-xl bg-canvas-sunken/40 border border-dashed border-neutral-300 max-w-lg mx-auto']) }}>
    <div class="w-16 h-16 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center mb-4">
        {{ $icon ?? '' }}
        @if(!isset($icon))
            <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        @endif
    </div>

    <h3 class="font-display text-xl font-bold text-neutral-800 mb-2">
        {{ $title }}
    </h3>

    <p class="font-body text-sm text-neutral-600 max-w-sm mb-6 leading-relaxed">
        {{ $message }}
    </p>

    @if($actionLabel && $actionUrl)
        <x-button :href="$actionUrl" variant="primary" size="md">
            {{ $actionLabel }}
        </x-button>
    @endif
</div>
