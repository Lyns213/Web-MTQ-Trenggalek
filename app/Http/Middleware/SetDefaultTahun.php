<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tahun;

class SetDefaultTahun
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has("selected_tahun_id")) {
            $tahunAktif = Tahun::where("is_active", true)->first();
            if ($tahunAktif) {
                session(["selected_tahun_id" => $tahunAktif->id]);
            }
        }

        return $next($request);
    }
}
