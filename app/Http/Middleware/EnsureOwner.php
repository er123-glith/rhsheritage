<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user && ($user->isAdmin() || $user->is_owner), 403, 'Akses ditolak. Halaman ini khusus untuk Administrator RHS HERITAGE.');

        return $next($request);
    }
}
