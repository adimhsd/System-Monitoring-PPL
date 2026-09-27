<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DplExport;
use App\Exports\DplTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\DplImport;
use App\Models\KelompokPpl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class DplController extends Controller
{
    /**
     * Tampilkan Daftar Data DPL (Dosen Pembimbing Lapangan).
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'dpl');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip_nidn', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $dplList = $query->latest()->paginate(20)->withQueryString();

        // Calculate total bimbingan mahasiswa per DPL
        foreach ($dplList as $dpl) {
            $totalMhs = KelompokPpl::where('dpl_id', $dpl->id)
                ->where('status', 'aktif')
                ->withCount('anggota')
                ->get()
                ->sum('anggota_count');

            $dpl->total_bimbingan_mhs = $totalMhs;
        }

        // Ringkasan Statistik Data DPL
        $totalDpl = User::where('role', 'dpl')->count();
        $totalBimbinganMhs = KelompokPpl::where('status', 'aktif')->withCount('anggota')->get()->sum('anggota_count');
        $avgBimbingan = $totalDpl > 0 ? round($totalBimbinganMhs / $totalDpl, 1) : 0;

        $statsSummary = [
            'total_dpl' => $totalDpl,
            'dpl_aktif' => User::where('role', 'dpl')->where('is_active', true)->count(),
            'dpl_nonaktif' => User::where('role', 'dpl')->where('is_active', false)->count(),
            'dpl_assigned' => User::where('role', 'dpl')->whereHas('kelompokDpl')->count(),
            'dpl_standby' => User::where('role', 'dpl')->doesntHave('kelompokDpl')->count(),
            'total_bimbingan_mhs' => $totalBimbinganMhs,
            'avg_bimbingan_per_dpl' => $avgBimbingan,
            'dpl_dengan_nip' => User::where('role', 'dpl')->whereNotNull('nip_nidn')->where('nip_nidn', '!=', '-')->where('nip_nidn', '!=', '')->count(),
            'dpl_dengan_wa' => User::where('role', 'dpl')->whereNotNull('no_hp')->where('no_hp', '!=', '-')->where('no_hp', '!=', '')->count(),
            'dpl_dengan_email' => User::where('role', 'dpl')->whereNotNull('email')->where('email', '!=', '-')->where('email', '!=', '')->count(),
        ];

        return view('admin.dpl.index', compact('dplList', 'statsSummary'));
    }

    /**
     * Form Tambah Data DPL Baru.
     */
    public function create()
    {
        return view('admin.dpl.create');
    }

    /**
     * Simpan Data DPL Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nip_nidn' => ['nullable', 'string', 'max:30'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'dpl',
            'nama_lengkap' => $request->nama_lengkap,
            'nip_nidn' => $request->nip_nidn,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'must_change_password' => true,
            'is_active' => true,
        ]);

        return redirect()->route('admin.dpl.index')
            ->with('success', 'Data DPL baru berhasil ditambahkan.');
    }

    /**
     * Form Edit Data DPL.
     */
    public function edit(User $dpl)
    {
        if ($dpl->role !== 'dpl') {
            abort(404);
        }

        return view('admin.dpl.edit', compact('dpl'));
    }

    /**
     * Update Data DPL.
     */
    public function update(Request $request, User $dpl)
    {
        if ($dpl->role !== 'dpl') {
            abort(404);
        }

        $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($dpl->id)],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nip_nidn' => ['nullable', 'string', 'max:30'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ]);

        $payload = [
            'username' => $request->username,
            'nama_lengkap' => $request->nama_lengkap,
            'nip_nidn' => $request->nip_nidn,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->password);
        }

        $dpl->update($payload);

        return redirect()->route('admin.dpl.index')
            ->with('success', 'Data DPL berhasil diperbarui.');
    }

    /**
     * Hapus Data DPL.
     */
    public function destroy(User $dpl)
    {
        if ($dpl->role !== 'dpl') {
            abort(404);
        }

        $dpl->delete();

        return redirect()->route('admin.dpl.index')
            ->with('success', 'Data DPL berhasil dihapus.');
    }

    /**
     * Export Excel Data DPL.
     */
    public function exportExcel()
    {
        return Excel::download(new DplExport, 'Master_Data_DPL_PPL.xlsx');
    }

    /**
     * Download Template Excel Import DPL.
     */
    public function downloadTemplate()
    {
        return Excel::download(new DplTemplateExport, 'Template_Import_DPL_PPL.xlsx');
    }

    /**
     * Import Excel Data DPL.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'file_excel.max' => 'Ukuran file Excel maksimal 5MB.',
        ]);

        try {
            Excel::import(new DplImport, $request->file('file_excel'));

            return redirect()->route('admin.dpl.index')
                ->with('success', 'Data Dosen Pembimbing (DPL) dari file Excel berhasil diimpor ke sistem.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.dpl.index')
                ->with('error', 'Gagal mengimpor file Excel DPL: ' . $e->getMessage());
        }
    }
}
