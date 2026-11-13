<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_name',
        'variant_label',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function returnItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ReturnItem::class, 'order_item_id');
    }

    /**
     * Jumlah kuantitas yang sedang atau telah diajukan retur (kecuali yang ditolak).
     */
    public function activeReturnQuantity(): int
    {
        return (int) $this->returnItems()
            ->whereHas('orderReturn', function ($q) {
                $q->where('status', '!=', \App\Enums\ReturnStatus::Ditolak->value);
            })
            ->sum('quantity');
    }

    /**
     * Sisa kuantitas item yang masih sah untuk diajukan retur.
     */
    public function remainingReturnableQuantity(): int
    {
        return max(0, $this->quantity - $this->activeReturnQuantity());
    }
}
