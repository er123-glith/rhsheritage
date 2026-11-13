# Laporan Perbaikan Upload Retur & Tombol Submit RHS Heritage

### 1. Penyebab Error Upload & Detail Retur
- Batas PHP lokal (`upload_max_filesize = 2M`, `post_max_size = 8M`) memicu `PostTooLargeException` (HTTP 413) serta kebocoran peringatan PHP (`display_errors = On`).
- Pada halaman detail admin retur, pemanggilan `$return->adminReplacementShipment()` memicu `BadMethodCallException` karena nama fungsi di model semula adalah `replacementShipment()`. Selain itu, relasi `customer()` belum terdefinisi pada `OrderReturn`.

### 2. Perubahan Konfigurasi dan Kode
- **PHP INI:** Mengatur `upload_max_filesize = 110M`, `post_max_size = 128M`, `memory_limit = 256M`, eksekusi/input time 300 detik, dan `display_errors = Off`.
- **Model:** Menambahkan method `adminReplacementShipment()`, `adminShipment()`, relasi `customer()`, serta accessor alamat & email pada [app/Models/OrderReturn.php](file:///c:/Users/ASUS/Downloads/RHS%20Heritage%20website/BUILD/app/Models/OrderReturn.php) dan [app/Models/Order.php](file:///c:/Users/ASUS/Downloads/RHS%20Heritage%20website/BUILD/app/Models/Order.php).
- **Backend:** Menyinkronkan `StoreReturnRequest` (video 100 MB, foto 5 MB), penanganan HTTP 413 di `bootstrap/app.php`, perbaikan tab filter dan array `counts` di `Admin\ReturnController`, serta rollback disk otomatis jika transaksi gagal.

### 3. Penyebab Tombol Tidak Terlihat
Formulir panjang tanpa sticky bar membuat tombol pengajuan tersembunyi di luar area scroll pengguna.

### 4. Solusi UI yang Diterapkan
- **Sticky Action Footer Bar** responsif (`fixed bottom-0 md:left-72`) dilengkapi indikator kelengkapan formulir.
- Validasi instan JavaScript/Alpine.js untuk video (maks 100 MB) dan foto (maks 5 MB).
- Menampilkan bilah progres upload berkas dan pencegahan *double submission*.

### 5. Hasil Pengujian
223 pengujian otomatis PHPUnit lulus 100% (850 assertions), mencakup upload video 10 MB, 95 MB, batas >100 MB, alur Replacement & Refund, akses berkas privat, detail admin retur, dan responsivitas UI.

### 6. Kendala yang Belum Terselesaikan
Tidak ada kendala tersisa.
