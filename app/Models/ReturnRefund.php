<?php

namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'amount',
        'bank_name',
        'account_number',
        'account_name',
        'status',
        'payment_reference',
        'proof_path',
        'transferred_at',
        'confirmed_received_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status'                => RefundStatus::class,
            'amount'                => 'decimal:2',
            'transferred_at'        => 'datetime',
            'confirmed_received_at' => 'datetime',
        ];
    }

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class, 'return_id');
    }
}
