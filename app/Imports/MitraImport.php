<?php

namespace App\Imports;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MitraImport implements ToModel, WithHeadingRow
{
    /**
     * Map baris Excel ke model Mitra (dan buat Akun PIC Mitra jika ada).
     */
    public function model(array $row): Model|array|null
    {
        $namaMitra = trim((string) ($row['nama_mitra_instansi'] ?? $row['nama_mitra'] ?? $row['nama'] ?? ''));

        if (empty($namaMitra)) {
            return null;
        }

        // Normalisasi kategori (SKPD, Swasta, UMKM)
        $katInput = strtoupper(trim((string) ($row['kategori'] ?? 'SKPD')));
        $kategori = match ($katInput) {
            'SWASTA' => 'Swasta',
            'UMKM' => 'UMKM',
            default => 'SKPD',
        };

        $alamat = isset($row['alamat']) ? trim((string) $row['alamat']) : null;
        if ($alamat === '-') {
            $alamat = null;
        }

        // Process PIC Mitra account
        $picUserId = null;
        $usernamePic = trim((string) ($row['username_pic'] ?? $row['username_pic_mitra'] ?? $row['username'] ?? ''));
        $namaPic = trim((string) ($row['nama_pic_mitra'] ?? $row['nama_pic'] ?? $row['pic'] ?? ''));
        $noHpPic = isset($row['no_hp_pic_mitra']) ? trim((string) $row['no_hp_pic_mitra']) : (isset($row['no_hp_pic']) ? trim((string) $row['no_hp_pic']) : (isset($row['no_hp']) ? trim((string) $row['no_hp']) : null));

        if ($noHpPic === '-') {
            $noHpPic = null;
        }

        if (!empty($usernamePic) || (!empty($namaPic) && $namaPic !== 'Belum ada PIC')) {
            if (empty($usernamePic)) {
                $usernamePic = 'pic_' . Str::slug($namaMitra, '_');
            }

            if (empty($namaPic) || $namaPic === 'Belum ada PIC') {
                $namaPic = 'PIC ' . $namaMitra;
            }

            $picUser = User::withTrashed()->where('username', $usernamePic)->first();
            if ($picUser) {
                if ($picUser->trashed()) {
                    $picUser->restore();
                }
                $picUser->update([
                    'role' => 'pic_mitra',
                    'nama_lengkap' => $namaPic,
                    'no_hp' => $noHpPic ?? $picUser->no_hp,
                    'is_active' => true,
                ]);
            } else {
                $picUser = User::create([
                    'username' => $usernamePic,
                    'password' => Hash::make('password123'),
                    'role' => 'pic_mitra',
                    'nama_lengkap' => $namaPic,
                    'no_hp' => $noHpPic,
                    'must_change_password' => true,
                    'is_active' => true,
                ]);
            }

            $picUserId = $picUser->id;
        }

        $mitra = Mitra::where('nama_mitra', $namaMitra)->first();

        if ($mitra) {
            $updatePayload = [
                'kategori' => $kategori,
                'alamat' => $alamat ?? $mitra->alamat,
            ];

            if ($picUserId !== null) {
                $updatePayload['pic_user_id'] = $picUserId;
            }

            $mitra->update($updatePayload);

            return $mitra;
        }

        return Mitra::create([
            'nama_mitra' => $namaMitra,
            'kategori' => $kategori,
            'alamat' => $alamat,
            'pic_user_id' => $picUserId,
        ]);
    }
}
