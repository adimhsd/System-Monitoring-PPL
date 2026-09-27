<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelompok;
use App\Models\ConfigAplikasi;
use App\Models\KelompokPpl;
use App\Models\PenilaianPpl;
use App\Models\User;
use App\Services\PenilaianService;
use App\Services\RekapNilaiExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    private const FILTERS = ['search', 'prodi', 'kelompok_id', 'dpl_id', 'status', 'huruf'];

    /**
     * Input & Rekap Nilai PPL seluruh mahasiswa fakultas.
     */
    public function index(Request $request)
    {
        $admin = Auth::user();
        $filters = $request->only(self::FILTERS);

        $mahasiswaList = PenilaianService::queryMahasiswa($admin, $filters)
            ->orderBy('kelompok_id')
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        $statsSummary = PenilaianService::statistik(PenilaianService::queryMahasiswa($admin));
        $statsSummary['total_dpl'] = User::where('role', 'dpl')->count();
        $statsSummary['dpl_selesai'] = KelompokPpl::whereNotNull('dpl_id')
            ->whereHas('anggota.penilaian', fn ($q) => $q->whereNotNull('nilai_dpl'))
            ->distinct()
            ->count('dpl_id');

        $kelompokList = KelompokPpl::orderBy('nama_kelompok')->get(['id', 'nama_kelompok']);
        $dplList = User::where('role', 'dpl')->orderBy('nama_lengkap')->get(['id', 'nama_lengkap']);
        $skalaHuruf = PenilaianPpl::skalaNilaiHuruf();

        return view('admin.penilaian.index', compact(
            'mahasiswaList', 'kelompokList', 'dplList', 'skalaHuruf', 'statsSummary'
        ));
    }

    /**
     * Input / koreksi nilai oleh Admin (Nilai Mitra & Nilai Laporan DPL).
     */
    public function update(Request $request, AnggotaKelompok $mahasiswa)
    {
        $data = $request->validate([
            'nilai_mitra' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_dpl' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'catatan_mitra' => ['nullable', 'string', 'max:1000'],
            'catatan_dpl' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:draft,locked'],
        ]);

        $penilaian = PenilaianService::simpan($mahasiswa, $data, Auth::user());

        return back()->with('success', $this->pesanTersimpan($mahasiswa, $penilaian));
    }

    public function lock(AnggotaKelompok $mahasiswa)
    {
        if (! PenilaianService::kunci($mahasiswa, Auth::user())) {
            return back()->with('error', "Nilai {$mahasiswa->nama} belum lengkap atau sudah terkunci.");
        }

        return back()->with('success', "Nilai {$mahasiswa->nama} berhasil dikunci (Final).");
    }

    public function unlock(AnggotaKelompok $mahasiswa)
    {
        if (! PenilaianService::bukaKunci($mahasiswa)) {
            return back()->with('error', "Nilai {$mahasiswa->nama} tidak dalam status terkunci.");
        }

        return back()->with('success', "Kunci nilai {$mahasiswa->nama} dibuka. DPL & PIC Mitra dapat merevisi kembali.");
    }

    /**
     * Aksi massal: kunci / buka kunci nilai mahasiswa terpilih.
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'aksi' => ['required', 'in:lock,unlock'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ], [
            'ids.required' => 'Pilih minimal satu mahasiswa terlebih dahulu.',
        ]);

        $admin = Auth::user();
        $count = 0;

        AnggotaKelompok::with('penilaian')->whereIn('id', $request->ids)->each(function ($mhs) use ($request, $admin, &$count) {
            $ok = $request->aksi === 'lock'
                ? PenilaianService::kunci($mhs, $admin)
                : PenilaianService::bukaKunci($mhs);
            $count += $ok ? 1 : 0;
        });

        $label = $request->aksi === 'lock' ? 'dikunci' : 'dibuka kuncinya';

        return back()->with('success', "{$count} nilai mahasiswa berhasil {$label}.");
    }

    public function export(Request $request)
    {
        $query = PenilaianService::queryMahasiswa(Auth::user(), $request->only(self::FILTERS));

        return RekapNilaiExportService::download($query, 'Seluruh Mahasiswa PPL');
    }

    /**
     * Update Konfigurasi Skala Nilai Huruf.
     */
    public function updateGradeScale(Request $request)
    {
        $request->validate([
            'skala' => ['required', 'array'],
            'skala.*.huruf' => ['required', 'string', 'max:5'],
            'skala.*.min' => ['required', 'numeric', 'min:0', 'max:100'],
            'skala.*.max' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        ConfigAplikasi::set('skala_nilai_huruf', array_values($request->skala));
        PenilaianService::hitungUlangHurufMutu();

        return redirect()->route('admin.penilaian.index')
            ->with('success', 'Konfigurasi skala nilai huruf berhasil diperbarui & diterapkan ulang.');
    }

    private function pesanTersimpan(AnggotaKelompok $mahasiswa, PenilaianPpl $p): string
    {
        if ($p->nilai_akhir === null) {
            return "Nilai {$mahasiswa->nama} tersimpan. Nilai Akhir akan terbentuk setelah Nilai Mitra & Nilai DPL lengkap.";
        }

        return "Nilai {$mahasiswa->nama} berhasil diperbarui (Akhir: {$p->nilai_akhir} / Grade: {$p->nilai_huruf}).";
    }
}
