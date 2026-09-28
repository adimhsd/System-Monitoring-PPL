<?php

namespace App\Http\Middleware;

use App\Models\KelompokPpl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kelompok MBKM tidak menggunakan logbook harian maupun kunjungan monitoring DPL
 * (kegiatan dilaksanakan di luar kampus dengan mekanisme berbeda).
 */
class BlockMbkmFitur
{
    private const LABEL = [
        'logbook' => 'logbook harian',
        'kunjungan' => 'kunjungan monitoring DPL',
    ];

    public function handle(Request $request, Closure $next, string $fitur = 'logbook'): Response
    {
        $kelompok = KelompokPpl::where('ketua_user_id', $request->user()?->id)->first();

        if ($kelompok?->isMbkm()) {
            return redirect()->route('ketua.dashboard')
                ->with('error', 'Kelompok MBKM tidak menggunakan ' . (self::LABEL[$fitur] ?? $fitur) . '.');
        }

        return $next($request);
    }
}
