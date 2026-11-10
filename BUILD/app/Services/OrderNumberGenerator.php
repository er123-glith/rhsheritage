<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderNumberGenerator
{
    /**
     * Generate nomor pesanan unik format: RHS-YYYYMMDD-NNNN
     * Menggunakan lockForUpdate untuk mencegah race condition.
     */
    public function generate(): string
    {
        return DB::transaction(function () {
            $prefix = 'RHS-' . now()->format('Ymd') . '-';

            // Ambil nomor urut tertinggi hari ini dengan lock
            $last = Order::where('order_number', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('order_number')
                ->value('order_number');

            if ($last) {
                $lastSeq = (int) substr($last, -4);
                $seq = $lastSeq + 1;
            } else {
                $seq = 1;
            }

            return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        });
    }
}
