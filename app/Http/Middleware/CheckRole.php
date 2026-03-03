<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Jika user yang sedang login role-nya TIDAK SAMA dengan yang diminta, TENDANG!
        if (auth()->user()->role !== $role) {
            abort(403, 'AKSES DITOLAK! Halaman ini khusus untuk '.strtoupper($role).'.');
        }

        // Jika sesuai, silakan masuk
        return $next($request);
    }
}
