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

foreach ($import->rows as $index => $row) {
    $u = (string)($row['username'] ?? '');
    if (str_contains(strtoupper($u), 'PPL41') || str_contains(strtoupper($row['nama_lengkap_dpl'] ?? ''), 'DENDI')) {
        echo "Row $index:\n";
        echo "  raw username: " . var_export($u, true) . " (length: " . strlen($u) . ")\n";
        echo "  hex bytes username: " . bin2hex($u) . "\n";
        echo "  name: " . var_export($row['nama_lengkap_dpl'], true) . "\n";
    }
}
