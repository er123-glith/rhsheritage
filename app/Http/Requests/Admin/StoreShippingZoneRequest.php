<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:shipping_zones,name'],
            'base_rate' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'rate_per_extra_kg' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama zona pengiriman wajib diisi.',
            'name.unique' => 'Nama zona pengiriman ini sudah digunakan.',
            'base_rate.required' => 'Tarif dasar (1 kg pertama) wajib diisi.',
            'base_rate.numeric' => 'Tarif dasar harus berupa angka.',
            'rate_per_extra_kg.required' => 'Tarif per kg tambahan wajib diisi.',
            'sort_order.required' => 'Nomor urut prioritas wajib diisi.',
        ];
    }
}
