<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'type',
        'courier',
        'tracking_number',
        'shipped_at',
        'shipping_proof_path',
        'received_at',
        'received_proof_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'shipped_at'  => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class, 'return_id');
    }
}
