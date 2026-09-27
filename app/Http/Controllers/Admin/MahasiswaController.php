<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MahasiswaExport;
use App\Exports\MahasiswaTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\MahasiswaImport;
use App\Models\KelompokPpl;
use App\Models\Mahasiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class MahasiswaController extends Controller
{
    /**
     * Tampilkan Daftar Master Data Mahasiswa.
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::with('kelompok');

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        if ($request->filled('kelompok_status')) {
            if ($request->kelompok_status === 'assigned') {
                $query->whereNotNull('kelompok_id');
            } elseif ($request->kelompok_status === 'unassigned') {
                $query->whereNull('kelompok_id');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('konsentrasi', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $mahasiswaList = $query->latest()->paginate(20)->withQueryString();
        $kelompokList = KelompokPpl::all();

        // Ringkasan Statistik Data Mahasiswa
        $statsSummary = [
            'total_mahasiswa' => Mahasiswa::count(),
            'laki_laki' => Mahasiswa::where('jenis_kelamin', 'Laki-laki')->count(),
            'perempuan' => Mahasiswa::where('jenis_kelamin', 'Perempuan')->count(),
            'assigned' => Mahasiswa::whereNotNull('kelompok_id')->count(),
            'unassigned' => Mahasiswa::whereNull('kelompok_id')->count(),
            'prodi_manajemen' => Mahasiswa::where('prodi', 'Manajemen')->count(),
            'prodi_akuntansi' => Mahasiswa::where('prodi', 'Akuntansi')->count(),
            'prodi_bisnis_digital' => Mahasiswa::where('prodi', 'Bisnis Digital')->count(),
            'memiliki_hp' => Mahasiswa::whereNotNull('no_hp')->where('no_hp', '!=', '')->count(),
            'tanpa_hp' => Mahasiswa::where(function ($q) {
                $q->whereNull('no_hp')->orWhere('no_hp', '');
            })->count(),
        ];

        return view('admin.mahasiswa.index', compact('mahasiswaList', 'kelompokList', 'statsSummary'));
    }

    /**
     * Form Tambah Mahasiswa Baru Manual.
     */
    public function create()
    {
        $kelompokList = KelompokPpl::where('status', 'aktif')->get();

        return view('admin.mahasiswa.create', compact('kelompokList'));
    }

    /**
     * Simpan Data Mahasiswa Baru Manual.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nim' => ['required', 'string', 'max:20', 'unique:anggota_kelompok,nim'],
            'nama' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'prodi' => ['required', Rule::in(['Manajemen', 'Akuntansi', 'Bisnis Digital'])],
            'konsentrasi' => ['nullable', Rule::in(['Pemasaran', 'Operasional', 'Keuangan', 'SDM', 'Akuntansi', 'Bisnis Digital'])],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'kelompok_id' => ['nullable', 'exists:kelompok_ppl,id'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM ini sudah terdaftar dalam sistem.',
            'nama.required' => 'Nama mahasiswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'prodi.required' => 'Program studi wajib dipilih.',
        ]);

        Mahasiswa::create([
            'nim' => $request->nim,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'prodi' => $request->prodi,
            'konsentrasi' => $request->konsentrasi,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'kelompok_id' => $request->kelompok_id,
        ]);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Data Mahasiswa baru berhasil ditambahkan.');
    }

    /**
     * Form Edit Data Mahasiswa.
     */
    public function edit(Mahasiswa $mahasiswa)
    {
        $kelompokList = KelompokPpl::where('status', 'aktif')->get();

        return view('admin.mahasiswa.edit', compact('mahasiswa', 'kelompokList'));
    }

    /**
     * Update Data Mahasiswa.
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'nim' => ['required', 'string', 'max:20', Rule::unique('anggota_kelompok', 'nim')->ignore($mahasiswa->id)],
            'nama' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'prodi' => ['required', Rule::in(['Manajemen', 'Akuntansi', 'Bisnis Digital'])],
            'konsentrasi' => ['nullable', Rule::in(['Pemasaran', 'Operasional', 'Keuangan', 'SDM', 'Akuntansi', 'Bisnis Digital'])],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'kelompok_id' => ['nullable', 'exists:kelompok_ppl,id'],
        ]);

        $mahasiswa->update([
            'nim' => $request->nim,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'prodi' => $request->prodi,
            'konsentrasi' => $request->konsentrasi,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'kelompok_id' => $request->kelompok_id,
        ]);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Data Mahasiswa berhasil diperbarui.');
    }

    /**
     * Hapus Data Mahasiswa.
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Data Mahasiswa berhasil dihapus.');
    }

    /**
     * Export Excel Data Mahasiswa.
     */
    public function exportExcel()
    {
        return Excel::download(new MahasiswaExport, 'Master_Data_Mahasiswa_PPL.xlsx');
    }

    /**
     * Download Template Excel Import Mahasiswa.
     */
    public function downloadTemplate()
    {
        return Excel::download(new MahasiswaTemplateExport, 'Template_Import_Mahasiswa_PPL.xlsx');
    }

    /**
     * Import Excel Data Mahasiswa.
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
            Excel::import(new MahasiswaImport, $request->file('file_excel'));

            return redirect()->route('admin.mahasiswa.index')
                ->with('success', 'Data Mahasiswa dari file Excel berhasil diimpor ke sistem.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.mahasiswa.index')
                ->with('error', 'Gagal mengimpor file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Cetak PDF Laporan Master Data Mahasiswa PPL (Landscape).
     */
    public function exportPdf(Request $request)
    {
        $query = Mahasiswa::with('kelompok');

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        if ($request->filled('kelompok_status')) {
            if ($request->kelompok_status === 'assigned') {
                $query->whereNotNull('kelompok_id');
            } elseif ($request->kelompok_status === 'unassigned') {
                $query->whereNull('kelompok_id');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('konsentrasi', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $mahasiswaList = $query->latest()->get();

        $totalMahasiswa = $mahasiswaList->count();
        $totalAssigned = $mahasiswaList->whereNotNull('kelompok_id')->count();
        $totalUnassigned = $mahasiswaList->whereNull('kelompok_id')->count();

        // Base64 Logo UNIKU
        $logoUnikuBase64 = null;
        $logoPath = public_path('images/logo-uniku.png');
        if (File::exists($logoPath)) {
            $logoUnikuBase64 = 'data:image/png;base64,' . base64_encode(File::get($logoPath));
        }

        $pdf = Pdf::loadView('pdf.laporan-mahasiswa', compact('mahasiswaList', 'totalMahasiswa', 'totalAssigned', 'totalUnassigned', 'logoUnikuBase64'))
            ->setPaper('a4', 'landscape');

        $fileName = 'Laporan_Master_Data_Mahasiswa_PPL_[' . date('d-m-Y') . '].pdf';

        return $pdf->download($fileName);
    }
}
