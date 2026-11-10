<?php

namespace App\Exceptions;

use App\Models\Order;
use App\Models\ProductVariant;
use RuntimeException;

class InsufficientStockOnConfirmException extends RuntimeException
{
    public function __construct(
        public readonly Order $order,
        public readonly ProductVariant $variant,
    ) {
        parent::__construct(
            "Gagal mengonfirmasi pesanan #{$order->order_number}: Stok untuk varian \"{$variant->variant_label}\" tidak mencukupi (tersisa {$variant->stock_quantity})."
        );
    }
}
