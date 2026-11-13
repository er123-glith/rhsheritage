<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'from_status',
        'to_status',
        'note',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => OrderStatus::class,
            'to_status'   => OrderStatus::class,
            'created_at'  => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (OrderStatusLog $log) {
            if (! $log->created_at) {
                $log->created_at = now();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Accessor label status bahasa Indonesia terpusat yang aman untuk string maupun Enum.
     */
    public function getStatusLabelAttribute(): string
    {
        return OrderStatus::labelFor($this->to_status);
    }

    /**
     * Accessor label status asal.
     */
    public function getFromStatusLabelAttribute(): string
    {
        return OrderStatus::labelFor($this->from_status);
    }

    /**
     * Alias atribut notes untuk kompatibilitas jika dipanggil plural.
     */
    public function getNotesAttribute(): ?string
    {
        return $this->note;
    }

    /**
     * Penentu pihak yang melakukan perubahan status secara faktual dan transparan.
     */
    public function getActorLabelAttribute(): string
    {
        $toStatus = $this->to_status instanceof OrderStatus
            ? $this->to_status
            : (is_string($this->to_status) ? OrderStatus::tryFrom($this->to_status) : null);

        $note = strtolower($this->note ?? '');

        if ($toStatus === OrderStatus::MenungguKonfirmasi) {
            return 'Pelanggan (Checkout)';
        }

        if ($toStatus === OrderStatus::Diproses) {
            return 'Owner RHS Heritage';
        }

        if ($toStatus === OrderStatus::Dikirim) {
            return 'Admin / Logistik RHS Heritage';
        }

        if ($toStatus === OrderStatus::Selesai) {
            $confirmedByName = null;
            if ($this->relationLoaded('order') && $this->order) {
                if ($this->order->relationLoaded('deliveryConfirmedBy') && $this->order->deliveryConfirmedBy) {
                    $confirmedByName = $this->order->deliveryConfirmedBy->name;
                }
            }
            return $confirmedByName ? "Pelanggan ({$confirmedByName})" : 'Pelanggan (Konfirmasi Penerimaan)';
        }

        if ($toStatus === OrderStatus::Dibatalkan) {
            if (str_contains($note, 'pembeli') || str_contains($note, 'customer') || str_contains($note, 'pelanggan')) {
                return 'Pelanggan';
            }
            return 'Owner / Admin RHS Heritage';
        }

        return 'Sistem RHS Heritage';
    }

    /**
     * Alias atribut status (kompatibilitas pelacakan publik).
     */
    public function getStatusAttribute(): ?OrderStatus
    {
        if ($this->to_status instanceof OrderStatus) {
            return $this->to_status;
        }

        return is_string($this->to_status) ? OrderStatus::tryFrom($this->to_status) : null;
    }
}
