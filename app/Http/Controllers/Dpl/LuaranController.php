<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\KelompokPpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LuaranController extends Controller
{
    /**
     * Tampilkan Daftar Luaran Akhir Kelompok Bimbingan DPL.
     */
    public function index()
    {
        $dpl = Auth::user();

        $kelompokList = KelompokPpl::with(['mitra', 'ketua', 'luaran'])
            ->where('dpl_id', $dpl->id)
            ->get();

        return view('dpl.luaran.index', compact('kelompokList'));
    }
}
