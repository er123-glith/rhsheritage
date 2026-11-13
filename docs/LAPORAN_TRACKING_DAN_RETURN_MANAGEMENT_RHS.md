# LAPORAN IMPLEMENTASI ORDER TRACKING, J&T SHIPPING, KONFIRMASI PELANGGAN & RETURN MANAGEMENT SYSTEM (RMS) RHS HERITAGE

## 1. Ringkasan Eksekutif

Pengembangan lanjutan website e-commerce **RHS Heritage** telah berhasil menyelesaikan integrasi menyeluruh pada modul pelacakan pesanan (*order tracking*), pengiriman ekspedisi resmi J&T Express oleh Admin, konfirmasi penerimaan pesanan oleh pelanggan, serta sistem manajemen retur komprehensif (*Return Management System* / RMS) untuk solusi penggantian barang (*replacement*) maupun pengembalian dana (*refund*). Seluruh alur dirancang menggunakan arsitektur *manual refresh* tanpa dependensi WebSocket/API kurir eksternal yang rentan terhadap latensi jaringan atau kuota pihak ketiga.

Sebanyak 29 skenario pengujian baru telah ditambahkan pada `tests/Feature/OrderTrackingAndReturnManagementTest.php`. Seluruh rangkaian pengujian sistemik lokal menghasilkan **198 tests passed** dengan **740 assertions (100% lulus)** tanpa regresi pada fungsionalitas sebelumnya.

---

## 2. Refactoring Fixed Sidebar Dashboard Admin & Customer

Struktur layout dashboard Admin (`resources/views/layouts/admin.blade.php`, `resources/views/components/layouts/admin.blade.php`) dan Customer (`resources/views/components/layouts/customer.blade.php`) telah direfaktor ke model **Fixed Sidebar**:
- **Tampilan Desktop (≥ 768px):** Sidebar terkunci pada posisi kiri layar (`fixed inset-y-0 left-0 w-72 h-screen`), memiliki scrollbar mandiri (`overflow-y-auto`) yang tidak mempengaruhi konten utama.
- **Konten Utama:** Diberi offset padding kiri (`md:pl-72`) dan dapat digulir secara independen tanpa menggeser sidebar.
- **Tampilan Mobile (< 768px):** Menggunakan sistem *off-canvas drawer* responsif berbasis Alpine.js dengan tombol toggle hamburger dan backdrop penutup.
- **Navigasi Baru:** Ditambahkan menu *Manajemen Retur* pada Admin (dengan badge notifikasi pengajuan baru) dan menu *Retur Saya* pada Customer Dashboard.

---

## 3. Tracking Pesanan & J&T Express Shipping Management

1. **Alur Pelacakan Manual:** Pelacakan pesanan menggunakan tombol *"Muat Ulang Status"* yang melakukan refresh halaman secara mandiri.
2. **Pengiriman Admin J&T Express:**
   - Admin memasukkan nomor resi resmi J&T Express, tanggal pengiriman, catatan, dan **wajib mengunggah foto bukti fisik resi** (JPG/PNG/WEBP, maksimal 2 MB).
   - Berkas bukti disimpan di direktori privat `storage/app/secure_proofs/shipping/`.
   - Status pesanan berpindah secara sah dari `diproses` menjadi `dikirim`.
3. **Integritas Status Selesai:** Admin dilarang keras mengubah status pesanan normal secara langsung menjadi `selesai`. Tombol *"Tandai Selesai"* ditiadakan pada dashboard admin untuk pesanan normal.

---

## 4. Konfirmasi Penerimaan Barang oleh Pelanggan

1. **Aksi Pelanggan:** Pada pesanan berstatus `dikirim`, tombol *"Konfirmasi Barang Diterima"* muncul pada halaman detail pesanan pelanggan.
2. **Validasi & Bukti:** Pelanggan **wajib mengunggah foto produk yang telah diterima** (maksimal 5 MB) dan mencentang persetujuan kebenaran penerimaan barang.
3. **Transisi Status:** Sistem mencatat `delivered_at`, `delivery_confirmed_by`, dan berkas `delivery_proof_path`, kemudian mengubah status menjadi `selesai`.
4. **Proteksi Sengketa:** Konfirmasi penerimaan otomatis diblokir apabila pesanan memiliki pengajuan retur aktif yang belum terselesaikan.

---

## 5. Return Management System (RMS): Alur Penggantian & Refund

RMS dibangun dengan dukungan dua skema penyelesaian:
- **Penggantian Barang (Replacement):**
  1. *Pengajuan:* Pelanggan memilih varian & kuantitas retur (dibatasi tidak melebihi sisa kuantitas sah), alasan retur, deskripsi, **wajib mengunggah video unboxing utuh** (MP4/MOV/WEBM, maksimal 100 MB), serta foto bukti kerusakan.
  2. *Tinjauan Admin:* Admin dapat menyetujui, menolak (wajib menyertakan alasan tertulis), atau meminta informasi tambahan.
  3. *Pengiriman Balik:* Pelanggan mengirim barang via J&T Express dan mengunggah nomor resi serta struk.
  4. *Verifikasi Gudang:* Admin memverifikasi kedatangan fisik barang di gudang.
  5. *Pengiriman Barang Pengganti:* Admin mengunggah resi J&T baru & bukti struk. Sistem **secara otomatis memvalidasi dan memotong stok varian pengganti**.
  6. *Penyelesaian:* Pelanggan mengonfirmasi barang pengganti telah tiba, status retur menjadi `selesai`.
- **Pengembalian Dana (Refund):**
  1. Melalui tahapan verifikasi bukti yang sama dengan batas nominal pengembalian maksimal dihitung otomatis berdasarkan nilai riil barang yang diretur.
  2. Admin mentransfer dana via bank, mencatat nomor referensi mutasi, dan mengunggah bukti transfer sah.
  3. Pelanggan mengonfirmasi penerimaan transfer, status retur menjadi `selesai`.

---

## 6. Keamanan & Proteksi Berkas Bukti Privat

- Seluruh berkas bukti (foto resi J&T, bukti penerimaan pelanggan, video unboxing, foto cacat produk, bukti transfer refund) disimpan di luar direktori publik (`storage/app/secure_proofs/`).
- Disajikan melalui `SecureFileController` (`GET /secure-proof/{type}/{id}/{filename}`) dengan mitigasi *Path Traversal* (`basename()`) dan verifikasi kepemilikan ketat: hanya Administrator dan Customer pemilik sah pesanan yang diizinkan mengakses berkas. Tamu atau pengguna lain menerima respons `401 Unauthorized` atau `403 Forbidden`.
- Halaman publik pelacakan pesanan (`/lacak-pesanan`) memproteksi data sensitif dengan mewajibkan verifikasi ganda (Nomor Pesanan + Nomor HP Pemesan) serta menyembunyikan alamat lengkap dan bukti foto privat.

---

## 7. Hasil Verifikasi & Pengujian Otomatis

Rangkaian automated test dijalankan melalui PHP 8.3 CLI:
- **Rangkaian Uji Fitur Tracking & Retur:** `tests/Feature/OrderTrackingAndReturnManagementTest.php` (29 pengujian, 106 assertions) — **100% Passed**.
- **Seluruh Rangkaian Repositori:**
  - Total Pengujian: **198 tests**
  - Total Assertion: **740 assertions**
  - Status: **PASSED (100% Success)**
  - Durasi Eksekusi: ~10 detik
