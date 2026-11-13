<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'address_line',
        'city',
        'province',
        'postal_code',
        'note',
        'shipping_zone_id',
        'shipping_zone_name',
        'total_weight_gram',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
        'cancellation_reason',
        'tracking_number',
        'courier_name',
        'shipping_date',
        'shipping_proof_path',
        'shipping_note',
        'delivered_at',
        'delivery_proof_path',
        'delivery_confirmed_by',
        'payload_json',
        'sent_to_wa_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload_json'      => 'array',
            'status'            => OrderStatus::class,
            'sent_to_wa_at'     => 'datetime',
            'confirmed_at'      => 'datetime',
            'shipping_date'     => 'datetime',
            'delivered_at'      => 'datetime',
            'subtotal'          => 'decimal:2',
            'shipping_cost'     => 'decimal:2',
            'total'             => 'decimal:2',
            'total_weight_gram' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at', 'asc');
    }

    public function shippingZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function deliveryConfirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_confirmed_by')->withTrashed();
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class, 'order_id')->latest();
    }

    /**
     * Memeriksa apakah pesanan memiliki pengajuan retur aktif yang belum tuntas.
     */
    public function hasActiveReturn(): bool
    {
        return $this->returns()
            ->whereNotIn('status', [
                \App\Enums\ReturnStatus::Ditolak->value,
                \App\Enums\ReturnStatus::Selesai->value,
            ])
            ->exists();
    }
}
