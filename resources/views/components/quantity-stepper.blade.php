@props(['value' => 1, 'min' => 1, 'max' => 99, 'name' => 'quantity'])

<div {{ $attributes->merge(['class' => 'inline-flex items-center h-10 border border-neutral-200 rounded-md bg-canvas-raised overflow-hidden shadow-1']) }}
     x-data="{ qty: {{ $value }}, min: {{ $min }}, max: {{ $max }} }">
    <button
        type="button"
        @click="if(qty > min) { qty--; $dispatch('qty-changed', qty); }"
        :disabled="qty <= min"
        class="w-10 h-full flex items-center justify-center text-neutral-600 hover:bg-neutral-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
        aria-label="Kurangi jumlah"
    >
        <span class="text-base font-bold leading-none">&minus;</span>
    </button>

    <input
        type="number"
        name="{{ $name }}"
        x-model.number="qty"
        @change="if(qty < min) qty = min; if(qty > max) qty = max; $dispatch('qty-changed', qty);"
        min="{{ $min }}"
        max="{{ $max }}"
        class="w-12 h-full text-center font-body text-sm font-semibold text-neutral-800 bg-transparent border-0 focus:ring-0 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
    >

    <button
        type="button"
        @click="if(qty < max) { qty++; $dispatch('qty-changed', qty); }"
        :disabled="qty >= max"
        class="w-10 h-full flex items-center justify-center text-neutral-600 hover:bg-neutral-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
        aria-label="Tambah jumlah"
    >
        <span class="text-base font-bold leading-none">&plus;</span>
    </button>
</div>
