<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalisasi nomor HP yang sama dengan PlaceOrderRequest (SEC-04).
     */
    protected function prepareForValidation(): void
    {
        $phone = $this->input('customer_phone', '');
        $phone = preg_replace('/\D/', '', $phone);

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
            'order_number'   => ['required', 'string', 'max:30'],
            'customer_phone' => ['required', 'string', 'regex:/^08[0-9]{8,12}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_number.required'   => 'Nomor pesanan wajib diisi.',
            'customer_phone.required' => 'Nomor HP/WhatsApp wajib diisi.',
            'customer_phone.regex'    => 'Format nomor HP tidak valid. Contoh: 08123456789.',
        ];
    }
}
