<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attribute_name' => ['required', 'array', 'min:1'],
            'attribute_name.*' => ['required', 'string', 'max:60'],
            'attribute_value' => ['required', 'array', 'min:1'],
            'attribute_value.*' => ['required', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:60', 'unique:product_variants,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
