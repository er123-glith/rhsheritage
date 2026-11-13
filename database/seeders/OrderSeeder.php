<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $v1 = ProductVariant::with('product')->first();
        $v2 = ProductVariant::with('product')->skip(1)->first();
        $v3 = ProductVariant::with('product')->skip(2)->first();

        if (! $v1 || ! $v2) {
            return;
        }

        $zoneSurabaya = ShippingZone::where('name', 'like', '%Surabaya%')->first() ?? ShippingZone::first();
        $zoneJatim = ShippingZone::where('name', 'like', '%Jawa Timur%')->first() ?? ShippingZone::first();
        $zoneJawa = ShippingZone::where('name', 'like', '%Pulau Jawa%')->first() ?? ShippingZone::first();
        $zoneLuarJawa = ShippingZone::where('name', 'like', '%Luar Pulau Jawa%')->first() ?? ShippingZone::first();

        // 1. Pesanan Baru: Menunggu Konfirmasi (Ahmad Fauzi)
        $o1 = Order::updateOrCreate(
            ['order_number' => 'ORD-20260826-0001'],
            [
                'customer_name' => 'Ahmad Fauzi',
                'customer_phone' => '081234567890',
                'address_line' => 'Jl. Darmo No. 45, RT 02 / RW 04, Wonokromo',
                'city' => 'Kota Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60241',
                'note' => 'Mohon dikemas rapi untuk kado wisuda santri.',
                'shipping_zone_id' => $zoneSurabaya->id,
                'shipping_zone_name' => $zoneSurabaya->name,
                'total_weight_gram' => 400,
                'subtotal' => $v1->price * 2,
                'shipping_cost' => $zoneSurabaya->base_rate,
                'total' => ($v1->price * 2) + $zoneSurabaya->base_rate,
                'status' => OrderStatus::MenungguKonfirmasi,
                'sent_to_wa_at' => now()->subMinutes(30),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $o1->id, 'product_variant_id' => $v1->id],
            [
                'product_name' => $v1->product->name,
                'variant_label' => $v1->variant_label,
                'unit_price' => $v1->price,
                'quantity' => 2,
                'subtotal' => $v1->price * 2,
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o1->id, 'to_status' => OrderStatus::MenungguKonfirmasi->value],
            [
                'from_status' => OrderStatus::MenungguKonfirmasi->value,
                'note' => 'Pesanan berhasil dibuat oleh pelanggan via form checkout',
                'created_at' => now()->subMinutes(30),
            ]
        );

        // 2. Pesanan Baru: Menunggu Konfirmasi (Siti Nurhaliza)
        $o2 = Order::updateOrCreate(
            ['order_number' => 'ORD-20260826-0002'],
            [
                'customer_name' => 'Siti Nurhaliza',
                'customer_phone' => '085712345678',
                'address_line' => 'Perum Permata Jingga Blok E-12',
                'city' => 'Kota Malang',
                'province' => 'Jawa Timur',
                'postal_code' => '65143',
                'note' => null,
                'shipping_zone_id' => $zoneJatim->id,
                'shipping_zone_name' => $zoneJatim->name,
                'total_weight_gram' => 300,
                'subtotal' => $v2->price * 1,
                'shipping_cost' => $zoneJatim->base_rate,
                'total' => $v2->price + $zoneJatim->base_rate,
                'status' => OrderStatus::MenungguKonfirmasi,
                'sent_to_wa_at' => now()->subHours(1),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $o2->id, 'product_variant_id' => $v2->id],
            [
                'product_name' => $v2->product->name,
                'variant_label' => $v2->variant_label,
                'unit_price' => $v2->price,
                'quantity' => 1,
                'subtotal' => $v2->price,
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o2->id, 'to_status' => OrderStatus::MenungguKonfirmasi->value],
            [
                'from_status' => OrderStatus::MenungguKonfirmasi->value,
                'note' => 'Pesanan berhasil dibuat oleh pelanggan',
                'created_at' => now()->subHours(1),
            ]
        );

        // 3. Pesanan Diproses (Muhammad Rizky)
        $o3 = Order::updateOrCreate(
            ['order_number' => 'ORD-20260825-0015'],
            [
                'customer_name' => 'Muhammad Rizky Pratama',
                'customer_phone' => '082198765432',
                'address_line' => 'Jl. Cihampelas No. 102',
                'city' => 'Kota Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40131',
                'note' => 'Kirim siang hari saat jam kerja kantor.',
                'shipping_zone_id' => $zoneJawa->id,
                'shipping_zone_name' => $zoneJawa->name,
                'total_weight_gram' => 600,
                'subtotal' => ($v1->price * 2) + ($v2->price * 1),
                'shipping_cost' => $zoneJawa->base_rate,
                'total' => ($v1->price * 2) + ($v2->price * 1) + $zoneJawa->base_rate,
                'status' => OrderStatus::Diproses,
                'sent_to_wa_at' => now()->subDay(),
                'confirmed_at' => now()->subDay()->addHours(2),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $o3->id, 'product_variant_id' => $v1->id],
            [
                'product_name' => $v1->product->name,
                'variant_label' => $v1->variant_label,
                'unit_price' => $v1->price,
                'quantity' => 2,
                'subtotal' => $v1->price * 2,
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o3->id, 'to_status' => OrderStatus::MenungguKonfirmasi->value],
            [
                'from_status' => null,
                'note' => 'Pesanan berhasil dibuat oleh pelanggan via form checkout',
                'created_at' => now()->subDay(),
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o3->id, 'to_status' => OrderStatus::Diproses->value],
            [
                'from_status' => OrderStatus::MenungguKonfirmasi->value,
                'note' => 'Dikonfirmasi oleh owner via panel admin',
                'created_at' => now()->subDay()->addHours(2),
            ]
        );

        // 4. Pesanan Dikirim (Hendra Wijaya)
        $o4 = Order::updateOrCreate(
            ['order_number' => 'ORD-20260824-0008'],
            [
                'customer_name' => 'Hendra Wijaya',
                'customer_phone' => '081377889900',
                'address_line' => 'Komplek Asia Mega Mas Blok BB No. 7',
                'city' => 'Kota Medan',
                'province' => 'Sumatera Utara',
                'postal_code' => '20216',
                'note' => null,
                'shipping_zone_id' => $zoneLuarJawa->id,
                'shipping_zone_name' => $zoneLuarJawa->name,
                'total_weight_gram' => 500,
                'subtotal' => $v1->price * 1,
                'shipping_cost' => $zoneLuarJawa->base_rate,
                'total' => $v1->price + $zoneLuarJawa->base_rate,
                'status' => OrderStatus::Dikirim,
                'tracking_number' => 'JNE-88239102910',
                'sent_to_wa_at' => now()->subDays(2),
                'confirmed_at' => now()->subDays(2)->addHour(),
                'shipping_date' => now()->subDays(1),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $o4->id, 'product_variant_id' => $v1->id],
            [
                'product_name' => $v1->product->name,
                'variant_label' => $v1->variant_label,
                'unit_price' => $v1->price,
                'quantity' => 1,
                'subtotal' => $v1->price,
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o4->id, 'to_status' => OrderStatus::MenungguKonfirmasi->value],
            [
                'from_status' => null,
                'note' => 'Pesanan berhasil dibuat oleh pelanggan via form checkout',
                'created_at' => now()->subDays(2),
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o4->id, 'to_status' => OrderStatus::Diproses->value],
            [
                'from_status' => OrderStatus::MenungguKonfirmasi->value,
                'note' => 'Dikonfirmasi oleh owner via panel admin',
                'created_at' => now()->subDays(2)->addHour(),
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o4->id, 'to_status' => OrderStatus::Dikirim->value],
            [
                'from_status' => OrderStatus::Diproses->value,
                'note' => 'Nomor resi pengiriman: JNE-88239102910',
                'created_at' => now()->subDays(1),
            ]
        );

        // 5. Pesanan Selesai (Bambang Sutrisno)
        $o5 = Order::updateOrCreate(
            ['order_number' => 'ORD-20260822-0003'],
            [
                'customer_name' => 'Bambang Sutrisno',
                'customer_phone' => '081299887766',
                'address_line' => 'Jl. Ketintang Baru XII No. 3',
                'city' => 'Kota Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60231',
                'note' => null,
                'shipping_zone_id' => $zoneSurabaya->id,
                'shipping_zone_name' => $zoneSurabaya->name,
                'total_weight_gram' => 300,
                'subtotal' => $v2->price * 1,
                'shipping_cost' => $zoneSurabaya->base_rate,
                'total' => $v2->price + $zoneSurabaya->base_rate,
                'status' => OrderStatus::Selesai,
                'tracking_number' => 'GRAB-EXPRESS-1029',
                'sent_to_wa_at' => now()->subDays(4),
                'confirmed_at' => now()->subDays(4)->addHour(),
                'shipping_date' => now()->subDays(3),
                'delivered_at' => now()->subDays(2),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $o5->id, 'product_variant_id' => $v2->id],
            [
                'product_name' => $v2->product->name,
                'variant_label' => $v2->variant_label,
                'unit_price' => $v2->price,
                'quantity' => 1,
                'subtotal' => $v2->price,
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o5->id, 'to_status' => OrderStatus::MenungguKonfirmasi->value],
            [
                'from_status' => null,
                'note' => 'Pesanan berhasil dibuat oleh pelanggan via form checkout',
                'created_at' => now()->subDays(4),
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o5->id, 'to_status' => OrderStatus::Diproses->value],
            [
                'from_status' => OrderStatus::MenungguKonfirmasi->value,
                'note' => 'Dikonfirmasi oleh owner via panel admin',
                'created_at' => now()->subDays(4)->addHour(),
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o5->id, 'to_status' => OrderStatus::Dikirim->value],
            [
                'from_status' => OrderStatus::Diproses->value,
                'note' => 'Nomor resi pengiriman: GRAB-EXPRESS-1029',
                'created_at' => now()->subDays(3),
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o5->id, 'to_status' => OrderStatus::Selesai->value],
            [
                'from_status' => OrderStatus::Dikirim->value,
                'note' => 'Pesanan telah diterima pelanggan dengan baik',
                'created_at' => now()->subDays(2),
            ]
        );

        // 6. Pesanan Dibatalkan (Dedi Kusnandar)
        $o6 = Order::updateOrCreate(
            ['order_number' => 'ORD-20260821-0001'],
            [
                'customer_name' => 'Dedi Kusnandar',
                'customer_phone' => '087811223344',
                'address_line' => 'Jl. Raya Manyar No. 88',
                'city' => 'Kabupaten Gresik',
                'province' => 'Jawa Timur',
                'postal_code' => '61151',
                'note' => null,
                'shipping_zone_id' => $zoneSurabaya->id,
                'shipping_zone_name' => $zoneSurabaya->name,
                'total_weight_gram' => 200,
                'subtotal' => $v1->price * 1,
                'shipping_cost' => $zoneSurabaya->base_rate,
                'total' => $v1->price + $zoneSurabaya->base_rate,
                'status' => OrderStatus::Dibatalkan,
                'cancellation_reason' => 'Pembeli membatalkan pesanan karena salah menentukan nomor songkok.',
                'sent_to_wa_at' => now()->subDays(5),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $o6->id, 'product_variant_id' => $v1->id],
            [
                'product_name' => $v1->product->name,
                'variant_label' => $v1->variant_label,
                'unit_price' => $v1->price,
                'quantity' => 1,
                'subtotal' => $v1->price,
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o6->id, 'to_status' => OrderStatus::MenungguKonfirmasi->value],
            [
                'from_status' => null,
                'note' => 'Pesanan berhasil dibuat oleh pelanggan via form checkout',
                'created_at' => now()->subDays(5),
            ]
        );

        OrderStatusLog::firstOrCreate(
            ['order_id' => $o6->id, 'to_status' => OrderStatus::Dibatalkan->value],
            [
                'from_status' => OrderStatus::MenungguKonfirmasi->value,
                'note' => 'Dibatalkan: Pembeli membatalkan pesanan karena salah menentukan nomor songkok.',
                'created_at' => now()->subDays(5)->addHours(3),
            ]
        );
    }
}
