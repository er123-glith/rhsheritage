# Laporan Perbaikan Upload Resi & Detail Pesanan RHS Heritage

### 1. Akar Penyebab Error
- **Error 1 (`PostTooLargeException`):** Pengunggahan file bukti resi melebihi batas `post_max_size` (8 MB) di server tanpa validasi ukuran pada frontend dan tanpa exception handler khusus di `bootstrap/app.php`, sehingga memicu crash unhandled HTTP 413.
- **Error 2 (`label() on string`):** Baris 221 pada `customer/orders/show.blade.php` memanggil `$log->to_status?->label()`, padahal kolom `to_status` pada model `OrderStatusLog` bertipe string mentah (belum dicast ke Enum `OrderStatus`). Operator `?->` tidak mencegah pemanggilan method pada tipe string di PHP 8.

### 2. File yang Diperbaiki
1. `config/uploads.php` (Konfigurasi batas terpusat)
2. `bootstrap/app.php` (Penanganan `PostTooLargeException` & HTTP 413)
3. `resources/views/errors/413.blade.php` (Tampilan error ramah pengguna)
4. `app/Models/OrderStatusLog.php` (Enum casting & accessor label aman)
5. `app/Enums/OrderStatus.php` (Helper `labelFor()` & method `badgeClass()`)
6. `app/Http/Controllers/Admin/OrderController.php` (Validasi backend via config)
7. `app/Http/Controllers/Customer/OrderController.php` (Validasi konfirmasi terima)
8. `resources/views/customer/orders/show.blade.php` (Fix pemanggilan label & UI upload)
9. `resources/views/admin/orders/show.blade.php` (UI validasi resi J&T)
10. `resources/views/customer/returns/create.blade.php` (UI validasi video/foto retur)
11. `tests/Feature/OrderTrackingAndReturnManagementTest.php` (Uji regresi)

### 3. Solusi Backend & Frontend
- **Backend:** Menyediakan konfigurasi upload terpusat (`config/uploads.php`: resi 2 MB, video 100 MB). Menangani `PostTooLargeException` di `bootstrap/app.php` yang mengembalikan respon HTTP 413 ramah pengguna. Menerapkan enum casting dan method statis `OrderStatus::labelFor()`.
- **Frontend:** Validasi ukuran file seketika saat dipilih via Alpine.js, menampilkan nama dan ukuran file, pesan error berbahasa Indonesia, penonaktifan tombol submit saat file melebihi batas, dan tombol ganti berkas.

### 4. Hasil Pengujian & Status Akhir
- 203 automated tests PHPUnit lulus 100% (763 assertions), mencakup uji file valid/invalid/oversize, HTTP 413, dan render riwayat status pesanan.
- **Status:** Selesai dan berfungsi normal (RESOLVED).

