<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Review extends Model
{
    use HasFactory;

    public const STATUS_PUBLISHED = 'published';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_HIDDEN = 'hidden';

    protected $fillable = [
        'user_id',
        'order_id',
        'rating',
        'review',
        'moderation_status',
        'moderation_reason',
        'moderated_by',
        'moderated_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'rating'        => 'integer',
            'moderated_at'  => 'datetime',
            'published_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by')->withTrashed();
    }

    /**
     * Scope ulasan yang disetujui dan tayang ke publik.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('moderation_status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope ulasan yang membutuhkan review manual admin.
     */
    public function scopePendingReview(Builder $query): Builder
    {
        return $query->where('moderation_status', self::STATUS_PENDING_REVIEW);
    }

    /**
     * Scope ulasan yang ditolak karena pelanggaran.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('moderation_status', self::STATUS_REJECTED);
    }

    /**
     * Scope ulasan yang disembunyikan admin.
     */
    public function scopeHidden(Builder $query): Builder
    {
        return $query->where('moderation_status', self::STATUS_HIDDEN);
    }

    /**
     * Memastikan ulasan berasal dari transaksi yang sah dan selesai.
     */
    public function isVerifiedPurchase(): bool
    {
        return $this->order && $this->order->status === \App\Enums\OrderStatus::Selesai;
    }

    /**
     * Nama tampilan customer yang aman (privasi terjaga: Ahmad F., Siti N., dsb).
     */
    public function getMaskedCustomerNameAttribute(): string
    {
        $fullName = trim($this->user?->name ?? $this->order?->customer_name ?? 'Pelanggan');
        $parts = preg_split('/\s+/', $fullName);

        if (count($parts) <= 1) {
            return $fullName;
        }

        $first = array_shift($parts);
        $initials = array_map(fn($p) => mb_substr($p, 0, 1) . '.', $parts);

        return $first . ' ' . implode(' ', $initials);
    }

    /**
     * Label status moderasi berbahasa Indonesia untuk UI Admin.
     */
    public function getModerationStatusLabelAttribute(): string
    {
        return match ($this->moderation_status) {
            self::STATUS_PUBLISHED      => 'Dipublikasikan',
            self::STATUS_PENDING_REVIEW => 'Menunggu Moderasi',
            self::STATUS_REJECTED       => 'Ditolak',
            self::STATUS_HIDDEN         => 'Disembunyikan',
            default                     => ucfirst($this->moderation_status),
        };
    }

    /**
     * CSS badge class untuk status moderasi.
     */
    public function getModerationStatusBadgeClassAttribute(): string
    {
        return match ($this->moderation_status) {
            self::STATUS_PUBLISHED      => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-800',
            self::STATUS_PENDING_REVIEW => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-800',
            self::STATUS_REJECTED       => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:border-rose-800',
            self::STATUS_HIDDEN         => 'bg-neutral-100 text-neutral-800 border-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-700',
            default                     => 'bg-neutral-100 text-neutral-800 border-neutral-200',
        };
    }
}
