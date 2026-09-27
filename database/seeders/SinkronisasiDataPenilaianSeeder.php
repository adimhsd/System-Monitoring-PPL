<?php

namespace Database\Seeders;

use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Menerapkan pembaruan data PPL yang dilakukan di SystemPenilaianPPL
 * (PLOTING PPL FIX.xlsx & Rekognisi PPL MBKM) ke struktur data Monitoring.
 *
 * Idempoten: aman dijalankan berulang kali. Akun Ketua Kelompok & PIC Mitra
 * baru dibuat dengan password default "password123" (konvensi import Monitoring),
 * akun DPL baru dengan password "FEB_Tangguh" (konvensi SystemPenilaianPPL).
 */
class SinkronisasiDataPenilaianSeeder extends Seeder
{
    private const TAHUN_AKADEMIK = '2026/2027';

    public function run(): void
    {
        DB::transaction(function () {
            $this->sinkronisasiPlotingFix();
            $this->sinkronisasiMbkm();
        });

        $this->command?->info('Sinkronisasi data SystemPenilaianPPL selesai.');
    }

    /**
     * Penyelarasan dengan PLOTING PPL FIX.xlsx.
     */
    private function sinkronisasiPlotingFix(): void
    {
        // Mitra & Kelompok 79
        $mitraVirginia = $this->mitra('Virginia Mahakarya Property', 'Swasta', 'Karangmangu Kabupaten Kuningan');
        $kelompok79 = $this->kelompok('KELOMPOK 79', $mitraVirginia, $this->dpl('DPL_PPL10'), 'PPL_Kelompok79');

        $this->mahasiswa('20230510122', 'Helmy Alpian D', 'Laki-laki', 'Manajemen', $kelompok79, 'Pemasaran');
        $this->mahasiswa('20230510378', 'Muhammad Raji A', 'Laki-laki', 'Manajemen', $kelompok79, 'Pemasaran');

        // Mutasi mahasiswa antar kelompok
        $mutasi = [
            '20230510423' => 'KELOMPOK 22',
            '20230510098' => 'KELOMPOK 77',
            '20230510396' => 'KELOMPOK 76',
            '20230510219' => 'KELOMPOK 16',
            '20230510284' => 'KELOMPOK 16',
            '20230610080' => 'KELOMPOK 69',
            '20230610087' => 'KELOMPOK 69',
        ];
        foreach ($mutasi as $nim => $namaKelompok) {
            $kelompok = $this->cariKelompok($namaKelompok);
            if ($kelompok) {
                AnggotaKelompok::where('nim', $nim)->update(['kelompok_id' => $kelompok->id]);
            }
        }

        // Pergantian DPL pembimbing
        $gantiDpl = [
            'KELOMPOK 08' => 'DPL_PPL24', // Dr. Rina Masruroh
            'KELOMPOK 13' => 'DPL_PPL16', // Faishal Rahimi
            'KELOMPOK 70' => 'DPL_PPL12', // Dr. Neni Nurhayati
        ];
        foreach ($gantiDpl as $namaKelompok => $username) {
            $dpl = $this->dpl($username);
            $kelompok = $this->cariKelompok($namaKelompok);
            if ($dpl && $kelompok) {
                $kelompok->update(['dpl_id' => $dpl->id]);
            }
        }

        // Mahasiswa yang tidak tercantum di ploting final
        AnggotaKelompok::where('nim', '20230510246')->delete();
    }

    /**
     * Rekognisi PPL MBKM: 2 DPL baru, 13 kelompok, 76 mahasiswa.
     */
    private function sinkronisasiMbkm(): void
    {
        $data = require database_path('data/ppl_mbkm_2026.php');

        $this->akunDpl('DPL_PPL42', 'Siti Nuke Nurfatimah, M.Sc', 'dpl_ppl42@uniku.ac.id');
        $this->akunDpl('DPL_PPL43', 'Teti Rahmawati, M.Si., Ak., CA', 'dpl_ppl43@uniku.ac.id');

        $mitraMbkm = $this->mitra('MBKM', 'MBKM', 'Program MBKM Rekognisi PPL FEB UNIKU');

        $kelompok = [];
        $no = 1;
        foreach ($data['kelompok'] as $namaKelompok => $dplUsername) {
            $username = sprintf('PPL_Kelompok_MBKM%02d', $no++);
            $kelompok[$namaKelompok] = $this->kelompok($namaKelompok, $mitraMbkm, $this->dpl($dplUsername), $username);
        }

        foreach ($data['mahasiswa'] as $mhs) {
            $this->mahasiswa($mhs['nim'], $mhs['nama'], $mhs['jenis_kelamin'], $mhs['prodi'], $kelompok[$mhs['kelompok']]);
        }
    }

    private function dpl(string $username): ?User
    {
        return User::where('role', 'dpl')->where('username', $username)->first();
    }

    private function akunDpl(string $username, string $nama, string $email): User
    {
        return User::withTrashed()->firstOrCreate(
            ['username' => $username],
            [
                'password' => Hash::make('FEB_Tangguh'),
                'role' => 'dpl',
                'nama_lengkap' => $nama,
                'email' => $email,
                'must_change_password' => true,
                'is_active' => true,
            ]
        );
    }

    private function mitra(string $nama, string $kategori, string $alamat): Mitra
    {
        $mitra = Mitra::firstOrCreate(
            ['nama_mitra' => $nama],
            ['kategori' => $kategori, 'alamat' => $alamat]
        );

        if (! $mitra->pic_user_id) {
            $pic = User::withTrashed()->firstOrCreate(
                ['username' => 'pic_' . Str::slug($nama, '_')],
                [
                    'password' => Hash::make('password123'),
                    'role' => 'pic_mitra',
                    'nama_lengkap' => 'PIC ' . $nama,
                    'must_change_password' => true,
                    'is_active' => true,
                ]
            );
            $mitra->update(['pic_user_id' => $pic->id]);
        }

        return $mitra;
    }

    private function cariKelompok(string $nama): ?KelompokPpl
    {
        $varian = [$nama, ucwords(strtolower($nama)), preg_replace('/ 0(\d)$/', ' $1', $nama)];

        return KelompokPpl::whereIn('nama_kelompok', array_unique($varian))->first();
    }

    private function kelompok(string $nama, Mitra $mitra, ?User $dpl, string $usernameKetua): KelompokPpl
    {
        $kelompok = $this->cariKelompok($nama);
        if ($kelompok) {
            return $kelompok;
        }

        $ketua = User::withTrashed()->firstOrCreate(
            ['username' => $usernameKetua],
            [
                'password' => Hash::make('password123'),
                'role' => 'ketua_kelompok',
                'nama_lengkap' => $nama,
                'must_change_password' => false,
                'is_active' => true,
            ]
        );

        return KelompokPpl::create([
            'nama_kelompok' => $nama,
            'mitra_id' => $mitra->id,
            'dpl_id' => $dpl?->id,
            'ketua_user_id' => $ketua->id,
            'tahun_akademik' => self::TAHUN_AKADEMIK,
            'status' => 'aktif',
        ]);
    }

    private function mahasiswa(string $nim, string $nama, string $jk, string $prodi, KelompokPpl $kelompok, ?string $konsentrasi = null): void
    {
        $mhs = AnggotaKelompok::firstOrNew(['nim' => $nim]);

        $mhs->fill([
            'nama' => $nama,
            'jenis_kelamin' => $jk,
            'prodi' => $prodi,
        ]);

        if ($konsentrasi !== null) {
            $mhs->konsentrasi = $konsentrasi;
        }

        $mhs->kelompok_id = $kelompok->id;
        $mhs->save();
    }
}
