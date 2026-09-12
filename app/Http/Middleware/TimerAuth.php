<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan facade Auth
use Symfony\Component\HttpFoundation\Response;

class TimerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah user sudah login atau belum
        if (!Auth::check()) {
            // Jika belum login, redirect ke halaman login Filament
            return redirect('/penilaian');
        }

        // Jika sudah login, lanjutkan request
        return $next($request);
    }
}
