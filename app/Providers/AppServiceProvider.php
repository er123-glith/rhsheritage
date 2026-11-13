<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // CartService sebagai singleton agar state sesi konsisten per request
        $this->app->singleton(CartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pastikan zona waktu PHP dan Carbon terkonfigurasi ke Asia/Jakarta
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));
        \Carbon\Carbon::setLocale(config('app.locale', 'id'));

        // Deteksi N+1 query di lingkungan lokal
        Model::preventLazyLoading(app()->isLocal());

        // Bagikan jumlah keranjang ke semua view publik
        View::composer('*', function ($view) {
            if (request()->is('admin*')) {
                return;
            }
            try {
                $cartCount = app(CartService::class)->count();
            } catch (\Throwable) {
                $cartCount = 0;
            }
            $view->with('cartCount', $cartCount);
        });
    }
}
