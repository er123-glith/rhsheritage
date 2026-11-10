<?php

namespace Database\Seeders;

use App\Enums\ApplicationType;
use App\Models\PartnerApplication;
use Illuminate\Database\Seeder;

class PartnerApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $applications = [
            [
                'type' => ApplicationType::Grosir,
                'applicant_name' => 'K.H. Nurul Huda',
                'institution_name' => 'Pondok Pesantren Al-Falah',
                'phone' => '081234567899',
                'email' => 'pesantren.alfalah@example.org',
                'region' => 'Kediri, Jawa Timur',
                'product_interest' => 'Songkok Nasional Beludru Hitam AC',
                'estimated_quantity' => '50 kodi (1.000 pcs)',
                'message' => 'Kami membutuhkan songkok seragam untuk santri baru angkatan 2026. Apakah bisa diberi bordir logo pesantren di bagian dalam?',
                'is_followed_up' => false,
                'created_at' => now()->subHours(4),
            ],
            [
                'type' => ApplicationType::Mitra,
                'applicant_name' => 'Hj. Mardhiyah',
                'institution_name' => 'Toko Busana Muslim Barokah',
                'phone' => '085733445566',
                'email' => 'toko.barokah.yk@example.com',
                'region' => 'Kota Yogyakarta, D.I. Yogyakarta',
                'product_interest' => 'Sarung Tenun Goyor ATBM & Songkok',
                'estimated_quantity' => '20 kodi per bulan',
                'message' => 'Toko kami berlokasi di area strategis dekat Masjid Kauman Yogyakarta. Ingin bermitra memasarkan produk kriya RHS Heritage.',
                'is_followed_up' => false,
                'created_at' => now()->subHours(12),
            ],
            [
                'type' => ApplicationType::Grosir,
                'applicant_name' => 'Drs. H. Mulyadi',
                'institution_name' => 'Yayasan Pendidikan Islam Al-Ikhlas',
                'phone' => '082199887711',
                'email' => 'alikhlas.semarang@example.sch.id',
                'region' => 'Kota Semarang, Jawa Tengah',
                'product_interest' => 'Songkok & Jilbab Sekolah',
                'estimated_quantity' => '30 kodi',
                'message' => 'Permintaan pengadaan seragam jilbab dan songkok untuk siswa SMP & SMA Islam Terpadu.',
                'is_followed_up' => true,
                'created_at' => now()->subDays(2),
            ],
            [
                'type' => ApplicationType::Mitra,
                'applicant_name' => 'Rina Sasmita',
                'institution_name' => 'Butik Madani Craft & Fashion',
                'phone' => '081388776655',
                'email' => 'rina.madani@example.com',
                'region' => 'Jakarta Selatan, DKI Jakarta',
                'product_interest' => 'Sarung Tenun Sutera & Kriya Nusantara',
                'estimated_quantity' => '15 kodi',
                'message' => 'Berminat untuk display consignment / kemitraan agen di butik kami di Gandaria.',
                'is_followed_up' => true,
                'created_at' => now()->subDays(4),
            ],
        ];

        foreach ($applications as $data) {
            PartnerApplication::updateOrCreate(
                [
                    'applicant_name' => $data['applicant_name'],
                    'institution_name' => $data['institution_name'],
                ],
                $data
            );
        }
    }
}
