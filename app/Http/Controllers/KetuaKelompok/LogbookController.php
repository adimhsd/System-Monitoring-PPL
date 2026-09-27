<?php

namespace App\Http\Controllers\KetuaKelompok;

use App\Http\Controllers\Controller;
use App\Jobs\CompressAndUploadFotoJob;
use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    /**
     * Tampilkan Daftar Logbook Kegiatan Harian Kelompok.
     */
    public function index()
    {
        $ketua = Auth::user();
        $kelompok = KelompokPpl::where('ketua_user_id', $ketua->id)->first();

        if (! $kelompok) {
            return redirect()->route('ketua.dashboard')
                ->with('error', 'Anda belum terdaftar dalam kelompok PPL aktif.');
        }

        $logbookList = KegiatanHarian::where('kelompok_id', $kelompok->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('ketua.logbook.index', compact('kelompok', 'logbookList'));
    }

    /**
     * Form Input Logbook Harian Baru.
     */
    public function create()
    {
        $ketua = Auth::user();
        $kelompok = KelompokPpl::where('ketua_user_id', $ketua->id)->first();

        if (! $kelompok || $kelompok->status !== 'aktif') {
            return redirect()->route('ketua.dashboard')
                ->with('error', 'Kelompok PPL Anda sudah tidak aktif atau belum terdaftar.');
        }

        return view('ketua.logbook.create', compact('kelompok'));
    }

    /**
     * Simpan Entri Logbook Harian Baru.
     */
    public function store(Request $request)
    {
        $ketua = Auth::user();
        $kelompok = KelompokPpl::where('ketua_user_id', $ketua->id)->firstOrFail();

        if ($kelompok->status !== 'aktif') {
            return redirect()->route('ketua.dashboard')
                ->with('error', 'Kelompok PPL Anda sudah tidak aktif.');
        }

        $request->validate([
            'tanggal' => ['required', 'date'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'waktu_selesai' => ['required', 'date_format:H:i', 'after:waktu_mulai'],
            'deskripsi_kegiatan' => ['required', 'string', 'min:10'],
            'foto_dokumentasi' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:1024'],
        ], [
            'tanggal.required' => 'Tanggal kegiatan wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
            'deskripsi_kegiatan.min' => 'Deskripsi kegiatan minimal 10 karakter.',
            'foto_dokumentasi.required' => 'Foto dokumentasi wajib diunggah.',
            'foto_dokumentasi.mimetypes' => 'Foto harus berformat JPG, JPEG, PNG, atau WebP.',
            'foto_dokumentasi.max' => 'Ukuran file foto maksimal 1MB di server.',
        ]);

        // Cek Constraint Unique (1 kelompok hanya 1 entri per tanggal)
        $existing = KegiatanHarian::where('kelompok_id', $kelompok->id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return redirect()->route('ketua.logbook.edit', $existing)
                ->with('info', 'Logbook untuk tanggal ' . Carbon::parse($request->tanggal)->translatedFormat('d F Y') . ' sudah ada. Anda dapat mengeditnya di halaman ini.');
        }

        // Auto-flag Terlambat: jika tanggal logbook di-backdate > 1 hari dari hari ini
        $logbookDate = Carbon::parse($request->tanggal);
        $isTerlambat = $logbookDate->diffInDays(now(), false) > 1;

        // Simpan temporary file foto ke storage/app/private/temp
        $tempPath = $request->file('foto_dokumentasi')->store('temp', 'local');

        $kegiatan = KegiatanHarian::create([
            'kelompok_id' => $kelompok->id,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'foto_dokumentasi' => $tempPath, // sementara simpan path temp
            'dilihat_mitra' => false,
            'dilihat_dpl' => false,
            'terlambat' => $isTerlambat,
        ]);

        // Dispatch Async Queue Job untuk Kompresi (max 800px WebP) & Upload ke R2/Local
        CompressAndUploadFotoJob::dispatch($kegiatan->id, $tempPath);

        return redirect()->route('ketua.logbook.index')
            ->with('success', 'Logbook harian berhasil disimpan. Foto sedang diproses secara otomatis di latar belakang.');
    }

    /**
     * Detail Logbook Harian Kelompok.
     */
    public function show(KegiatanHarian $logbook)
    {
        $ketua = Auth::user();
        $kelompok = KelompokPpl::where('ketua_user_id', $ketua->id)->firstOrFail();

        if ($logbook->kelompok_id !== $kelompok->id) {
            abort(403, 'Akses ditolak. Logbook ini bukan milik kelompok Anda.');
        }

        $logbook->load(['kelompok.mitra.picUser', 'kelompok.dpl']);

        return view('ketua.logbook.show', compact('kelompok', 'logbook'));
    }

    /**
     * Form Edit Logbook Harian.
     */
    public function edit(KegiatanHarian $logbook)
    {
        $ketua = Auth::user();
        $kelompok = KelompokPpl::where('ketua_user_id', $ketua->id)->firstOrFail();

        if ($logbook->kelompok_id !== $kelompok->id) {
            abort(403, 'Akses ditolak.');
        }

        return view('ketua.logbook.edit', compact('kelompok', 'logbook'));
    }

    /**
     * Update Entri Logbook Harian.
     */
    public function update(Request $request, KegiatanHarian $logbook)
    {
        $ketua = Auth::user();
        $kelompok = KelompokPpl::where('ketua_user_id', $ketua->id)->firstOrFail();

        if ($logbook->kelompok_id !== $kelompok->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'waktu_selesai' => ['required', 'date_format:H:i', 'after:waktu_mulai'],
            'deskripsi_kegiatan' => ['required', 'string', 'min:10'],
            'foto_dokumentasi' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:1024'],
        ]);

        $data = [
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
        ];

        if ($request->hasFile('foto_dokumentasi')) {
            $tempPath = $request->file('foto_dokumentasi')->store('temp', 'local');
            $data['foto_dokumentasi'] = $tempPath;

            CompressAndUploadFotoJob::dispatch($logbook->id, $tempPath);
        }

        $logbook->update($data);

        return redirect()->route('ketua.logbook.index')
            ->with('success', 'Logbook harian berhasil diperbarui.');
    }
}
