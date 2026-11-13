<?php

namespace App\Support;

class Terbilang
{
    /**
     * Mengonversi angka integer menjadi teks terbilang bahasa Indonesia.
     */
    public static function make(float|int $angka): string
    {
        $angka = abs((int) $angka);
        $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        $terbilang = '';
        if ($angka < 12) {
            $terbilang = ' ' . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = self::make($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            $sisa = $angka % 10;
            $terbilang = self::make((int)($angka / 10)) . ' Puluh' . ($sisa ? ' ' . self::make($sisa) : '');
        } elseif ($angka < 200) {
            $sisa = $angka - 100;
            $terbilang = 'Seratus' . ($sisa ? ' ' . self::make($sisa) : '');
        } elseif ($angka < 1000) {
            $sisa = $angka % 100;
            $terbilang = self::make((int)($angka / 100)) . ' Ratus' . ($sisa ? ' ' . self::make($sisa) : '');
        } elseif ($angka < 2000) {
            $sisa = $angka - 1000;
            $terbilang = 'Seribu' . ($sisa ? ' ' . self::make($sisa) : '');
        } elseif ($angka < 1000000) {
            $sisa = $angka % 1000;
            $terbilang = self::make((int)($angka / 1000)) . ' Ribu' . ($sisa ? ' ' . self::make($sisa) : '');
        } elseif ($angka < 1000000000) {
            $sisa = $angka % 1000000;
            $terbilang = self::make((int)($angka / 1000000)) . ' Juta' . ($sisa ? ' ' . self::make($sisa) : '');
        } elseif ($angka < 1000000000000) {
            $sisa = $angka % 1000000000;
            $terbilang = self::make((int)($angka / 1000000000)) . ' Miliar' . ($sisa ? ' ' . self::make($sisa) : '');
        }

        return trim($terbilang);
    }

    /**
     * Format Rupiah lengkap dengan kata 'Rupiah' di akhir.
     */
    public static function rupiah(float|int $amount): string
    {
        $amount = (int) $amount;
        if ($amount === 0) {
            return 'Nol Rupiah';
        }

        return self::make($amount) . ' Rupiah';
    }
}
