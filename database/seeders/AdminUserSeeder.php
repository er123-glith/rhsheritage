<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Jalankan database seeder untuk membuat akun Administrator RHS Heritage.
     * Mengikuti spesifikasi keamanan pada Section H.
     */
    public function run(): void
    {
        $name = env('ADMIN_NAME', config('shop.admin_name', 'RHS Heritage Admin'));
        $email = env('ADMIN_EMAIL', config('shop.admin_email', 'admin@rhsheritage.id'));
        $password = env('ADMIN_PASSWORD', config('shop.admin_password'));

        if (empty($email)) {
            $this->command?->error('ADMIN_EMAIL belum dikonfigurasi pada environment.');
            return;
        }

        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            // Proteksi: cegah eskalasi hak akses akun customer menjadi admin secara sembarangan
            if ($existingUser->isCustomer()) {
                $this->command?->error("Konflik terdeteksi: Email '{$email}' sudah terdaftar sebagai akun customer. Role tidak dapat diubah otomatis menjadi admin.");
                return;
            }

            // Jika akun admin sudah ada, jangan timpa password-nya setiap kali seeder dijalankan
            $this->command?->info("Akun administrator dengan email '{$email}' sudah tersedia. Data dipertahankan tanpa mengubah kata sandi.");
            return;
        }

        // Validasi ketersediaan kata sandi dari environment
        if (empty($password)) {
            $this->command?->error("Variabel environment 'ADMIN_PASSWORD' wajib diatur di file .env sebelum menjalankan seeder ini.");
            return;
        }

        // Buat akun administrator baru dengan role 'admin'
        User::create([
            'name'              => $name,
            'email'             => $email,
            'password'          => Hash::make($password),
            'role'              => 'admin',
            'is_owner'          => true,
            'email_verified_at' => now(),
        ]);

        // Output aman: jangan pernah mencetak plain password ke output konsol
        $this->command?->info("Akun administrator ('{$email}') berhasil dibuat dengan role 'admin'.");
    }
}
