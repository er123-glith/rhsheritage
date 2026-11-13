<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PartnerApplicationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ShippingZoneController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    // Logout route — mendukung POST (standard form UI dengan CSRF) dan GET (graceful fallback browser / URL langsung)
    Route::match(['get', 'post'], '/logout', [AuthenticatedSessionController::class, 'destroy'])->name('admin.logout');

    // Guest routes — halaman login tunggal untuk admin dan customer
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    });

    // Authenticated Admin routes
    Route::middleware(['auth', 'verified.owner'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Produk & Galeri Foto
        Route::resource('produk', ProductController::class)
            ->parameters(['produk' => 'product'])
            ->names('admin.products')
            ->except(['show']);

        Route::post('/produk/{product}/foto', [ProductImageController::class, 'store'])
            ->name('admin.products.images.store');
        Route::delete('/produk/{product}/foto/{image}', [ProductImageController::class, 'destroy'])
            ->name('admin.products.images.destroy');

        // Varian Produk
        Route::post('/produk/{product}/varian', [VariantController::class, 'store'])
            ->name('admin.variants.store');
        Route::patch('/produk/{product}/varian/{variant}', [VariantController::class, 'update'])
            ->name('admin.variants.update');
        Route::delete('/produk/{product}/varian/{variant}', [VariantController::class, 'destroy'])
            ->name('admin.variants.destroy');

        // Kategori Produk
        Route::resource('kategori', CategoryController::class)
            ->parameters(['kategori' => 'category'])
            ->names('admin.categories')
            ->except(['show']);

        // Kelola Stok Inventaris
        Route::get('/stok', [StockController::class, 'index'])->name('admin.stock.index');
        Route::patch('/stok/{variant}', [StockController::class, 'update'])->name('admin.stock.update');
        Route::patch('/stok/{variant}/quick', [StockController::class, 'quickAdjust'])->name('admin.stock.quick-adjust');

        // Pesanan Masuk
        Route::get('/pesanan', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/pesanan/{order}/konfirmasi', [OrderController::class, 'confirm'])->name('admin.orders.confirm');
        Route::post('/pesanan/{order}/batal', [OrderController::class, 'cancel'])->name('admin.orders.cancel');
        Route::post('/pesanan/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');

        // Manajemen Pelanggan (Customer)
        Route::get('/pelanggan', [CustomerController::class, 'index'])->name('admin.customers.index');
        Route::get('/pelanggan/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');

        // Grosir & Mitra
        Route::get('/grosir-mitra', [PartnerApplicationController::class, 'index'])->name('admin.partners.index');
        Route::patch('/grosir-mitra/{partner}/toggle-followup', [PartnerApplicationController::class, 'toggleFollowup'])->name('admin.partners.toggle-followup');
        Route::delete('/grosir-mitra/{partner}', [PartnerApplicationController::class, 'destroy'])->name('admin.partners.destroy');

        // Zona Ongkir
        Route::resource('ongkir', ShippingZoneController::class)
            ->parameters(['ongkir' => 'shipping_zone'])
            ->names('admin.shipping')
            ->except(['show']);
        Route::patch('/ongkir/{shipping_zone}/toggle-active', [ShippingZoneController::class, 'toggleActive'])->name('admin.shipping.toggle-active');
    });
});
