<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranDpl;
use App\Models\PendaftaranMahasiswa;
use App\Services\PendaftaranService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Formulir pendaftaran publik (tanpa login), seperti Google Form:
 * hanya untuk pengumpulan data yang kemudian diverifikasi Admin.
 */
class PendaftaranController extends Controller
{
    public function index()
    {
        return view('pendaftaran.index', [
            'mahasiswaDibuka' => PendaftaranService::dibuka('mahasiswa'),
            'dplDibuka' => PendaftaranService::dibuka('dpl'),
        ]);
    }

    public function formMahasiswa()
    {
        return view('pendaftaran.mahasiswa', [
            'dibuka' => PendaftaranService::dibuka('mahasiswa'),
            'programMbkm' => PendaftaranMahasiswa::PROGRAM_MBKM,
            'konsentrasiList' => PendaftaranMahasiswa::KONSENTRASI,
        ]);
    }

    public function simpanMahasiswa(Request $request)
    {
        if (! PendaftaranService::dibuka('mahasiswa')) {
            return redirect()->route('pendaftaran.mahasiswa')->with('error', 'Pendaftaran mahasiswa PPL sedang ditutup.');
        }

        $data = $request->validate([
            'jenis_ppl' => ['required', Rule::in(['reguler', 'mbkm'])],
            'nim' => [
                'required', 'string', 'regex:/^[0-9]{8,20}$/',
                Rule::unique('pendaftaran_mahasiswa', 'nim')->where(fn ($q) => $q->where('status', '!=', 'ditolak')),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'prodi' => ['required', Rule::in(['Manajemen', 'Akuntansi', 'Bisnis Digital'])],
            'konsentrasi' => ['nullable', Rule::in(PendaftaranMahasiswa::KONSENTRASI)],
            'no_hp' => ['required', 'string', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'email' => ['nullable', 'email', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'program_mbkm' => ['nullable', 'required_if:jenis_ppl,mbkm', Rule::in(PendaftaranMahasiswa::PROGRAM_MBKM)],
            'instansi_mbkm' => ['nullable', 'required_if:jenis_ppl,mbkm', 'string', 'max:150'],
            'bukti_pembayaran' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:1024'],
            'pernyataan' => ['accepted'],
        ], [
            'jenis_ppl.required' => 'Pilih jenis PPL (Reguler / MBKM).',
            'nim.required' => 'NIM wajib diisi.',
            'nim.regex' => 'NIM hanya boleh berisi angka (8-20 digit).',
            'nim.unique' => 'NIM ini sudah terdaftar. Hubungi Admin PPL jika ada perubahan data.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Pilih jenis kelamin.',
            'prodi.required' => 'Pilih program studi.',
            'no_hp.required' => 'No. HP / WhatsApp wajib diisi.',
            'no_hp.regex' => 'Format No. HP / WhatsApp tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'program_mbkm.required_if' => 'Pilih program MBKM yang diikuti.',
            'instansi_mbkm.required_if' => 'Nama instansi / mitra MBKM wajib diisi.',
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diunggah.',
            'bukti_pembayaran.mimes' => 'Bukti pembayaran harus berformat PDF.',
            'bukti_pembayaran.mimetypes' => 'Bukti pembayaran harus berformat PDF.',
            'bukti_pembayaran.max' => 'Ukuran file bukti pembayaran maksimal 1 MB.',
            'pernyataan.accepted' => 'Centang pernyataan kebenaran data terlebih dahulu.',
        ]);

        if ($data['jenis_ppl'] === 'reguler') {
            $data['program_mbkm'] = null;
            $data['instansi_mbkm'] = null;
        }

        $data['file_bukti_pembayaran'] = PendaftaranService::simpanFile(
            $request->file('bukti_pembayaran'), 'mahasiswa', 'bukti_bayar_' . $data['nim']
        );
        unset($data['bukti_pembayaran'], $data['pernyataan']);

        $pendaftaran = PendaftaranMahasiswa::create($data);

        return redirect()->route('pendaftaran.selesai')->with('pendaftaran', [
            'jenis' => 'Mahasiswa PPL ' . ($pendaftaran->isMbkm() ? 'MBKM' : 'Reguler'),
            'nama' => $pendaftaran->nama,
            'identitas' => 'NIM ' . $pendaftaran->nim,
            'nomor' => 'MHS-' . str_pad((string) $pendaftaran->id, 5, '0', STR_PAD_LEFT),
        ]);
    }

    public function formDpl()
    {
        return view('pendaftaran.dpl', [
            'dibuka' => PendaftaranService::dibuka('dpl'),
        ]);
    }

    public function simpanDpl(Request $request)
    {
        if (! PendaftaranService::dibuka('dpl')) {
            return redirect()->route('pendaftaran.dpl')->with('error', 'Pendaftaran DPL sedang ditutup.');
        }

        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nip_nidn' => [
                'required', 'string', 'max:30',
                Rule::unique('pendaftaran_dpl', 'nip_nidn')->where(fn ($q) => $q->where('status', '!=', 'ditolak')),
            ],
            'no_hp' => ['required', 'string', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'email' => ['required', 'email', 'max:100'],
            'surat_kesanggupan' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:2048'],
            'pernyataan' => ['accepted'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap beserta gelar wajib diisi.',
            'nip_nidn.required' => 'NIP / NIDN wajib diisi.',
            'nip_nidn.unique' => 'NIP / NIDN ini sudah terdaftar. Hubungi Admin PPL jika ada perubahan data.',
            'no_hp.required' => 'No. HP / WhatsApp wajib diisi.',
            'no_hp.regex' => 'Format No. HP / WhatsApp tidak valid.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'surat_kesanggupan.required' => 'Surat kesanggupan menjadi DPL wajib diunggah.',
            'surat_kesanggupan.mimes' => 'Surat kesanggupan harus berformat PDF.',
            'surat_kesanggupan.mimetypes' => 'Surat kesanggupan harus berformat PDF.',
            'surat_kesanggupan.max' => 'Ukuran file surat kesanggupan maksimal 2 MB.',
            'pernyataan.accepted' => 'Centang pernyataan kesediaan terlebih dahulu.',
        ]);

        $data['file_surat_kesanggupan'] = PendaftaranService::simpanFile(
            $request->file('surat_kesanggupan'), 'dpl', 'surat_kesanggupan_' . $data['nip_nidn']
        );
        unset($data['surat_kesanggupan'], $data['pernyataan']);

        $pendaftaran = PendaftaranDpl::create($data);

        return redirect()->route('pendaftaran.selesai')->with('pendaftaran', [
            'jenis' => 'Dosen Pembimbing Lapangan (DPL)',
            'nama' => $pendaftaran->nama_lengkap,
            'identitas' => 'NIP/NIDN ' . $pendaftaran->nip_nidn,
            'nomor' => 'DPL-' . str_pad((string) $pendaftaran->id, 5, '0', STR_PAD_LEFT),
        ]);
    }

    public function selesai()
    {
        if (! session()->has('pendaftaran')) {
            return redirect()->route('pendaftaran.index');
        }

        return view('pendaftaran.selesai', ['data' => session('pendaftaran')]);
    }
}
