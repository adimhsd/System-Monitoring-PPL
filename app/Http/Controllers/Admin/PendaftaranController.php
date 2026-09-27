<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranDpl;
use App\Models\PendaftaranMahasiswa;
use App\Services\PendaftaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Verifikasi data formulir pendaftaran Mahasiswa PPL & DPL.
 */
class PendaftaranController extends Controller
{
    public function mahasiswa(Request $request)
    {
        $query = PendaftaranMahasiswa::with('anggota.kelompok');

        foreach (['status', 'jenis_ppl', 'prodi'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%"));
        }

        $pendaftaranList = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'total' => PendaftaranMahasiswa::count(),
            'menunggu' => PendaftaranMahasiswa::where('status', 'menunggu')->count(),
            'diterima' => PendaftaranMahasiswa::where('status', 'diterima')->count(),
            'ditolak' => PendaftaranMahasiswa::where('status', 'ditolak')->count(),
            'reguler' => PendaftaranMahasiswa::where('jenis_ppl', 'reguler')->where('status', '!=', 'ditolak')->count(),
            'mbkm' => PendaftaranMahasiswa::where('jenis_ppl', 'mbkm')->where('status', '!=', 'ditolak')->count(),
        ];

        $dibuka = PendaftaranService::dibuka('mahasiswa');

        return view('admin.pendaftaran.mahasiswa', compact('pendaftaranList', 'stats', 'dibuka'));
    }

    public function dpl(Request $request)
    {
        $query = PendaftaranDpl::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nip_nidn', 'like', "%{$search}%"));
        }

        $pendaftaranList = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'total' => PendaftaranDpl::count(),
            'menunggu' => PendaftaranDpl::where('status', 'menunggu')->count(),
            'diterima' => PendaftaranDpl::where('status', 'diterima')->count(),
            'ditolak' => PendaftaranDpl::where('status', 'ditolak')->count(),
        ];

        $dibuka = PendaftaranService::dibuka('dpl');

        return view('admin.pendaftaran.dpl', compact('pendaftaranList', 'stats', 'dibuka'));
    }

    public function fileMahasiswa(PendaftaranMahasiswa $pendaftaran)
    {
        return PendaftaranService::tampilkanFile(
            $pendaftaran->file_bukti_pembayaran,
            'Bukti_Pembayaran_' . $pendaftaran->nim . '.pdf'
        );
    }

    public function fileDpl(PendaftaranDpl $pendaftaran)
    {
        return PendaftaranService::tampilkanFile(
            $pendaftaran->file_surat_kesanggupan,
            'Surat_Kesanggupan_DPL_' . preg_replace('/\W/', '', $pendaftaran->nip_nidn) . '.pdf'
        );
    }

    public function terimaMahasiswa(PendaftaranMahasiswa $pendaftaran)
    {
        PendaftaranService::terimaMahasiswa($pendaftaran, Auth::user());

        return back()->with('success', "Pendaftaran {$pendaftaran->nama} diterima dan masuk ke Data Mahasiswa (siap diplot ke kelompok).");
    }

    public function bulkTerimaMahasiswa(Request $request)
    {
        $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']], [
            'ids.required' => 'Pilih minimal satu pendaftar terlebih dahulu.',
        ]);

        $admin = Auth::user();
        $count = 0;

        PendaftaranMahasiswa::whereIn('id', $request->ids)->where('status', 'menunggu')->each(function ($p) use ($admin, &$count) {
            PendaftaranService::terimaMahasiswa($p, $admin);
            $count++;
        });

        return back()->with('success', "{$count} pendaftaran mahasiswa diterima dan masuk ke Data Mahasiswa.");
    }

    public function tolakMahasiswa(Request $request, PendaftaranMahasiswa $pendaftaran)
    {
        $request->validate(['catatan_admin' => ['nullable', 'string', 'max:500']]);
        PendaftaranService::tolak($pendaftaran, Auth::user(), $request->catatan_admin);

        return back()->with('success', "Pendaftaran {$pendaftaran->nama} ditolak. Mahasiswa dapat mendaftar ulang dengan NIM yang sama.");
    }

    public function terimaDpl(PendaftaranDpl $pendaftaran)
    {
        $user = PendaftaranService::terimaDpl($pendaftaran, Auth::user());

        return back()->with('success', "Pendaftaran {$pendaftaran->nama_lengkap} diterima. Akun DPL: username {$user->username}"
            . ($user->wasRecentlyCreated ? ', password awal ' . PendaftaranService::PASSWORD_DEFAULT_DPL . '.' : ' (akun lama diperbarui).'));
    }

    public function tolakDpl(Request $request, PendaftaranDpl $pendaftaran)
    {
        $request->validate(['catatan_admin' => ['nullable', 'string', 'max:500']]);
        PendaftaranService::tolak($pendaftaran, Auth::user(), $request->catatan_admin);

        return back()->with('success', "Pendaftaran {$pendaftaran->nama_lengkap} ditolak.");
    }

    public function toggle(Request $request, string $jenis)
    {
        abort_unless(in_array($jenis, ['mahasiswa', 'dpl'], true), 404);
        $request->validate(['dibuka' => ['required', Rule::in(['0', '1'])]]);

        PendaftaranService::setDibuka($jenis, $request->dibuka === '1');

        $label = $jenis === 'dpl' ? 'DPL' : 'Mahasiswa PPL';

        return back()->with('success', "Formulir pendaftaran {$label} " . ($request->dibuka === '1' ? 'dibuka.' : 'ditutup.'));
    }
}
