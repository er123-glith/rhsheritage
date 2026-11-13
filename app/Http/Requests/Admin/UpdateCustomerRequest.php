<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang berwenang
        return $this->user() && $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => preg_replace('/\s+/', ' ', trim($this->input('name', ''))),
            ]);
        }

        if ($this->has('phone')) {
            $this->merge([
                'phone' => trim($this->input('phone', '')),
            ]);
        }
    }

    public function rules(): array
    {
        $customerId = $this->route('customer') instanceof User
            ? $this->route('customer')->id
            : $this->route('customer');

        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($customerId)->whereNull('deleted_at'),
            ],
            'phone' => ['nullable', 'string', 'max:25', 'regex:/^[0-9+\-\s()]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama pelanggan wajib diisi.',
            'name.max'       => 'Nama pelanggan maksimal 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format alamat email tidak valid.',
            'email.unique'   => 'Alamat email sudah terdaftar oleh pengguna lain.',
            'email.max'      => 'Alamat email maksimal 255 karakter.',
            'phone.regex'    => 'Format nomor telepon tidak valid. Gunakan angka, spasi, atau tanda tambah (+).',
            'phone.max'      => 'Nomor telepon maksimal 25 karakter.',
        ];
    }
}
