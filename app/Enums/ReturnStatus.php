<?php

namespace App\Enums;

enum ReturnStatus: string
{
    case MenungguReview      = 'menunggu_review';
    case PerluInformasi      = 'perlu_informasi';
    case Disetujui           = 'disetujui';
    case Ditolak             = 'ditolak';
    case MenungguBarangBalik = 'menunggu_barang_balik';
    case BarangDiperiksa     = 'barang_diperiksa';
    case DalamPenyelesaian   = 'dalam_penyelesaian';
    case Selesai             = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::MenungguReview      => 'Menunggu Review',
            self::PerluInformasi      => 'Perlu Informasi',
            self::Disetujui           => 'Disetujui',
            self::Ditolak             => 'Ditolak',
            self::MenungguBarangBalik => 'Menunggu Barang Balik',
            self::BarangDiperiksa     => 'Barang Diperiksa',
            self::DalamPenyelesaian   => 'Dalam Penyelesaian',
            self::Selesai             => 'Selesai',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::MenungguReview      => 'bg-amber-100 text-amber-800 border-amber-200',
            self::PerluInformasi      => 'bg-blue-100 text-blue-800 border-blue-200',
            self::Disetujui           => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            self::Ditolak             => 'bg-rose-100 text-rose-800 border-rose-200',
            self::MenungguBarangBalik => 'bg-purple-100 text-purple-800 border-purple-200',
            self::BarangDiperiksa     => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            self::DalamPenyelesaian   => 'bg-teal-100 text-teal-800 border-teal-200',
            self::Selesai             => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        };
    }

    public function badgeClass(): string
    {
        return $this->badgeClasses();
    }

    public function isBlockingOrderCompletion(): bool
    {
        return ! in_array($this, [self::Ditolak, self::Selesai], true);
    }
}

