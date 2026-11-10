<?php

namespace App\Enums;

enum ApplicationType: string
{
    case Grosir = 'grosir';
    case Mitra  = 'mitra';

    public function label(): string
    {
        return match ($this) {
            self::Grosir => 'Harga Grosir',
            self::Mitra  => 'Toko Mitra',
        };
    }
}
