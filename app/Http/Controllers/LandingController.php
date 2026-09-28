<?php

namespace App\Http\Controllers;

use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\PenilaianPpl;
use App\Models\User;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $stats = [
            'total_mahasiswa' => AnggotaKelompok::count(),
            'total_kelompok' => KelompokPpl::count(),
            'total_mitra' => Mitra::count(),
            'total_dpl' => User::where('role', 'dpl')->count(),
            'total_penilaian' => PenilaianPpl::count(),
            'total_locked' => PenilaianPpl::where('status', 'locked')->count(),
        ];

        return view('landing', compact('stats'));
    }
}
