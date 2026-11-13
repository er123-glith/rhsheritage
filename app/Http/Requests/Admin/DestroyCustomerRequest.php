<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DestroyCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'confirmation' => ['required', 'string', 'in:HAPUS'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirmation.required' => 'Ketik "HAPUS" untuk mengonfirmasi penonaktifan akun pelanggan.',
            'confirmation.in'       => 'Konfirmasi tidak cocok. Anda wajib mengetik "HAPUS" secara persis untuk menonaktifkan akun.',
        ];
    }
}
