# Laporan Perbaikan Timeline & Riwayat Pesanan RHS Heritage

### 1. Penyebab Urutan Salah
1. Tidak adanya komponen pipeline baku 4 tahap tetap (`menunggu_konfirmasi → diproses → dikirim → selesai`).
2. Log awal checkout sempat tidak menyertakan `created_at` eksplisit sehingga terjadi perbedaan zona waktu (WIB vs UTC) yang menempatkan "Menunggu Konfirmasi" setelah tahap lanjutan.
3. Lazy loading tidak sengaja terpanggil pada accessor aktor log saat mode strict aktif.

### 2. File yang Diperbaiki
- `app/Enums/OrderStatus.php`
- `app/Models/Order.php` & `OrderStatusLog.php`
- `app/Services/OrderService.php`
- `app/Http/Controllers/` (Admin, Customer, Public `OrderController`)
- `resources/views/components/order/progress-timeline.blade.php`
- `resources/views/` (`admin/orders/show`, `customer/orders/show`, `public/order/track`)
- `tests/Feature/OrderStatusTimelineTest.php`

### 3. Perubahan Logika & UI
- **Pipeline Stepper**: Urutan baku 4 tahap (*Completed*, *Current*, *Upcoming*) dengan ikon Font Awesome.
- **Cabang Khusus**: Dibatalkan dan Retur aktif dipisahkan tanpa mencemari 4 tahap utama.
- **Riwayat Kronologis**: Log tersusun kronologis aktual (`created_at ASC, id ASC`), menempatkan Menunggu Konfirmasi di urutan pertama di atas Diproses, diikuti Dikirim dan Selesai.
- **Responsif & Dark Mode**: Tampilan rapi vertikal/horizontal serta bebas lazy-loading error.

### 4. Hasil Pengujian
259 tes PHPUnit lolos 100% (termasuk 15 pengujian menyeluruh skenario timeline, lazy loading, dan status).

### 5. Status Akhir
**Selesai dan Terverifikasi.**
