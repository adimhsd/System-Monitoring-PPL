<?php

namespace App\Http\Controllers\PicMitra;

use App\Http\Controllers\Controller;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\PenilaianPpl;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    /**
     * Tampilkan Daftar Kelompok & Mahasiswa untuk Penilaian PIC Mitra.
     */
    public function index()
    {
        $mitra = Mitra::where('pic_user_id', Auth::id())->first();

        $kelompokList = $mitra
            ? KelompokPpl::with(['anggota.penilaian', 'dpl', 'ketua'])
                ->where('mitra_id', $mitra->id)
                ->where('status', 'aktif')
                ->get()
            : collect();

        $skalaHuruf = PenilaianPpl::skalaNilaiHuruf();

        return view('pic.penilaian.index', compact('kelompokList', 'skalaHuruf'));
    }

    /**
     * Simpan/Update Nilai Mitra per mahasiswa (Bobot 60%).
     * Mahasiswa yang nilainya sudah dikunci dilewati.
     */
    public function storeOrUpdate(Request $request, KelompokPpl $kelompok)
    {
        if ($kelompok->mitra?->pic_user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menilai kelompok ini.');
        }

        $request->validate([
            'nilai' => ['required', 'array'],
            'nilai.*.nilai_mitra' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai.*.catatan_mitra' => ['nullable', 'string', 'max:1000'],
        ], [
            'nilai.*.nilai_mitra.numeric' => 'Nilai Mitra harus berupa angka (0-100).',
            'nilai.*.nilai_mitra.min' => 'Nilai Mitra minimal 0.',
            'nilai.*.nilai_mitra.max' => 'Nilai Mitra maksimal 100.',
        ]);

        $anggota = $kelompok->anggota()->with('penilaian')->get()->keyBy('id');
        $pic = Auth::user();
        $tersimpan = 0;
        $dilewati = 0;

        foreach ($request->nilai as $anggotaId => $data) {
            $mhs = $anggota->get((int) $anggotaId);

            if (! $mhs || ($data['nilai_mitra'] ?? null) === null) {
                continue;
            }

            if ($mhs->penilaian?->isLocked()) {
                $dilewati++;
                continue;
            }

            PenilaianService::simpan($mhs, [
                'nilai_mitra' => $data['nilai_mitra'],
                'catatan_mitra' => $data['catatan_mitra'] ?? null,
            ], $pic);
            $tersimpan++;
        }

        $pesan = "Nilai Mitra (60%) untuk {$tersimpan} mahasiswa {$kelompok->nama_kelompok} berhasil disimpan.";
        if ($dilewati > 0) {
            $pesan .= " {$dilewati} mahasiswa dilewati karena nilainya sudah dikunci.";
        }

        return redirect()->route('pic.penilaian.index')->with('success', $pesan);
    }
}
