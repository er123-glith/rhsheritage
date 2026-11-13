<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticatedForCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'authenticated' => false,
                    'message' => 'Silakan login atau daftar terlebih dahulu untuk menambahkan produk ke keranjang.',
                    'redirect' => route('login'),
                ], 401);
            }

            // Simpan URL produk sebelumnya agar setelah login customer dikembalikan ke halaman produk tersebut
            $previousUrl = url()->previous();
            if ($request->isMethod('post') && $previousUrl && $previousUrl !== url()->current()) {
                session()->put('url.intended', $previousUrl);
            } else {
                session()->put('url.intended', $request->fullUrl());
            }

            return redirect()->route('login')->with(
                'cart_auth_notice',
                'Silakan login atau daftar terlebih dahulu untuk menambahkan produk ke keranjang.'
            );
        }

        return $next($request);
    }
}

