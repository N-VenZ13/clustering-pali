<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Jika user belum login, atau role-nya tidak sesuai dengan yang diizinkan
        if (!auth()->check() || auth()->user()->role !== $role) {
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        return $next($request);
    }
}
