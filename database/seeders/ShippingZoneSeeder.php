<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        // TODO: OQ-06 — Tarif dan batas wilayah zona pengiriman perlu dikonfirmasi kepada owner.
        // Nilai berikut adalah default/contoh awal yang dapat diubah owner melalui dashboard.
        $zones = [
            [
                'name' => 'Zona 1 — Surabaya Raya',
                'base_rate' => 10000,
                'rate_per_extra_kg' => 5000,
                'description' => 'Wilayah Surabaya, Sidoarjo, Gresik',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Zona 2 — Jawa Timur',
                'base_rate' => 15000,
                'rate_per_extra_kg' => 7000,
                'description' => 'Wilayah Jawa Timur di luar Surabaya Raya',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Zona 3 — Pulau Jawa',
                'base_rate' => 22000,
                'rate_per_extra_kg' => 10000,
                'description' => 'Wilayah Pulau Jawa di luar Jawa Timur (DKI, Jabar, Jateng, DIY, Banten)',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Zona 4 — Luar Pulau Jawa',
                'base_rate' => 45000,
                'rate_per_extra_kg' => 25000,
                'description' => 'Seluruh wilayah di luar Pulau Jawa (Sumatra, Kalimantan, Sulawesi, Bali, Nusa Tenggara, Maluku, Papua)',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::updateOrCreate(['name' => $zone['name']], $zone);
        }
    }
}
