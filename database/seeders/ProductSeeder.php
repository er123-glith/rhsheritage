<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $songkok = Category::where('slug', 'songkok')->first();
        $sarung  = Category::where('slug', 'sarung')->first();
        $jilbab  = Category::where('slug', 'jilbab')->first();

        if (!$songkok || !$sarung || !$jilbab) {
            return;
        }

        // 1. Songkok AC
        $p1 = Product::updateOrCreate(
            ['slug' => 'songkok-nasional-beludru-hitam-ac'],
            [
                'category_id' => $songkok->id,
                'name' => 'Songkok Nasional Beludru Hitam AC',
                'description' => "Songkok nasional berkualitas premium dengan bahan beludru impor halus warna hitam pekat. Dilengkapi ventilasi udara (AC) di bagian atas yang memberikan kesejukan dan sirkulasi udara optimal saat digunakan beribadah maupun acara resmi kenegaraan. Struktur kokoh dan tidak mudah penyok.",
                'base_price' => 75000,
                'weight_gram' => 200,
                'is_active' => true,
            ]
        );

        $variantsP1 = [
            ['sku' => 'SKK-AC-05', 'label' => 'No. 5 (Tinggi 9cm)', 'attr' => ['Ukuran' => 'No. 5', 'Tinggi' => '9cm'], 'price' => 75000, 'stock' => 20],
            ['sku' => 'SKK-AC-06', 'label' => 'No. 6 (Tinggi 9cm)', 'attr' => ['Ukuran' => 'No. 6', 'Tinggi' => '9cm'], 'price' => 75000, 'stock' => 25],
            ['sku' => 'SKK-AC-07', 'label' => 'No. 7 (Tinggi 9cm)', 'attr' => ['Ukuran' => 'No. 7', 'Tinggi' => '9cm'], 'price' => 75000, 'stock' => 30],
            ['sku' => 'SKK-AC-08', 'label' => 'No. 8 (Tinggi 9cm)', 'attr' => ['Ukuran' => 'No. 8', 'Tinggi' => '9cm'], 'price' => 75000, 'stock' => 25],
            ['sku' => 'SKK-AC-09', 'label' => 'No. 9 (Tinggi 9cm)', 'attr' => ['Ukuran' => 'No. 9', 'Tinggi' => '9cm'], 'price' => 80000, 'stock' => 15],
        ];

        foreach ($variantsP1 as $v) {
            ProductVariant::updateOrCreate(
                ['product_id' => $p1->id, 'sku' => $v['sku']],
                [
                    'variant_label' => $v['label'],
                    'attribute_map' => $v['attr'],
                    'price' => $v['price'],
                    'stock_quantity' => $v['stock'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Songkok Standar Polos
        $p2 = Product::updateOrCreate(
            ['slug' => 'songkok-exclusive-peci-polos-hitam-non-ac'],
            [
                'category_id' => $songkok->id,
                'name' => 'Songkok Exclusive Peci Polos Hitam Standar',
                'description' => "Peci songkok polos hitam klasik tradisi. Bahan beludru halus dengan jahitan presisi rapi. Nyaman, ringan di kepala, dan awet untuk pemakaian ibadah harian maupun seragam santri pondok pesantren.",
                'base_price' => 65000,
                'weight_gram' => 200,
                'is_active' => true,
            ]
        );

        $variantsP2 = [
            ['sku' => 'SKK-STD-06', 'label' => 'No. 6 (Tinggi 9cm)', 'attr' => ['Ukuran' => 'No. 6', 'Tinggi' => '9cm'], 'price' => 65000, 'stock' => 18],
            ['sku' => 'SKK-STD-07', 'label' => 'No. 7 (Tinggi 9cm)', 'attr' => ['Ukuran' => 'No. 7', 'Tinggi' => '9cm'], 'price' => 65000, 'stock' => 22],
            ['sku' => 'SKK-STD-08', 'label' => 'No. 8 (Tinggi 9cm)', 'attr' => ['Ukuran' => 'No. 8', 'Tinggi' => '9cm'], 'price' => 65000, 'stock' => 20],
        ];

        foreach ($variantsP2 as $v) {
            ProductVariant::updateOrCreate(
                ['product_id' => $p2->id, 'sku' => $v['sku']],
                [
                    'variant_label' => $v['label'],
                    'attribute_map' => $v['attr'],
                    'price' => $v['price'],
                    'stock_quantity' => $v['stock'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Sarung Goyor ATBM Sutra Tradisi
        $p3 = Product::updateOrCreate(
            ['slug' => 'sarung-tenun-atbm-goyor-sutra-tradisi'],
            [
                'category_id' => $sarung->id,
                'name' => 'Sarung Tenun ATBM Goyor Sutra Tradisi',
                'description' => "Mahakarya tenun manual asli pengrajin menggunakan Alat Tenun Bukan Mesin (ATBM). Menghasilkan kain goyor yang sangat jatuh, dingin di kulit saat cuaca panas, dan hangat saat udara dingin. Motif khas tradisi nusantara bernilai koleksi tinggi.",
                'base_price' => 285000,
                'weight_gram' => 450,
                'is_active' => true,
            ]
        );

        $variantsP3 = [
            ['sku' => 'SRG-GYR-GRN', 'label' => 'Motif Botolan Hijau Lumut', 'attr' => ['Motif' => 'Botolan Hijau Lumut'], 'price' => 285000, 'stock' => 10],
            ['sku' => 'SRG-GYR-MRN', 'label' => 'Motif Kombinasi Maroon Emas', 'attr' => ['Motif' => 'Maroon Emas'], 'price' => 285000, 'stock' => 8],
            ['sku' => 'SRG-GYR-BLK', 'label' => 'Motif Klasik Hitam Gold', 'attr' => ['Motif' => 'Hitam Gold'], 'price' => 295000, 'stock' => 12],
        ];

        foreach ($variantsP3 as $v) {
            ProductVariant::updateOrCreate(
                ['product_id' => $p3->id, 'sku' => $v['sku']],
                [
                    'variant_label' => $v['label'],
                    'attribute_map' => $v['attr'],
                    'price' => $v['price'],
                    'stock_quantity' => $v['stock'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Sarung Tenun Mesres Klasik RHS
        $p4 = Product::updateOrCreate(
            ['slug' => 'sarung-tenun-mesres-klasik-rhs'],
            [
                'category_id' => $sarung->id,
                'name' => 'Sarung Tenun Mesres Khas RHS HERITAGE',
                'description' => "Sarung tenun mesres berkualitas dengan kerapatan benang tinggi dan kelembutan ekstra. Warna pekat tidak mudah luntur meski dicuci berkali-kali. Pilihan utama untuk hadiah, seragam pengajian, dan santri.",
                'base_price' => 175000,
                'weight_gram' => 400,
                'is_active' => true,
            ]
        );

        $variantsP4 = [
            ['sku' => 'SRG-MSR-EMR', 'label' => 'Emerald Green', 'attr' => ['Warna' => 'Emerald Green'], 'price' => 175000, 'stock' => 14],
            ['sku' => 'SRG-MSR-NVY', 'label' => 'Royal Navy', 'attr' => ['Warna' => 'Royal Navy'], 'price' => 175000, 'stock' => 16],
            ['sku' => 'SRG-MSR-BRG', 'label' => 'Burgundy Wine', 'attr' => ['Warna' => 'Burgundy Wine'], 'price' => 175000, 'stock' => 12],
        ];

        foreach ($variantsP4 as $v) {
            ProductVariant::updateOrCreate(
                ['product_id' => $p4->id, 'sku' => $v['sku']],
                [
                    'variant_label' => $v['label'],
                    'attribute_map' => $v['attr'],
                    'price' => $v['price'],
                    'stock_quantity' => $v['stock'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Jilbab Bergo Pet Seragam Putih
        $p5 = Product::updateOrCreate(
            ['slug' => 'jilbab-seragam-sekolah-bergo-pet-putih'],
            [
                'category_id' => $jilbab->id,
                'name' => 'Jilbab Seragam Sekolah Bergo Pet Putih Bersih',
                'description' => "Kerudung bergo seragam sekolah dengan pet busa khusus yang tegak antem (anti-tembem) dan tidak lepek saat dicuci. Dibuat dari bahan kaos TC super yang adem menyerap keringat, tidak menerawang, dan sangat rapi dipakai siswi seharian di sekolah.",
                'base_price' => 45000,
                'weight_gram' => 150,
                'is_active' => true,
            ]
        );

        $variantsP5 = [
            ['sku' => 'JLB-WHT-S', 'label' => 'Ukuran S (Tingkat SD/MI)', 'attr' => ['Ukuran' => 'S (SD/MI)'], 'price' => 45000, 'stock' => 40],
            ['sku' => 'JLB-WHT-M', 'label' => 'Ukuran M (Tingkat SMP/MTs)', 'attr' => ['Ukuran' => 'M (SMP/MTs)'], 'price' => 48000, 'stock' => 50],
            ['sku' => 'JLB-WHT-L', 'label' => 'Ukuran L (Tingkat SMA/MA)', 'attr' => ['Ukuran' => 'L (SMA/MA)'], 'price' => 52000, 'stock' => 45],
        ];

        foreach ($variantsP5 as $v) {
            ProductVariant::updateOrCreate(
                ['product_id' => $p5->id, 'sku' => $v['sku']],
                [
                    'variant_label' => $v['label'],
                    'attribute_map' => $v['attr'],
                    'price' => $v['price'],
                    'stock_quantity' => $v['stock'],
                    'is_active' => true,
                ]
            );
        }

        // 6. Jilbab Seragam Pramuka Coklat Tua
        $p6 = Product::updateOrCreate(
            ['slug' => 'jilbab-seragam-pramuka-bergo-coklat-tua'],
            [
                'category_id' => $jilbab->id,
                'name' => 'Jilbab Seragam Pramuka Bergo Pet Coklat Kopi',
                'description' => "Jilbab bergo instan warna coklat kopi resmi standar Gerakan Pramuka. Pet kokoh rapi, jahitan tepi halus, tidak gerah saat kegiatan luar ruangan, dan awet tidak mudah berbulu.",
                'base_price' => 48000,
                'weight_gram' => 150,
                'is_active' => true,
            ]
        );

        $variantsP6 = [
            ['sku' => 'JLB-PRM-S', 'label' => 'Ukuran S (SD)', 'attr' => ['Ukuran' => 'S (SD)'], 'price' => 48000, 'stock' => 30],
            ['sku' => 'JLB-PRM-M', 'label' => 'Ukuran M (SMP)', 'attr' => ['Ukuran' => 'M (SMP)'], 'price' => 50000, 'stock' => 35],
            ['sku' => 'JLB-PRM-L', 'label' => 'Ukuran L (SMA)', 'attr' => ['Ukuran' => 'L (SMA)'], 'price' => 54000, 'stock' => 28],
        ];

        foreach ($variantsP6 as $v) {
            ProductVariant::updateOrCreate(
                ['product_id' => $p6->id, 'sku' => $v['sku']],
                [
                    'variant_label' => $v['label'],
                    'attribute_map' => $v['attr'],
                    'price' => $v['price'],
                    'stock_quantity' => $v['stock'],
                    'is_active' => true,
                ]
            );
        }
    }
}
