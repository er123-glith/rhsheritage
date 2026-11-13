<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminResetCustomerPasswordRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berwenang melakukan permintaan ini.
     * Hanya pengguna dengan peran Administrator yang diizinkan.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Aturan validasi untuk reset kata sandi pelanggan oleh Administrator.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                Password::min(12),
                'confirmed',
            ],
            'password_confirmation' => ['required', 'string'],
            'admin_password'        => ['required', 'string'],
        ];
    }

    /**
     * Pesan kesalahan khusus dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.required'              => 'Kata sandi baru pelanggan wajib diisi.',
            'password.min'                   => 'Kata sandi baru minimal harus 12 karakter.',
            'password.confirmed'             => 'Konfirmasi kata sandi baru tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi kata sandi baru wajib diisi.',
            'admin_password.required'        => 'Kata sandi akun Administrator Anda wajib diisi untuk verifikasi keamanan.',
        ];
    }
}

