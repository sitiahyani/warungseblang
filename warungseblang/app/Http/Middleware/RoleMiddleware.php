<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika belum login → arahkan ke login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Ambil role user
        $userRole = Auth::user()->role;

        // Jika role tidak sesuai → abort 403
        if (!in_array($userRole, $roles)) {
            abort(403, 'AKSES DITOLAK');
        }

        return $next($request);
    }
}