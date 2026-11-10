<?php

namespace App\Exceptions;

use App\Models\ProductVariant;
use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(
        public readonly ProductVariant $variant,
        public readonly int $available,
        public readonly int $requested = 0,
    ) {
        parent::__construct(
            "Stok tidak mencukupi untuk varian [{$variant->variant_label}]. Tersedia: {$available}."
        );
    }

    public function userMessage(): string
    {
        if ($this->available <= 0) {
            return "Maaf, stok \"{$this->variant->variant_label}\" sudah habis.";
        }

        return "Maaf, stok \"{$this->variant->variant_label}\" hanya tersisa {$this->available} unit.";
    }
}
