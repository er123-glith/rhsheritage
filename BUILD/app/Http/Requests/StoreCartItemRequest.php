<?php

namespace App\Http\Requests;

use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function messages(): array
    {
        return [
            'variant_id.required' => 'Varian produk harus dipilih.',
            'variant_id.exists'   => 'Varian produk tidak ditemukan.',
            'quantity.required'   => 'Jumlah harus diisi.',
            'quantity.integer'    => 'Jumlah harus berupa angka.',
            'quantity.min'        => 'Jumlah minimal 1.',
            'quantity.max'        => 'Jumlah maksimal 99 per transaksi.',
        ];
    }

    /** Validasi tambahan: varian harus aktif */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isEmpty()) {
                    $variant = ProductVariant::find($this->variant_id);
                    if ($variant && ! $variant->is_active) {
                        $validator->errors()->add('variant_id', 'Varian produk ini sedang tidak tersedia.');
                    }
                }
            },
        ];
    }
}
