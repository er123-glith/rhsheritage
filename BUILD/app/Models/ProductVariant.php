<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'variant_label',
        'attribute_map',
        'price',
        'stock_quantity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'attribute_map' => 'array',
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isAvailable(int $qty = 1): bool
    {
        return $this->is_active && $this->stock_quantity >= $qty;
    }

    public function scopeLowStock(Builder $q): void
    {
        $threshold = config('shop.low_stock_threshold', 5);
        $q->where('stock_quantity', '>', 0)
          ->where('stock_quantity', '<=', $threshold);
    }
}
