<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShippingZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $zoneId = $this->route('shipping_zone')?->id ?? $this->route('ongkir')?->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('shipping_zones', 'name')->ignore($zoneId)],
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
