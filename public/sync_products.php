<?php
// SCRIPT SINKRONISASI PRODUK & FOTO DARI LOCALHOST KE CPANEL
require __DIR__ . '/../laravel/vendor/autoload.php';
$app = require_once __DIR__ . '/../laravel/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('product_images')->truncate();
    DB::table('product_variants')->truncate();
    DB::table('products')->truncate();

    // 1. Insert Products
    $products = array (
  0 => 
  array (
    'id' => 3,
    'category_id' => 1,
    'name' => 'Songkok Motif AC',
    'slug' => 'songkok-motif-ac',
    'description' => 'hadir dengan desain elegan yang dihiasi motif bordir pada bagian badan, dipadukan dengan fitur AC untuk membantu sirkulasi udara. Cocok untuk ibadah, acara formal, maupun momen spesial dengan tampilan yang lebih berkelas.',
    'base_price' => 80000,
    'weight_gram' => 200,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:46:34',
    'updated_at' => '2026-08-31 15:29:43',
  ),
  1 => 
  array (
    'id' => 4,
    'category_id' => 1,
    'name' => 'Songkok Polos Full AC',
    'slug' => 'songkok-polos-full-ac',
    'description' => 'hadir dengan desain polos yang simpel dan elegan, dilengkapi AC pada seluruh bagian atas untuk membantu sirkulasi udara. Nyaman digunakan untuk ibadah, acara formal, maupun aktivitas sehari-hari.',
    'base_price' => 65000,
    'weight_gram' => 130,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-31 15:26:51',
  ),
  2 => 
  array (
    'id' => 5,
    'category_id' => 2,
    'name' => 'Songkok Polos',
    'slug' => 'songkok-polos',
    'description' => 'memiliki desain sederhana dan elegan tanpa tambahan AC. Tampilannya rapi, klasik, dan nyaman digunakan untuk ibadah, acara formal, maupun kegiatan sehari-hari.',
    'base_price' => 60000,
    'weight_gram' => 180,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-31 15:25:06',
  ),
  3 => 
  array (
    'id' => 6,
    'category_id' => 1,
    'name' => 'Songkok AC Polos',
    'slug' => 'songkok-ac-polos',
    'description' => 'merupakan songkok dengan desain polos yang simpel dan elegan. Tampilannya rapi, nyaman digunakan, serta cocok untuk berbagai acara formal maupun kegiatan sehari-hari.',
    'base_price' => 50000,
    'weight_gram' => 101,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-31 15:19:48',
  ),
  4 => 
  array (
    'id' => 7,
    'category_id' => 2,
    'name' => 'Sarung Goyor ATBM',
    'slug' => 'sarung-goyor-atbm',
    'description' => 'merupakan sarung yang dibuat menggunakan **Alat Tenun Bukan Mesin (ATBM)** dengan proses pengerjaan tradisional. Memiliki motif khas dan beragam pilihan warna, bahannya terasa ringan, adem, serta nyaman digunakan untuk ibadah maupun aktivitas sehari-hari.',
    'base_price' => 300000,
    'weight_gram' => 150,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-31 15:16:02',
  ),
  5 => 
  array (
    'id' => 8,
    'category_id' => 3,
    'name' => 'Pashmina Hijab',
    'slug' => 'pashmina-hijab',
    'description' => 'hadir dengan desain simpel dan elegan, mudah dibentuk serta memberikan tampilan yang anggun. Cocok digunakan untuk aktivitas sehari-hari, acara formal, maupun berbagai gaya hijab kekinian.',
    'base_price' => 100000,
    'weight_gram' => 150,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-31 15:14:11',
  ),
  6 => 
  array (
    'id' => 9,
    'category_id' => 1,
    'name' => 'Songkok Polos AC Custom',
    'slug' => 'songkok-polos-ac-custom',
    'description' => 'merupakan songkok polos dengan tambahan AC yang nyaman digunakan. Dapat dipesan secara custom dengan logo, nama, atau identitas yayasan/sekolahan, sehingga cocok untuk seragam santri, siswa, maupun kegiatan resmi lembaga.',
    'base_price' => 50000,
    'weight_gram' => 180,
    'is_active' => 1,
    'created_at' => '2026-08-31 15:31:26',
    'updated_at' => '2026-08-31 15:31:26',
  ),
  7 => 
  array (
    'id' => 10,
    'category_id' => 3,
    'name' => 'Kerudung Segi Empat',
    'slug' => 'kerudung-segi-empat',
    'description' => 'hadir dengan desain sederhana, rapi, dan nyaman digunakan sehari-hari. Dapat dipesan sesuai warna, ukuran, serta logo atau identitas sekolah, sehingga cocok untuk kebutuhan seragam siswi dan kegiatan resmi sekolah.',
    'base_price' => 32000,
    'weight_gram' => 100,
    'is_active' => 1,
    'created_at' => '2026-08-31 15:33:14',
    'updated_at' => '2026-08-31 15:33:14',
  ),
);
    DB::table('products')->insert($products);

    // 2. Insert Variants
    $variants = array (
  0 => 
  array (
    'id' => 4,
    'product_id' => 3,
    'sku' => 'SKK-AC-05',
    'variant_label' => 'No. 5 (Tinggi 9cm)',
    'attribute_map' => '{"Ukuran":"No. 5","Tinggi":"9cm"}',
    'price' => 75000,
    'stock_quantity' => 20,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  1 => 
  array (
    'id' => 5,
    'product_id' => 3,
    'sku' => 'SKK-AC-06',
    'variant_label' => 'No. 6 (Tinggi 9cm)',
    'attribute_map' => '{"Ukuran":"No. 6","Tinggi":"9cm"}',
    'price' => 75000,
    'stock_quantity' => 25,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  2 => 
  array (
    'id' => 6,
    'product_id' => 3,
    'sku' => 'SKK-AC-07',
    'variant_label' => 'No. 7 (Tinggi 9cm)',
    'attribute_map' => '{"Ukuran":"No. 7","Tinggi":"9cm"}',
    'price' => 75000,
    'stock_quantity' => 30,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  3 => 
  array (
    'id' => 7,
    'product_id' => 3,
    'sku' => 'SKK-AC-08',
    'variant_label' => 'No. 8 (Tinggi 9cm)',
    'attribute_map' => '{"Ukuran":"No. 8","Tinggi":"9cm"}',
    'price' => 75000,
    'stock_quantity' => 25,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  4 => 
  array (
    'id' => 8,
    'product_id' => 3,
    'sku' => 'SKK-AC-09',
    'variant_label' => 'No. 9 (Tinggi 9cm)',
    'attribute_map' => '{"Ukuran":"No. 9","Tinggi":"9cm"}',
    'price' => 80000,
    'stock_quantity' => 15,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  5 => 
  array (
    'id' => 9,
    'product_id' => 4,
    'sku' => 'SKK-STD-06',
    'variant_label' => 'No. 6 (Tinggi 9cm)',
    'attribute_map' => '{"Ukuran":"No. 6","Tinggi":"9cm"}',
    'price' => 65000,
    'stock_quantity' => 18,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  6 => 
  array (
    'id' => 10,
    'product_id' => 4,
    'sku' => 'SKK-STD-07',
    'variant_label' => 'No. 7 (Tinggi 9cm)',
    'attribute_map' => '{"Ukuran":"No. 7","Tinggi":"9cm"}',
    'price' => 65000,
    'stock_quantity' => 22,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  7 => 
  array (
    'id' => 11,
    'product_id' => 4,
    'sku' => 'SKK-STD-08',
    'variant_label' => 'No. 8 (Tinggi 9cm)',
    'attribute_map' => '{"Ukuran":"No. 8","Tinggi":"9cm"}',
    'price' => 65000,
    'stock_quantity' => 20,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  8 => 
  array (
    'id' => 21,
    'product_id' => 8,
    'sku' => 'JLB-PRM-S',
    'variant_label' => 'Ukuran S (SD)',
    'attribute_map' => '{"Ukuran":"S (SD)"}',
    'price' => 48000,
    'stock_quantity' => 30,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  9 => 
  array (
    'id' => 22,
    'product_id' => 8,
    'sku' => 'JLB-PRM-M',
    'variant_label' => 'Ukuran M (SMP)',
    'attribute_map' => '{"Ukuran":"M (SMP)"}',
    'price' => 50000,
    'stock_quantity' => 35,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  10 => 
  array (
    'id' => 23,
    'product_id' => 8,
    'sku' => 'JLB-PRM-L',
    'variant_label' => 'Ukuran L (SMA)',
    'attribute_map' => '{"Ukuran":"L (SMA)"}',
    'price' => 54000,
    'stock_quantity' => 28,
    'is_active' => 1,
    'created_at' => '2026-08-26 06:48:23',
    'updated_at' => '2026-08-26 06:48:23',
  ),
  11 => 
  array (
    'id' => 24,
    'product_id' => 7,
    'sku' => NULL,
    'variant_label' => 'REGULAR 125 - 135cm',
    'attribute_map' => '{"REGULAR":"125 - 135cm"}',
    'price' => 300000,
    'stock_quantity' => 100,
    'is_active' => 1,
    'created_at' => '2026-08-31 15:17:58',
    'updated_at' => '2026-08-31 15:28:17',
  ),
  12 => 
  array (
    'id' => 25,
    'product_id' => 6,
    'sku' => 'SK-AC-PLS',
    'variant_label' => '7 57cm',
    'attribute_map' => '{"7":"57cm"}',
    'price' => 50000,
    'stock_quantity' => 50,
    'is_active' => 1,
    'created_at' => '2026-08-31 15:22:56',
    'updated_at' => '2026-08-31 15:22:56',
  ),
  13 => 
  array (
    'id' => 26,
    'product_id' => 5,
    'sku' => 'SK-PLS',
    'variant_label' => '7 57cm',
    'attribute_map' => '{"7":"57cm"}',
    'price' => 60000,
    'stock_quantity' => 50,
    'is_active' => 1,
    'created_at' => '2026-08-31 15:25:39',
    'updated_at' => '2026-08-31 15:25:39',
  ),
  14 => 
  array (
    'id' => 27,
    'product_id' => 4,
    'sku' => 'SK-F-AC',
    'variant_label' => '7 57',
    'attribute_map' => '{"7":"57"}',
    'price' => 65000,
    'stock_quantity' => 44,
    'is_active' => 1,
    'created_at' => '2026-08-31 15:27:51',
    'updated_at' => '2026-08-31 15:27:51',
  ),
  15 => 
  array (
    'id' => 28,
    'product_id' => 9,
    'sku' => NULL,
    'variant_label' => 'Standar',
    'attribute_map' => '[]',
    'price' => 50000,
    'stock_quantity' => 0,
    'is_active' => 1,
    'created_at' => '2026-08-31 15:31:26',
    'updated_at' => '2026-08-31 15:31:26',
  ),
  16 => 
  array (
    'id' => 29,
    'product_id' => 9,
    'sku' => 'SK-CSTM',
    'variant_label' => 'No. 7 (Tinggi 9)',
    'attribute_map' => '{"No. 7":"(Tinggi 9)"}',
    'price' => 50000,
    'stock_quantity' => 100,
    'is_active' => 1,
    'created_at' => '2026-08-31 15:32:18',
    'updated_at' => '2026-08-31 15:32:18',
  ),
  17 => 
  array (
    'id' => 31,
    'product_id' => 10,
    'sku' => 'KRDNG-CSTM',
    'variant_label' => 'L 115x115',
    'attribute_map' => '{"L":"115x115"}',
    'price' => 32000,
    'stock_quantity' => 100,
    'is_active' => 1,
    'created_at' => '2026-08-31 15:34:23',
    'updated_at' => '2026-08-31 15:34:23',
  ),
);
    DB::table('product_variants')->insert($variants);

    // 3. Insert Product Images
    $images = array (
  0 => 
  array (
    'id' => 7,
    'product_id' => 8,
    'path' => 'products/8/646ed09b-8fe5-4be1-93ce-125627c5b456.webp',
    'alt_text' => 'Pashmina Hijab',
    'sort_order' => 0,
    'created_at' => '2026-08-31 15:13:47',
    'updated_at' => '2026-08-31 15:13:47',
  ),
  1 => 
  array (
    'id' => 8,
    'product_id' => 7,
    'path' => 'products/7/00f1caa2-cfb7-4ba5-afb7-0e012f10feb6.webp',
    'alt_text' => 'Sarung Goyor ATBM',
    'sort_order' => 0,
    'created_at' => '2026-08-31 15:16:18',
    'updated_at' => '2026-08-31 15:16:18',
  ),
  2 => 
  array (
    'id' => 9,
    'product_id' => 6,
    'path' => 'products/6/17d621ea-b7ae-48da-a8cd-5f26afe7f98f.webp',
    'alt_text' => 'Songkok AC Polos',
    'sort_order' => 0,
    'created_at' => '2026-08-31 15:20:30',
    'updated_at' => '2026-08-31 15:20:30',
  ),
  3 => 
  array (
    'id' => 10,
    'product_id' => 5,
    'path' => 'products/5/2784a240-3441-4031-8450-42b48bbfa863.webp',
    'alt_text' => 'Songkok Polos',
    'sort_order' => 0,
    'created_at' => '2026-08-31 15:25:17',
    'updated_at' => '2026-08-31 15:25:17',
  ),
  4 => 
  array (
    'id' => 11,
    'product_id' => 4,
    'path' => 'products/4/3177a098-bb0a-4e8e-89d6-ca9831b3a80d.webp',
    'alt_text' => 'Songkok Polos Full AC',
    'sort_order' => 0,
    'created_at' => '2026-08-31 15:27:06',
    'updated_at' => '2026-08-31 15:27:06',
  ),
  5 => 
  array (
    'id' => 12,
    'product_id' => 3,
    'path' => 'products/3/409e4122-c502-4917-8eac-db88f51f6c83.webp',
    'alt_text' => 'Songkok Motif AC',
    'sort_order' => 0,
    'created_at' => '2026-08-31 15:29:59',
    'updated_at' => '2026-08-31 15:29:59',
  ),
  6 => 
  array (
    'id' => 13,
    'product_id' => 9,
    'path' => 'products/9/047da725-9551-40b1-b23c-f2ba12a5bb5b.webp',
    'alt_text' => 'Songkok Polos AC Custom',
    'sort_order' => 0,
    'created_at' => '2026-08-31 15:31:38',
    'updated_at' => '2026-08-31 15:31:38',
  ),
  7 => 
  array (
    'id' => 14,
    'product_id' => 10,
    'path' => 'products/10/67e9b2e7-6de4-42aa-8b35-93281e74b2db.webp',
    'alt_text' => 'Kerudung Segi Empat',
    'sort_order' => 0,
    'created_at' => '2026-08-31 15:33:23',
    'updated_at' => '2026-08-31 15:33:23',
  ),
);
    DB::table('product_images')->insert($images);

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // 4. Create storage symlink jika belum ada
    $target = '/home/rhst3547/laravel/storage/app/public';
    $shortcut = '/home/rhst3547/public_html/storage';
    if (!file_exists($shortcut)) {
        @symlink($target, $shortcut);
    }

    echo '<div style="font-family:sans-serif;padding:30px;background:#101827;color:#fff;border-radius:12px;max-width:600px;margin:40px auto;text-align:center;">';
    echo '<h2 style="color:#34d399;margin-bottom:10px;">✓ SINKRONISASI BERHASIL 100%!</h2>';
    echo '<p style="color:#9ca3af;font-size:15px;line-height:1.6;">Berhasil memasukkan <b>' . count($products) . ' produk</b>, <b>' . count($variants) . ' varian</b>, dan <b>' . count($images) . ' foto produk</b> dari localhost ke server cPanel.</p>';
    echo '<a href="/katalog" style="display:inline-block;margin-top:20px;padding:12px 24px;background:#3b82f6;color:#fff;text-decoration:none;font-weight:bold;border-radius:8px;">Lihat Katalog Sekarang →</a>';
    echo '</div>';
} catch (\Throwable $e) {
    echo '<pre style="color:red;padding:20px;">Error: ' . htmlspecialchars($e->getMessage()) . '</pre>';
}
