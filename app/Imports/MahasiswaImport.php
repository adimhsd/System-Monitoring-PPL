<?php

namespace App\Imports;

use App\Models\KelompokPpl;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaImport implements ToModel, WithHeadingRow
{
    /**
     * Map baris Excel ke model Mahasiswa.
     */
    public function model(array $row): Model|array|null
    {
        $nim = trim((string) ($row['nim'] ?? ''));

        if (empty($nim)) {
            return null;
        }

        // Normalisasi nama
        $nama = trim((string) ($row['nama_mahasiswa'] ?? $row['nama'] ?? 'Mahasiswa PPL'));

        // Normalisasi jenis kelamin
        $jkInput = strtolower(trim((string) ($row['jenis_kelamin'] ?? $row['jk'] ?? $row['gender'] ?? 'Laki-laki')));
        $jenisKelamin = match ($jkInput) {
            'perempuan', 'p', 'female', 'f', 'wanita' => 'Perempuan',
            default => 'Laki-laki',
        };

        // Normalisasi prodi
        $prodiInput = trim((string) ($row['program_studi'] ?? $row['prodi'] ?? 'Manajemen'));
        $prodi = match (strtolower($prodiInput)) {
            'akuntansi' => 'Akuntansi',
            'bisnis digital', 'bisnis_digital' => 'Bisnis Digital',
            default => 'Manajemen',
        };

        // Normalisasi konsentrasi, no_hp, alamat
        $konsentrasi = isset($row['konsentrasi']) ? trim((string) $row['konsentrasi']) : (isset($row['kelas']) ? trim((string) $row['kelas']) : null);
        if ($konsentrasi === '-') {
            $konsentrasi = null;
        }

        $noHp = isset($row['no_hp_whatsapp']) ? trim((string) $row['no_hp_whatsapp']) : (isset($row['no_hp']) ? trim((string) $row['no_hp']) : null);
        if ($noHp === '-') {
            $noHp = null;
        }

        $alamat = isset($row['alamat']) ? trim((string) $row['alamat']) : null;
        if ($alamat === '-') {
            $alamat = null;
        }

        // Normalisasi Kelompok PPL
        $kelompokId = null;
        $kelompokInput = trim((string) ($row['kelompok_ppl'] ?? $row['nama_kelompok'] ?? $row['kelompok'] ?? ''));

        if (!empty($kelompokInput) && $kelompokInput !== '-' && strtolower($kelompokInput) !== 'belum diplotkan') {
            $parts = explode('-', $kelompokInput);
            $namaKelompokClean = trim($parts[0]);

            $kelompokObj = KelompokPpl::where('id', $kelompokInput)
                ->orWhere('nama_kelompok', $kelompokInput)
                ->orWhere('nama_kelompok', $namaKelompokClean)
                ->orWhere('nama_kelompok', 'like', "%{$namaKelompokClean}%")
                ->first();

            if ($kelompokObj) {
                $kelompokId = $kelompokObj->id;
            }
        }

        $payload = [
            'nama' => $nama,
            'jenis_kelamin' => $jenisKelamin,
            'prodi' => $prodi,
            'konsentrasi' => $konsentrasi,
            'no_hp' => $noHp,
            'alamat' => $alamat,
        ];

        if ($kelompokId !== null) {
            $payload['kelompok_id'] = $kelompokId;
        }

        return Mahasiswa::updateOrCreate(
            ['nim' => $nim],
            $payload
        );
    }
}
