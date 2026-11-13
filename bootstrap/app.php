<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\EnsureAccountIsActive::class,
        ]);

        $middleware->alias([
            'verified.owner' => \App\Http\Middleware\EnsureOwner::class,
            'role'           => \App\Http\Middleware\EnsureRole::class,
            'cart.auth'      => \App\Http\Middleware\EnsureAuthenticatedForCart::class,
            'account.active' => \App\Http\Middleware\EnsureAccountIsActive::class,
        ]);

        $middleware->redirectTo(
            guests: fn (\Illuminate\Http\Request $request) => $request->is('admin*') ? '/admin/login' : '/login',
            users: fn (\Illuminate\Http\Request $request) => $request->user()?->isAdmin() ? '/admin' : '/',
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Penanganan PostTooLargeException & HTTP 413 secara ramah pengguna (RHS Heritage)
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, Request $request) {
            $userMessage = 'Ukuran file terlalu besar. Silakan kompres atau pilih file yang lebih kecil, kemudian coba kembali.';

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'message' => $userMessage,
                    'error'   => 'post_too_large',
                ], 413);
            }

            if ($request->hasSession()) {
                return back()->withInput()->with('error', $userMessage);
            }

            return response()->view('errors.413', [
                'message' => $userMessage,
            ], 413);
        });
    })->create();
