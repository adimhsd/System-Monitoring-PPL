<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! in_array($request->user()->role, $roles)) {
            // Redirect user to their role-specific dashboard if accessing unauthorized route
            return match ($request->user()->role) {
                'admin' => redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Anda telah diarahkan ke Dashboard Admin.'),
                'dpl' => redirect()->route('dpl.dashboard')->with('error', 'Akses ditolak. Anda telah diarahkan ke Dashboard DPL.'),
                'pic_mitra' => redirect()->route('pic.dashboard')->with('error', 'Akses ditolak. Anda telah diarahkan ke Dashboard PIC Mitra.'),
                'ketua_kelompok' => redirect()->route('ketua.dashboard')->with('error', 'Akses ditolak. Anda telah diarahkan ke Dashboard Ketua Kelompok.'),
                default => abort(403, 'Akses tidak diizinkan untuk role ini.'),
            };
        }

        return $next($request);
    }
}
