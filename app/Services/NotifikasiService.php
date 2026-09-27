<?php

namespace App\Services;

use App\Models\Notifikasi;

class NotifikasiService
{
    /**
     * Kirim notifikasi ke user tertentu.
     */
    public static function kirim(int $userId, string $judul, string $pesan, ?string $link = null): Notifikasi
    {
        return Notifikasi::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => 'sistem',
            'link' => $link,
            'is_read' => false,
        ]);
    }
}
