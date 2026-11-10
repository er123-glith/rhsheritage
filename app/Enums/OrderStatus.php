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
