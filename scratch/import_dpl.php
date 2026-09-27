<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Imports\DplImport;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

echo "Memulai Impor File Excel DPL: data-master/Master_Data_DPL_PPL.xlsx ...\n";

$filePath = base_path('data-master/Master_Data_DPL_PPL.xlsx');

if (!file_exists($filePath)) {
    echo "ERROR: Berkas $filePath tidak ditemukan!\n";
    exit(1);
}

try {
    Excel::import(new DplImport, $filePath);
    $totalDpl = User::where('role', 'dpl')->count();
    echo "SUCCESS: Berkas Excel DPL berhasil diimpor ke dalam database!\n";
    echo "Total Data Master DPL di Database sekarang: {$totalDpl} DPL.\n";
} catch (\Throwable $e) {
    echo "ERROR: Gagal mengimpor file Excel: " . $e->getMessage() . "\n";
    exit(1);
}
