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
        $this->rows[] = $array;
    }
}

$import = new InspectImport();
Excel::import($import, base_path('data-master/Master_Data_DPL_PPL.xlsx'));

echo "Total sheets in Master_Data_DPL_PPL.xlsx: " . count($import->rows) . "\n";
foreach ($import->rows as $sheetIndex => $sheetRows) {
    echo "Sheet $sheetIndex count: " . count($sheetRows) . "\n";
    foreach ($sheetRows as $rIndex => $r) {
        if (($r['username'] ?? '') === 'DPL_PPL41') {
            echo "   Found DPL_PPL41 in Sheet $sheetIndex Row $rIndex: " . var_export($r['nama_lengkap_dpl'] ?? '', true) . "\n";
        }
    }
}
