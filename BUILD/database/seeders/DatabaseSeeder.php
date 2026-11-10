<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            OwnerSeeder::class,
            CategorySeeder::class,
            ShippingZoneSeeder::class,
            PageContentSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            PartnerApplicationSeeder::class,
        ]);
    }
}
