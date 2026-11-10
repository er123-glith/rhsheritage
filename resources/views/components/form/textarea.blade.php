@props([
    'name',
    'label' => null,
    'required' => false,
    'placeholder' => null,
    'hint' => null,
    'rows' => 4,
    'value' => null,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="block font-body text-xs font-semibold uppercase tracking-wider text-neutral-700">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($required) required @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        {{ $attributes->class([
            'w-full px-4 py-3 rounded-md font-body text-sm text-neutral-700 bg-white border transition duration-160 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 placeholder:text-neutral-500',
            'border-danger ring-1 ring-danger' => $errors->has($name),
            'border-neutral-200' => ! $errors->has($name),
        ]) }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="font-body text-xs text-danger flex items-center gap-1 mt-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $message }}</span>
        </p>
    @elseif($hint)
        <p class="font-body text-xs text-neutral-500 mt-1">{{ $hint }}</p>
    @enderror
</div>
