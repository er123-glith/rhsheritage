<?php

namespace App\Http\Requests;

use App\Enums\ApplicationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi nomor HP pemohon.
     */
    protected function prepareForValidation(): void
    {
        $phone = $this->input('phone', '');
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '628')) {
            $phone = '0' . substr($phone, 2);
        } elseif (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        $this->merge(['phone' => $phone]);
    }

    public function rules(): array
    {
        return [
            'type'               => ['required', Rule::enum(ApplicationType::class)],
            'applicant_name'     => ['required', 'string', 'min:2', 'max:100'],
            'institution_name'   => ['nullable', 'string', 'max:150'],
            'phone'              => ['required', 'string', 'regex:/^08[0-9]{8,12}$/'],
            'email'              => ['nullable', 'email', 'max:150'],
            'region'             => ['required', 'string', 'min:3', 'max:150'],
            'product_interest'   => ['required', 'string', 'max:300'],
            'estimated_quantity' => ['required', 'string', 'max:100'],
            'message'            => ['nullable', 'string', 'max:1000'],
            // Honeypot anti-spam (SEC-13) — harus kosong
            'website'            => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'               => 'Jenis pengajuan wajib dipilih.',
            'applicant_name.required'     => 'Nama lengkap wajib diisi.',
            'phone.required'              => 'Nomor HP/WhatsApp wajib diisi.',
            'phone.regex'                 => 'Format nomor HP tidak valid.',
            'region.required'             => 'Kota/Wilayah wajib diisi.',
            'product_interest.required'   => 'Produk yang diminati wajib diisi.',
            'estimated_quantity.required' => 'Estimasi jumlah wajib diisi.',
            'website.max'                 => 'Pengajuan tidak dapat diproses.',
        ];
    }

    public function attributes(): array
    {
        return [
            'type'               => 'jenis pengajuan',
            'applicant_name'     => 'nama lengkap',
            'institution_name'   => 'nama toko/institusi',
            'phone'              => 'nomor HP/WhatsApp',
            'region'             => 'kota/wilayah',
            'product_interest'   => 'produk yang diminati',
            'estimated_quantity' => 'estimasi jumlah',
        ];
    }
}
