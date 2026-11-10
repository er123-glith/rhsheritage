<?php

namespace App\Exceptions;

use RuntimeException;

class EmptyCartException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Keranjang belanja kosong. Tambahkan produk terlebih dahulu.');
    }
}
