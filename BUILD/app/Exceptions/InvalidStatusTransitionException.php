<?php

namespace App\Exceptions;

use App\Enums\OrderStatus;
use RuntimeException;

class InvalidStatusTransitionException extends RuntimeException
{
    public function __construct(
        public readonly OrderStatus $from,
        public readonly OrderStatus $to,
    ) {
        parent::__construct(
            "Perubahan status tidak diizinkan: dari \"{$from->label()}\" ke \"{$to->label()}\"."
        );
    }
}
