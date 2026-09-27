<?php

namespace App\Http\Middleware;

use App\Models\KelompokPpl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kelompok MBKM tidak menggunakan logbook harian (kegiatan & monitoring
 * dilakukan di luar kampus dengan mekanisme berbeda).
 */
class BlockMbkmLogbook
{
    public function handle(Request $request, Closure $next): Response
    {
        $kelompok = KelompokPpl::where('ketua_user_id', $request->user()?->id)->first();

        if ($kelompok?->isMbkm()) {
            return redirect()->route('ketua.dashboard')
                ->with('error', 'Kelompok MBKM tidak menggunakan logbook harian.');
        }

        return $next($request);
    }
}
