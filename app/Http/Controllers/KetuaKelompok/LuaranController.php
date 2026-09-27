<?php

namespace App\Http\Controllers\KetuaKelompok;

use App\Http\Controllers\Controller;
use App\Models\KelompokPpl;
use App\Models\LuaranKelompok;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LuaranController extends Controller
{
    /**
     * Tampilkan Halaman Luaran Akhir Kelompok.
     */
    public function index()
    {
        $ketua = Auth::user();
        $kelompok = KelompokPpl::where('ketua_user_id', $ketua->id)->first();

        if (! $kelompok) {
            return redirect()->route('ketua.dashboard')
                ->with('error', 'Anda belum terdaftar dalam kelompok PPL aktif.');
        }

        $luaran = LuaranKelompok::where('kelompok_id', $kelompok->id)->first();

        return view('ketua.luaran.index', compact('kelompok', 'luaran'));
    }

    /**
     * Simpan / Perbarui Upload Luaran Akhir (PDF Max 10MB + YouTube Link).
     */
    public function storeOrUpdate(Request $request)
    {
        $ketua = Auth::user();
        $kelompok = KelompokPpl::where('ketua_user_id', $ketua->id)->firstOrFail();

        $luaran = LuaranKelompok::where('kelompok_id', $kelompok->id)->first();

        $rules = [
            'url_video' => ['required', 'url', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/i'],
            'file_laporan_pdf' => [
                $luaran ? 'nullable' : 'required',
                'file',
                'mimetypes:application/pdf',
                'max:10240', // Max 10MB (10240 KB)
            ],
        ];

        $messages = [
            'file_laporan_pdf.required' => 'File laporan akhir format PDF wajib diunggah.',
            'file_laporan_pdf.mimetypes' => 'File laporan harus berformat PDF.',
            'file_laporan_pdf.max' => 'Ukuran file PDF maksimal 10MB.',
            'url_video.required' => 'Link URL video YouTube kegiatan PPL wajib diisi.',
            'url_video.url' => 'Format URL video tidak valid.',
            'url_video.regex' => 'Link video harus berasal dari YouTube (youtube.com atau youtu.be).',
        ];

        $request->validate($rules, $messages);

        $filePath = $luaran ? $luaran->file_laporan_pdf : null;

        if ($request->hasFile('file_laporan_pdf')) {
            $disk = config('filesystems.disks.r2.key') ? 'r2' : 'local';
            $fileName = 'luaran/kelompok_' . $kelompok->id . '/laporan_akhir_' . time() . '.pdf';
            
            $filePath = $request->file('file_laporan_pdf')->storeAs(
                'luaran/kelompok_' . $kelompok->id,
                'laporan_akhir_' . time() . '.pdf',
                $disk
            );
        }

        $luaranRecord = LuaranKelompok::updateOrCreate(
            ['kelompok_id' => $kelompok->id],
            [
                'file_laporan_pdf' => $filePath,
                'url_video' => $request->url_video,
                'uploaded_at' => now(),
            ]
        );

        // Kirim Notifikasi ke DPL & PIC Mitra
        if ($kelompok->dpl_id) {
            NotifikasiService::kirim(
                $kelompok->dpl_id,
                'Luaran Akhir PPL Diunggah',
                'Kelompok ' . $kelompok->nama_kelompok . ' telah mengunggah Laporan PDF & Link Video YouTube.',
                route('dpl.luaran.index')
            );
        }

        if ($kelompok->mitra && $kelompok->mitra->pic_user_id) {
            NotifikasiService::kirim(
                $kelompok->mitra->pic_user_id,
                'Luaran Akhir PPL Diunggah',
                'Kelompok ' . $kelompok->nama_kelompok . ' telah mengunggah Laporan PDF & Link Video YouTube.',
                route('pic.dashboard')
            );
        }

        return redirect()->route('ketua.luaran.index')
            ->with('success', 'Luaran akhir PPL (Laporan PDF & Link Video YouTube) berhasil disimpan.');
    }
}
