<?php

namespace App\Models;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderReturn extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'return_number',
        'order_id',
        'user_id',
        'solution',
        'reason',
        'description',
        'status',
        'admin_notes',
        'rejection_reason',
        'agreed_terms',
    ];

    protected function casts(): array
    {
        return [
            'status'       => ReturnStatus::class,
            'agreed_terms' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'return_number';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function customer(): BelongsTo
    {
        return $this->user();
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(ReturnEvidence::class, 'return_id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(ReturnShipment::class, 'return_id');
    }

    public function refund(): HasOne
    {
        return $this->hasOne(ReturnRefund::class, 'return_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReturnStatusHistory::class, 'return_id')->orderBy('created_at', 'asc');
    }

    public function unboxingVideo(): ?ReturnEvidence
    {
        return $this->evidences->firstWhere('file_type', 'unboxing_video');
    }

    public function photos(): \Illuminate\Support\Collection
    {
        return $this->evidences->where('file_type', 'photo');
    }

    public function customerShipment(): ?ReturnShipment
    {
        return $this->shipments->firstWhere('type', 'return_from_customer');
    }

    public function replacementShipment(): ?ReturnShipment
    {
        return $this->shipments->firstWhere('type', 'replacement_from_admin');
    }

    public function adminReplacementShipment(): ?ReturnShipment
    {
        return $this->replacementShipment();
    }

    public function adminShipment(): ?ReturnShipment
    {
        return $this->replacementShipment();
    }

    public function getCustomerNameAttribute(): ?string
    {
        return $this->customer?->name ?? $this->order?->customer_name;
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->customer?->phone ?? $this->order?->customer_phone;
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->customer?->email ?? $this->order?->customer_email;
    }

    public function reasonLabel(): string
    {
        return match ($this->reason) {
            'rusak_cacat'   => 'Produk Rusak / Cacat',
            'tidak_sesuai'  => 'Produk Tidak Sesuai Deskripsi',
            'salah_varian'  => 'Salah Varian / Ukuran / Warna',
            'jumlah_kurang' => 'Jumlah Barang Kurang',
            'tidak_lengkap' => 'Komponen Produk Tidak Lengkap',
            default         => 'Alasan Lainnya',
        };
    }

    public function solutionLabel(): string
    {
        return match ($this->solution) {
            'replacement' => 'Penggantian Barang (Replacement)',
            'refund'      => 'Pengembalian Dana (Refund)',
            default       => ucfirst($this->solution),
        };
    }
}
