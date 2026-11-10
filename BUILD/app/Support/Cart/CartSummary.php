<?php

namespace App\Support\Cart;

use App\Models\ShippingZone;
use Illuminate\Support\Collection;

class CartSummary
{
    public function __construct(
        /** @var Collection<CartItem> */
        public readonly Collection $items,
        public readonly int $subtotal,
        public readonly int $shippingCost,
        public readonly int $total,
        public readonly ?ShippingZone $zone = null,
    ) {}

    /** Jumlah total item (qty) dalam keranjang */
    public function itemCount(): int
    {
        return $this->items->sum(fn(CartItem $item) => $item->quantity);
    }

    /** Jumlah produk unik dalam keranjang */
    public function uniqueCount(): int
    {
        return $this->items->count();
    }

    /** Total berat seluruh item dalam gram */
    public function totalWeightGram(): int
    {
        return $this->items->sum(fn(CartItem $item) => $item->totalWeightGram());
    }
}
