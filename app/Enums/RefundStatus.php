<?php

namespace App\Enums;

enum RefundStatus: string
{
    case BelumDiproses      = 'belum_diproses';
    case Diproses           = 'diproses';
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Diterima           = 'diterima';
    case Gagal              = 'gagal';

    public function label(): string
    {
        return match ($this) {
            self::BelumDiproses      => 'Belum Diproses',
            self::Diproses           => 'Diproses',
            self::MenungguKonfirmasi => 'Menunggu Konfirmasi',
            self::Diterima           => 'Diterima',
            self::Gagal              => 'Gagal',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::BelumDiproses      => 'bg-neutral-100 text-neutral-700 border-neutral-200',
            self::Diproses           => 'bg-blue-100 text-blue-800 border-blue-200',
            self::MenungguKonfirmasi => 'bg-amber-100 text-amber-800 border-amber-200',
            self::Diterima           => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::Gagal              => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
