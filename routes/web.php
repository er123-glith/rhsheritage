<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\OrderController;
use App\Http\Controllers\Public\PartnershipController;
use App\Http\Controllers\Public\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes — Halaman yang bisa diakses tamu (tanpa login)
|--------------------------------------------------------------------------
*/

// Beranda
Route::get('/', [HomeController::class, 'index'])->name('home');

// Katalog Produk
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/kategori/{category:slug}', [CatalogController::class, 'category'])->name('catalog.category');

// Detail Produk
Route::get('/produk/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// Konfirmasi & Lacak Pesanan
Route::get('/pesanan/{orderNumber}', [OrderController::class, 'confirmation'])->name('order.confirmation');
Route::get('/pesanan/{orderNumber}/invoice', [OrderController::class, 'invoice'])->name('order.invoice');
Route::get('/lacak-pesanan', [OrderController::class, 'trackForm'])->name('order.track');
Route::post('/lacak-pesanan', [OrderController::class, 'track'])
    ->middleware('throttle:10,1')
    ->name('order.track.submit');

// Grosir & Mitra
Route::get('/grosir-mitra', [PartnershipController::class, 'index'])->name('partnership.index');
Route::post('/grosir-mitra', [PartnershipController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('partnership.store');

// Tentang Kami
Route::get('/tentang', [AboutController::class, 'index'])->name('about');

/*
|--------------------------------------------------------------------------
| Fitur Transaksi — Terproteksi Autentikasi (Section D & G)
|--------------------------------------------------------------------------
*/

// Keranjang Belanja — Proteksi khusus redirect dengan notifikasi ke login
Route::middleware('cart.auth')->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/keranjang/{variantId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{variantId}', [CartController::class, 'destroy'])->name('cart.destroy');
});

// Checkout — Wajib autentikasi pengguna
Route::middleware(['auth', 'cart.auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/ongkir', [CheckoutController::class, 'shipping'])->name('checkout.shipping');
    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('checkout.store');
});

/*
|--------------------------------------------------------------------------
| Fitur Akun Customer & Profil (Section C.2 & E)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pesanan', [\App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders');
    Route::get('/pesanan/{orderNumber}', [\App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');
    Route::get('/pesanan/{orderNumber}/invoice', [\App\Http\Controllers\Customer\OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('/pesanan/{orderNumber}/konfirmasi-terima', [\App\Http\Controllers\Customer\OrderController::class, 'confirmDelivery'])->name('orders.confirm-delivery');

    // Manajemen Retur Customer (RMS)
    Route::get('/retur', [\App\Http\Controllers\Customer\ReturnController::class, 'index'])->name('returns.index');
    Route::get('/retur/ajukan/{orderNumber}', [\App\Http\Controllers\Customer\ReturnController::class, 'create'])->name('returns.create');
    Route::post('/retur', [\App\Http\Controllers\Customer\ReturnController::class, 'store'])->name('returns.store');
    Route::get('/retur/{returnNumber}', [\App\Http\Controllers\Customer\ReturnController::class, 'show'])->name('returns.show');
    Route::post('/retur/{returnNumber}/kirim-resi', [\App\Http\Controllers\Customer\ReturnController::class, 'shipReturn'])->name('returns.ship');
    Route::post('/retur/{returnNumber}/konfirmasi-pengganti', [\App\Http\Controllers\Customer\ReturnController::class, 'confirmReplacement'])->name('returns.confirm-replacement');
    Route::post('/retur/{returnNumber}/konfirmasi-refund', [\App\Http\Controllers\Customer\ReturnController::class, 'confirmRefund'])->name('returns.confirm-refund');

    // Ulasan Toko & Pengalaman Pelanggan
    Route::get('/pesanan/{orderNumber}/ulasan', [\App\Http\Controllers\Customer\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/pesanan/{orderNumber}/ulasan', [\App\Http\Controllers\Customer\ReviewController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('reviews.store');
});

// Endpoint unduh / lihat berkas bukti privat berotorisasi
Route::middleware('auth')->get('/secure-proof/{type}/{id}/{filename}', [\App\Http\Controllers\SecureFileController::class, 'show'])->name('secure.proof');

Route::middleware('auth')->group(function () {
    Route::get('/pesanan-saya', fn () => redirect()->route('customer.orders'));
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil');
});

// Dashboard redirect berdasarkan role
Route::get('/dashboard', function () {
    return auth()->user()?->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('customer.dashboard');
})->middleware(['auth'])->name('dashboard');

// Helper route migrasi cPanel (bisa dihapus setelah setup selesai)
Route::get('/setup-cpanel-migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh --seed --force');
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        try {
            \Illuminate\Support\Facades\Artisan::call('storage:link');
            $storageOutput = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $storageOutput = 'Storage link notice: ' . $e->getMessage();
        }

        return '<pre style="background:#111;color:#0f0;padding:20px;font-family:monospace;font-size:14px;border-radius:8px;">'
            . "=== HASIL MIGRASI & SEEDER ===\n\n"
            . htmlspecialchars($migrateOutput)
            . "\n\n=== STORAGE LINK ===\n\n"
            . htmlspecialchars($storageOutput)
            . '</pre>';
    } catch (\Throwable $e) {
        return '<pre style="background:#111;color:#f33;padding:20px;font-family:monospace;font-size:14px;border-radius:8px;">'
            . "=== ERROR MIGRASI ===\n\n"
            . htmlspecialchars($e->getMessage()) . "\n\n"
            . htmlspecialchars($e->getTraceAsString())
            . '</pre>';
    }
});

// Rute Autentikasi Laravel Breeze
require __DIR__.'/auth.php';
