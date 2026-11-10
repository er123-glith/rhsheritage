<?php

namespace App\Support\Cart;

use App\Models\ProductVariant;

class CartItem
{
    public function __construct(
        public readonly int $variantId,
        public int $quantity,
        public readonly ProductVariant $variant,
    ) {}

    public static function fromVariant(ProductVariant $variant, int $quantity): static
    {
        return new static(
            variantId: $variant->id,
            quantity: $quantity,
            variant: $variant,
        );
    }

    /** Harga satuan dalam Rupiah (integer) */
    public function unitPrice(): int
    {
        return (int) $this->variant->price;
    }

    /** Subtotal item dalam Rupiah (integer) */
    public function subtotal(): int
    {
        return $this->unitPrice() * $this->quantity;
    }

    /** Berat total item dalam gram */
    public function totalWeightGram(): int
    {
        return ($this->variant->product->weight_gram ?? 0) * $this->quantity;
    }
}
