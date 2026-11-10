<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->is_owner, 403, 'Akses ditolak. Halaman ini khusus untuk Owner RHS HERITAGE.');

        return $next($request);
    }
}
