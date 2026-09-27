<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PlottingPplExport;
use App\Http\Controllers\Controller;
use App\Models\KelompokPpl;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class PlottingController extends Controller
{
    /**
     * Tampilkan Daftar Plotting Kelompok.
     */
    public function index(Request $request)
    {
        $query = KelompokPpl::with(['mitra', 'dpl', 'anggota', 'ketua']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelompok', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function ($mq) use ($search) {
                      $mq->where('nama_mitra', 'like', "%{$search}%");
                  })
                  ->orWhereHas('dpl', function ($dq) use ($search) {
                      $dq->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $plottingList = $query->latest()->paginate(20)->withQueryString();

        return view('admin.plotting.index', compact('plottingList'));
    }

    /**
     * Form Tambah Plotting Baru.
     */
    public function create()
    {
        $kelompokList = KelompokPpl::where('status', 'aktif')->get();
        $mitraList = Mitra::orderBy('nama_mitra')->get();
        $dplList = User::where('role', 'dpl')->where('is_active', true)->get();
        $unassignedMahasiswa = Mahasiswa::whereNull('kelompok_id')->get();

        // Calculate current active student load for each DPL (max 10)
        foreach ($dplList as $dpl) {
            $totalMhs = KelompokPpl::where('dpl_id', $dpl->id)
                ->where('status', 'aktif')
                ->withCount('anggota')
                ->get()
                ->sum('anggota_count');

            $dpl->total_bimbingan_mhs = $totalMhs;
        }

        return view('admin.plotting.create', compact('kelompokList', 'mitraList', 'dplList', 'unassignedMahasiswa'));
    }

    /**
     * Edit Plotting Kelompok (Penempatan Mitra, DPL, & Anggota).
     */
    public function edit(KelompokPpl $kelompok)
    {
        $kelompok->load(['mitra', 'dpl', 'anggota']);

        $mitraList = Mitra::orderBy('nama_mitra')->get();
        $dplList = User::where('role', 'dpl')->where('is_active', true)->get();

        // Available mahasiswa: unassigned OR already in this kelompok
        $availableMahasiswa = Mahasiswa::whereNull('kelompok_id')
            ->orWhere('kelompok_id', $kelompok->id)
            ->get();

        // Calculate DPL load
        foreach ($dplList as $dpl) {
            $totalMhs = KelompokPpl::where('dpl_id', $dpl->id)
                ->where('status', 'aktif')
                ->where('id', '!=', $kelompok->id)
                ->withCount('anggota')
                ->get()
                ->sum('anggota_count');

            $dpl->total_bimbingan_mhs = $totalMhs;
        }

        return view('admin.plotting.edit', compact('kelompok', 'mitraList', 'dplList', 'availableMahasiswa'));
    }

    /**
     * Simpan / Update Plotting Kelompok.
     */
    public function update(Request $request, KelompokPpl $kelompok)
    {
        $request->validate([
            'mitra_id' => ['required', 'exists:mitra,id'],
            'dpl_id' => ['required', 'exists:users,id'],
            'mahasiswa_ids' => ['required', 'array', 'min:1', 'max:20'],
            'mahasiswa_ids.*' => ['exists:anggota_kelompok,id'],
        ], [
            'mitra_id.required' => 'Mitra penempatan wajib dipilih.',
            'dpl_id.required' => 'Dosen Pembimbing (DPL) wajib dipilih.',
            'mahasiswa_ids.required' => 'Pilih minimal 1 mahasiswa anggota kelompok.',
            'mahasiswa_ids.max' => 'Satu kelompok maksimal terdiri dari 20 mahasiswa.',
        ]);

        // Validation for DPL maximum load (30 students max)
        $selectedMahasiswaCount = count($request->mahasiswa_ids);
        $currentDplLoad = KelompokPpl::where('dpl_id', $request->dpl_id)
            ->where('status', 'aktif')
            ->where('id', '!=', $kelompok->id)
            ->withCount('anggota')
            ->get()
            ->sum('anggota_count');

        if (($currentDplLoad + $selectedMahasiswaCount) > 30) {
            return back()->withInput()->withErrors([
                'dpl_id' => "DPL ini sudah membimbing {$currentDplLoad} mahasiswa. Penambahan {$selectedMahasiswaCount} mahasiswa melebihi batas maksimal 30 mahasiswa per DPL.",
            ]);
        }

        DB::transaction(function () use ($kelompok, $request) {
            $mahasiswaIds = array_map('intval', (array) $request->mahasiswa_ids);

            // Update Kelompok mapping
            KelompokPpl::where('id', $kelompok->id)->update([
                'mitra_id' => $request->mitra_id,
                'dpl_id' => $request->dpl_id,
            ]);

            // Clear previous members not selected anymore
            Mahasiswa::where('kelompok_id', $kelompok->id)
                ->whereNotIn('id', $mahasiswaIds)
                ->update(['kelompok_id' => null]);

            // Assign selected members to this kelompok
            Mahasiswa::whereIn('id', $mahasiswaIds)
                ->update(['kelompok_id' => $kelompok->id]);
        });

        return redirect()->route('admin.plotting.index')
            ->with('success', 'Plotting kelompok ' . $kelompok->nama_kelompok . ' berhasil diperbarui.');
    }

    /**
     * Cetak PDF Laporan Plotting & Pemetaan Penempatan PPL (Landscape).
     */
    public function exportPdf(Request $request)
    {
        $query = KelompokPpl::with(['mitra.picUser', 'dpl', 'anggota', 'ketua']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelompok', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function ($mq) use ($search) {
                      $mq->where('nama_mitra', 'like', "%{$search}%");
                  })
                  ->orWhereHas('dpl', function ($dq) use ($search) {
                      $dq->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $plottingList = $query->latest()->get();

        $totalMahasiswa = $plottingList->sum(fn ($k) => $k->anggota->count());
        $totalMitra = $plottingList->pluck('mitra_id')->filter()->unique()->count();

        // Base64 Logo UNIKU
        $logoUnikuBase64 = null;
        $logoPath = public_path('images/logo-uniku.png');
        if (File::exists($logoPath)) {
            $logoUnikuBase64 = 'data:image/png;base64,' . base64_encode(File::get($logoPath));
        }

        $pdf = Pdf::loadView('pdf.laporan-plotting', compact('plottingList', 'totalMahasiswa', 'totalMitra', 'logoUnikuBase64'))
            ->setPaper('a4', 'landscape');

        $fileName = 'Laporan_Plotting_Penempatan_PPL_[' . date('d-m-Y') . '].pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export Excel Report Plotting PPL.
     */
    public function exportExcel(Request $request)
    {
        $search = $request->query('search');
        $fileName = 'Laporan_Plotting_PPL_[' . date('d-m-Y') . '].xlsx';

        return Excel::download(new PlottingPplExport($search), $fileName);
    }
}
