<?php

namespace App\Http\Requests\Customer;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
    /**
     * Pastikan hanya customer pemilik pesanan yang dapat mengajukan retur.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $order = Order::find($this->input('order_id'));
        if (! $order) {
            return false;
        }

        return $order->user_id === $user->id;
    }

    /**
     * Aturan validasi pengajuan retur barang (Replacement / Refund).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id'       => ['required', 'exists:orders,id'],
            'solution'       => ['required', 'in:replacement,refund'],
            'reason'         => ['required', 'in:rusak_cacat,tidak_sesuai,salah_varian,jumlah_kurang,tidak_lengkap,lainnya'],
            'description'    => ['required', 'string', 'min:10', 'max:2000'],
            'items'          => ['required', 'array', 'min:1'],
            'items.*.id'     => ['required', 'exists:order_items,id'],
            'items.*.qty'    => ['required', 'integer', 'min:1'],
            'unboxing_video' => [
                'required',
                'file',
                'mimetypes:video/mp4,video/quicktime,video/webm',
                'max:102400', // 100 MB
            ],
            'photos'         => ['required', 'array', 'min:1'],
            'photos.*'       => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120', // 5 MB
            ],
            'agreement'      => ['accepted'],
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
            'order_id.required'       => 'Pesanan wajib dipilih.',
            'order_id.exists'         => 'Pesanan tidak ditemukan.',
            'solution.required'       => 'Solusi penyelesaian (Penggantian / Pengembalian Dana) wajib dipilih.',
            'solution.in'             => 'Pilihan solusi tidak valid.',
            'reason.required'         => 'Alasan retur wajib dipilih.',
            'reason.in'               => 'Alasan retur tidak valid.',
            'description.required'    => 'Deskripsi masalah wajib diisi secara jelas.',
            'description.min'         => 'Deskripsi masalah minimal harus 10 karakter.',
            'items.required'          => 'Minimal satu produk harus dipilih untuk diretur.',
            'items.min'               => 'Minimal satu produk harus dipilih untuk diretur.',
            'unboxing_video.required' => 'Video unboxing wajib diunggah untuk pengajuan retur standar.',
            'unboxing_video.file'     => 'Berkas video unboxing tidak valid.',
            'unboxing_video.mimetypes'=> 'Format video unboxing harus berupa MP4, MOV, atau WEBM.',
            'unboxing_video.max'      => 'Ukuran video unboxing maksimal 100 MB.',
            'photos.required'         => 'Minimal satu foto bukti fisik wajib diunggah.',
            'photos.min'              => 'Minimal satu foto bukti fisik wajib diunggah.',
            'photos.*.mimes'          => 'Format foto bukti harus berupa JPG, JPEG, PNG, atau WEBP.',
            'photos.*.max'            => 'Ukuran setiap foto bukti maksimal 5 MB.',
            'agreement.accepted'      => 'Anda harus menyetujui pernyataan keaslian informasi dan bukti retur.',
        ];
    }

    /**
     * Validasi lanjutan: memastikan kuantitas retur tidak melebihi sisa yang sah.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            $orderId = $this->input('order_id');

            foreach ($items as $itemData) {
                if (empty($itemData['id']) || empty($itemData['qty'])) {
                    continue;
                }

                $orderItem = OrderItem::find($itemData['id']);
                if (! $orderItem || $orderItem->order_id != $orderId) {
                    $validator->errors()->add('items', 'Item produk yang dipilih tidak sesuai dengan pesanan.');
                    continue;
                }

                $remaining = $orderItem->remainingReturnableQuantity();
                if ((int) $itemData['qty'] > $remaining) {
                    $validator->errors()->add(
                        'items',
                        "Jumlah retur untuk '{$orderItem->product_name}' ({$itemData['qty']}) melebihi kuantitas yang dapat diretur (maksimal {$remaining})."
                    );
                }
            }
        });
    }
}
