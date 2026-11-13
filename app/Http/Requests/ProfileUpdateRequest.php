<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Aturan validasi pembaruan profil pengguna (Admin & Customer).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone'       => ['nullable', 'string', 'max:25'],
            'address'     => ['nullable', 'string', 'max:500'],
            'city'        => ['nullable', 'string', 'max:100'],
            'province'    => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
        ];
    }

    /**
     * Pesan kustom validasi profil dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format alamat email tidak valid.',
            'email.unique'   => 'Alamat email ini sudah terdaftar oleh pengguna lain.',
            'phone.max'      => 'Nomor telepon/WhatsApp maksimal 25 karakter.',
            'address.max'    => 'Alamat lengkap maksimal 500 karakter.',
            'city.max'       => 'Kota/Kabupaten maksimal 100 karakter.',
            'province.max'   => 'Provinsi maksimal 100 karakter.',
            'postal_code.max'=> 'Kode pos maksimal 10 karakter.',
        ];
    }
}
