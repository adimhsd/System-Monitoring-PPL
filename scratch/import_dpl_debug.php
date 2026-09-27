<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class DebugDplImport extends App\Imports\DplImport {
    public int $rowCount = 0;

    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null {
        $this->rowCount++;
        $u = trim((string)($row['username'] ?? ''));
        $n = trim((string)($row['nama_lengkap_dpl'] ?? ''));
        echo "[Row {$this->rowCount}] username='$u' | name='$n'\n";

        try {
            $res = parent::model($row);
            if ($res) {
                echo "  -> OK: ID {$res->id} | username '{$res->username}'\n";
            }
            return $res;
        } catch (\Throwable $e) {
            echo "  -> FAILED: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

echo "Starting debug import of data-master/Master_Data_DPL_PPL.xlsx ...\n";
try {
    Excel::import(new DebugDplImport, base_path('data-master/Master_Data_DPL_PPL.xlsx'));
    echo "SUCCESS! Total DPL: " . User::where('role', 'dpl')->count() . "\n";
} catch (\Throwable $e) {
    echo "CAUGHT EXCEPTION: " . $e->getMessage() . "\n";
}
