<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    public function run(): void
    {
        // TODO: OQ-19, OQ-20 — Ketentuan skema harga grosir dan syarat kemitraan perlu dikonfirmasi owner.
        $contents = [
            [
                'key' => 'grosir',
                'title' => 'Ketentuan Pembelian Grosir',
                'body' => '<p>RHS HERITAGE melayani pembelian dalam jumlah besar untuk kebutuhan toko, yayasan, sekolah, maupun komunitas.</p><h3>Keuntungan Pembelian Grosir</h3><ul><li>Harga khusus bertingkat sesuai volume pemesanan.</li><li>Jaminan kualitas bahan dan jahitan standar RHS HERITAGE.</li><li>Dukungan pengiriman ke seluruh wilayah Indonesia.</li></ul><p>Silakan isi formulir pengajuan di bawah untuk mendapatkan penawaran resmi dari kami.</p>',
            ],
            [
                'key' => 'mitra',
                'title' => 'Program Toko Mitra RHS HERITAGE',
                'body' => '<p>Jadilah mitra resmi RHS HERITAGE untuk memasarkan produk songkok, sarung ATBM, dan jilbab sekolah berkualitas di kota Anda.</p><h3>Syarat & Keuntungan Mitra</h3><ul><li>Memiliki toko fisik atau jaringan distribusi aktif.</li><li>Mendapatkan katalog produk terkini dan materi promosi.</li><li>Prioritas alokasi produksi untuk pesanan musiman (Ramadhan/Tahun Ajaran Baru).</li></ul>',
            ],
            [
                'key' => 'tentang',
                'title' => 'Tentang RHS HERITAGE',
                'body' => '<p>RHS HERITAGE adalah usaha keluarga (family business) yang berfokus pada produksi kriya berkualitas tinggi sejak <strong>tahun 2004</strong>.</p><p>Dengan pengalaman lebih dari dua dekade, kami memproduksi songkok bervarian ukuran dan motif, sarung Alat Tenun Bukan Mesin (ATBM), serta kerudung sekolah dengan komitmen pada kenyamanan, kerapian, dan keawetan bahan.</p>',
            ],
        ];

        foreach ($contents as $content) {
            PageContent::updateOrCreate(['key' => $content['key']], $content);
        }
    }
}
