<?php

namespace App\Enums;

enum OrderStatus: string
{
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Diproses           = 'diproses';
    case Dikirim            = 'dikirim';
    case Selesai            = 'selesai';
    case Dibatalkan         = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::MenungguKonfirmasi => 'Menunggu Konfirmasi',
            self::Diproses           => 'Diproses',
            self::Dikirim            => 'Dikirim',
            self::Selesai            => 'Selesai',
            self::Dibatalkan         => 'Dibatalkan',
        };
    }

    /**
     * Urutan pipeline tahapan utama pesanan (Alur Bisnis RHS Heritage).
     * Urutan wajib: Menunggu Konfirmasi -> Diproses -> Dikirim -> Selesai.
     * Status Dibatalkan berada di cabang tersendiri.
     *
     * @return array<int, self>
     */
    public static function pipelineCases(): array
    {
        return [
            self::MenungguKonfirmasi,
            self::Diproses,
            self::Dikirim,
            self::Selesai,
        ];
    }

    /**
     * Indeks urutan langkah pipeline utama (1-based index).
     */
    public function stepIndex(): int
    {
        return match ($this) {
            self::MenungguKonfirmasi => 1,
            self::Diproses           => 2,
            self::Dikirim            => 3,
            self::Selesai            => 4,
            self::Dibatalkan         => 0,
        };
    }

    /**
     * Ikon Font Awesome terstandarisasi untuk tema RHS Heritage.
     */
    public function icon(): string
    {
        return match ($this) {
            self::MenungguKonfirmasi => 'fa-solid fa-clock',
            self::Diproses           => 'fa-solid fa-box-open',
            self::Dikirim            => 'fa-solid fa-truck-fast',
            self::Selesai            => 'fa-solid fa-circle-check',
            self::Dibatalkan         => 'fa-solid fa-circle-xmark',
        };
    }

    /**
     * Keterangan deskripsi alur bisnis per status.
     */
    public function stepDescription(): string
    {
        return match ($this) {
            self::MenungguKonfirmasi => 'Pesanan berhasil dibuat dan menunggu konfirmasi ketersediaan stok oleh owner.',
            self::Diproses           => 'Pesanan telah dikonfirmasi owner, stok dipotong, dan barang sedang dikemas.',
            self::Dikirim            => 'Paket telah diserahkan ke pihak ekspedisi J&T Express dengan nomor resi resmi.',
            self::Selesai            => 'Pesanan telah tiba dan penerimaan telah dikonfirmasi oleh pembeli.',
            self::Dibatalkan         => 'Pesanan telah dibatalkan dan proses transaksi dihentikan.',
        };
    }

    /**
     * Memeriksa apakah status merupakan bagian dari 4 alur utama.
     */
    public function isPipeline(): bool
    {
        return $this !== self::Dibatalkan;
    }

    /**
     * Helper representasi label terpusat yang aman menerima string maupun Enum instance.
     */
    public static function labelFor(mixed $status): string
    {
        if ($status instanceof self) {
            return $status->label();
        }

        if (is_string($status) && $status !== '') {
            return self::tryFrom($status)?->label() ?? $status;
        }

        return '-';
    }

    /**
     * Badge CSS class untuk representasi UI status pesanan (BR-08 / Desain Bagian 4.9)
     */
    public function badgeClass(): string
    {
        $tokens = $this->colorTokens();
        return ($tokens['bg'] ?? 'bg-neutral-100') . ' ' . ($tokens['text'] ?? 'text-neutral-700');
    }

    public function badgeClasses(): string
    {
        return $this->badgeClass();
    }

    /** Transisi yang diizinkan — BR-08 */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::MenungguKonfirmasi => [self::Diproses, self::Dibatalkan],
            self::Diproses           => [self::Dikirim, self::Dibatalkan],
            self::Dikirim            => [self::Selesai],
            self::Selesai,
            self::Dibatalkan         => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), strict: true);
    }

    /** Token warna dari desain.md — Bagian 4.9 */
    public function colorTokens(): array
    {
        return match ($this) {
            self::MenungguKonfirmasi => ['text' => 'text-[#B8860B]', 'bg' => 'bg-gold-100'],
            self::Diproses           => ['text' => 'text-primary-500', 'bg' => 'bg-primary-50'],
            self::Dikirim            => ['text' => 'text-primary-700', 'bg' => 'bg-primary-100'],
            self::Selesai            => ['text' => 'text-success',     'bg' => 'bg-primary-50'],
            self::Dibatalkan         => ['text' => 'text-danger',      'bg' => 'bg-danger-bg'],
        };
    }
}
