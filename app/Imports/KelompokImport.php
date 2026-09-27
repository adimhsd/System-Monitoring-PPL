<?php

namespace App\Imports;

use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelompokImport implements ToModel, WithHeadingRow
{
    /**
     * Map baris Excel ke model KelompokPpl & User Ketua Kelompok.
     */
    public function model(array $row): Model|array|null
    {
        $idKelompok = trim((string) ($row['id_kelompok'] ?? ''));
        $namaKelompok = trim((string) ($row['nama_kelompok'] ?? ''));

        if (empty($namaKelompok)) {
            return null;
        }

        $tahunAkademik = trim((string) ($row['tahun_akademik'] ?? '2026/2027'));
        $statusInput = strtolower(trim((string) ($row['status_kelompok'] ?? $row['status'] ?? 'aktif')));
        $status = in_array($statusInput, ['aktif', 'selesai', 'dibatalkan']) ? $statusInput : 'aktif';

        // 1. Resolve / Create User Ketua Kelompok
        $usernameInput = trim((string) ($row['username_ketua_user'] ?? $row['username_ketua'] ?? $row['username'] ?? ''));
        if (empty($usernameInput)) {
            $usernameInput = 'kelompok_' . Str::slug($namaKelompok, '_');
        }

        $passwordInput = trim((string) ($row['password_ketua'] ?? $row['password'] ?? 'password123'));
        if (empty($passwordInput)) {
            $passwordInput = 'password123';
        }

        $userKetua = User::withTrashed()
            ->where('username', $usernameInput)
            ->first();

        if ($userKetua) {
            if ($userKetua->trashed()) {
                $userKetua->restore();
            }
            $userKetua->update([
                'nama_lengkap' => $namaKelompok,
                'role' => 'ketua_kelompok',
                'is_active' => true,
            ]);
        } else {
            $userKetua = User::create([
                'username' => $usernameInput,
                'password' => Hash::make($passwordInput),
                'role' => 'ketua_kelompok',
                'nama_lengkap' => $namaKelompok,
                'must_change_password' => false,
                'is_active' => true,
            ]);
        }

        // 2. Resolve DPL
        $dplId = null;
        $dplInput = trim((string) ($row['nama_dpl'] ?? $row['dpl'] ?? ''));
        if (!empty($dplInput) && $dplInput !== '-' && strtolower($dplInput) !== 'belum diplotkan') {
            $dplUser = User::withTrashed()
                ->where('role', 'dpl')
                ->where(function ($q) use ($dplInput) {
                    $q->where('nip_nidn', $dplInput)
                      ->orWhere('nama_lengkap', $dplInput)
                      ->orWhere('nama_lengkap', 'like', "%{$dplInput}%");
                })
                ->first();

            if ($dplUser) {
                if ($dplUser->trashed()) {
                    $dplUser->restore();
                }
                $dplId = $dplUser->id;
            }
        }

        // 3. Resolve Mitra
        $mitraId = null;
        $mitraInput = trim((string) ($row['nama_mitra'] ?? $row['mitra'] ?? ''));
        if (!empty($mitraInput) && $mitraInput !== '-' && strtolower($mitraInput) !== 'belum diplotkan') {
            $mitraObj = Mitra::withTrashed()
                ->where(function ($q) use ($mitraInput) {
                    $q->where('nama_mitra', $mitraInput)
                      ->orWhere('nama_mitra', 'like', "%{$mitraInput}%");
                })
                ->first();

            if ($mitraObj) {
                if ($mitraObj->trashed()) {
                    $mitraObj->restore();
                }
                $mitraId = $mitraObj->id;
            }
        }

        // 4. Update or Create KelompokPpl
        $existingKelompok = null;
        if (!empty($idKelompok) && is_numeric($idKelompok)) {
            $existingKelompok = KelompokPpl::find($idKelompok);
        }

        if (!$existingKelompok) {
            $existingKelompok = KelompokPpl::where('nama_kelompok', $namaKelompok)->first();
        }

        $payload = [
            'nama_kelompok' => $namaKelompok,
            'ketua_user_id' => $userKetua->id,
            'tahun_akademik' => $tahunAkademik,
            'status' => $status,
        ];

        if ($dplId !== null) {
            $payload['dpl_id'] = $dplId;
        }

        if ($mitraId !== null) {
            $payload['mitra_id'] = $mitraId;
        }

        if ($existingKelompok) {
            $existingKelompok->update($payload);
            return $existingKelompok;
        }

        return KelompokPpl::create($payload);
    }
}
