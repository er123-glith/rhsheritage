<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi nomor HP sebelum validasi:
     * 08xxx atau +628xxx atau 628xxx → 08xxx
     */
    protected function prepareForValidation(): void
    {
        $phone = $this->input('customer_phone', '');
        $phone = preg_replace('/\D/', '', $phone); // buang non-digit

        if (str_starts_with($phone, '628')) {
            $phone = '0' . substr($phone, 2);
        } elseif (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        $this->merge(['customer_phone' => $phone]);
    }

    public function rules(): array
    {
        return [
            'customer_name'       => ['required', 'string', 'min:2', 'max:100'],
            'customer_phone'      => ['required', 'string', 'regex:/^08[0-9]{8,12}$/'],
            'address_line'        => ['required', 'string', 'min:10', 'max:500'],
            'address_city'        => ['required', 'string', 'min:2', 'max:100'],
            'address_province'    => ['required', 'string', 'min:2', 'max:100'],
            'address_postal_code' => ['required', 'digits:5'],
            'shipping_zone_id'    => ['required', 'integer', 'exists:shipping_zones,id'],
            'address_note'        => ['nullable', 'string', 'max:300'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required'       => 'Nama lengkap wajib diisi.',
            'customer_name.min'            => 'Nama minimal 2 karakter.',
            'customer_phone.required'      => 'Nomor HP/WhatsApp wajib diisi.',
            'customer_phone.regex'         => 'Format nomor HP tidak valid. Contoh: 08123456789.',
            'address_line.required'        => 'Alamat lengkap wajib diisi.',
            'address_line.min'             => 'Alamat terlalu singkat (minimal 10 karakter).',
            'address_city.required'        => 'Kota/Kabupaten wajib diisi.',
            'address_province.required'    => 'Provinsi wajib diisi.',
            'address_postal_code.required' => 'Kode pos wajib diisi.',
            'address_postal_code.digits'   => 'Kode pos harus 5 digit.',
            'shipping_zone_id.required'    => 'Zona pengiriman wajib dipilih.',
            'shipping_zone_id.exists'      => 'Zona pengiriman tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_name'       => 'nama lengkap',
            'customer_phone'      => 'nomor HP/WhatsApp',
            'address_line'        => 'alamat lengkap',
            'address_city'        => 'kota/kabupaten',
            'address_province'    => 'provinsi',
            'address_postal_code' => 'kode pos',
            'shipping_zone_id'    => 'zona pengiriman',
            'address_note'        => 'catatan pengiriman',
        ];
    }
}
