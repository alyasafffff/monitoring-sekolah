<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Cek apakah role user sesuai dengan yang diminta rute
        // Contoh: role:admin -> $role = 'admin'
        if (Auth::user()->role !== $role) {
            // Jika role user bukan admin, tampilkan halaman 403 (Forbidden)
            abort(403, 'Akses Ditolak. Anda bukan ' . ucfirst($role));
        }

        return $next($request);
    }
}