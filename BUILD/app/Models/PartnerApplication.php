<?php

namespace App\Models;

use App\Enums\ApplicationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'applicant_name',
        'institution_name',
        'phone',
        'email',
        'region',
        'product_interest',
        'estimated_quantity',
        'message',
        'is_followed_up',
    ];

    protected function casts(): array
    {
        return [
            'type' => ApplicationType::class,
            'is_followed_up' => 'boolean',
        ];
    }
}
