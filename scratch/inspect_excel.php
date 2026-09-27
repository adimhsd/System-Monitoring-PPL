<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class InspectImport implements ToArray, WithHeadingRow {
    public array $rows = [];
    public function array(array $array): void {
        $this->rows = $array;
    }
}

$import = new InspectImport();
Excel::import($import, base_path('data-master/Master_Data_DPL_PPL.xlsx'));

echo "Total rows in Master_Data_DPL_PPL.xlsx: " . count($import->rows) . "\n";

$usernames = [];
foreach ($import->rows as $index => $row) {
    $u = trim((string)($row['username'] ?? ''));
    $name = trim((string)($row['nama_lengkap_dpl'] ?? ''));
    echo "Row $index: username='$u' | name='$name'\n";
    if (isset($usernames[$u])) {
        echo "   WARNING: Duplicate username '$u' in Excel! First seen at row {$usernames[$u]}\n";
    } else {
        $usernames[$u] = $index;
    }
}
