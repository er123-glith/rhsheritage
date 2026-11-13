<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_owner',
        'role',
        'address',
        'city',
        'province',
        'postal_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_owner' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || (bool) $this->is_owner;
    }

    public function isCustomer(): bool
    {
        return ! $this->isAdmin();
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    /**
     * Format alamat lengkap pengguna yang terstruktur.
     */
    public function getFullAddressAttribute(): ?string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->province,
            $this->postal_code,
        ]);

        return ! empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Mendapatkan informasi alamat dan identitas resmi toko/admin untuk invoice dan kontak toko.
     * Mengambil dari profil Admin/Owner yang aktif atau yang memiliki data alamat lengkap terbaru.
     */
    public static function getAdminStoreInfo(?User $preferredAdmin = null): array
    {
        $admin = $preferredAdmin;

        if (! $admin && auth()->check() && auth()->user()->isAdmin()) {
            $admin = auth()->user();
        }

        if (! $admin) {
            // Prioritaskan admin/owner yang alamatnya terisi dan paling baru diperbarui
            $admin = static::where(function ($query) {
                $query->where('is_owner', true)->orWhere('role', 'admin');
            })
            ->whereNotNull('address')
            ->where('address', '!=', '')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();
        }

        if (! $admin) {
            $admin = static::where('is_owner', true)->orWhere('role', 'admin')->latest('id')->first();
        }

        $address = $admin?->full_address 
            ?: ($admin?->address ?: 'Griya RHS Heritage, Jl. Budaya Warisan No. 88, Surakarta, Jawa Tengah 57126');

        $phone = $admin?->phone 
            ?: config('whatsapp.destination_number', '6285728979999');

        $email = $admin?->email 
            ?: config('shop.admin_email', 'admin@rhsheritage.id');

        $city = $admin?->city ?: 'Surakarta';
        $province = $admin?->province ?: 'Jawa Tengah';
        $postalCode = $admin?->postal_code ?: '57126';
        $name = $admin?->name ?: 'Admin RHS HERITAGE';

        return [
            'name'        => $name,
            'address'     => $address,
            'phone'       => $phone,
            'email'       => $email,
            'website'     => 'rhsheritage.my.id',
            'city'        => $city,
            'province'    => $province,
            'postal_code' => $postalCode,
        ];
    }
}
