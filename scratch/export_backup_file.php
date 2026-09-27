<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$filename = __DIR__ . '/../data-master/file_backup_[' . date('d-m-Y') . '].sql';

$pdo = DB::getPdo();
$driver = DB::getDriverName();

$output = "";
$output .= "-- ========================================================\n";
$output .= "-- Database Backup - Sistem Monitoring PPL FEB UNIKU\n";
$output .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
$output .= "-- Database Driver: " . $driver . "\n";
$output .= "-- ========================================================\n\n";

if ($driver !== 'sqlite') {
    $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
    $tables = DB::select('SHOW TABLES');
    $tableKey = array_keys((array) $tables[0])[0] ?? null;
} else {
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    $tableKey = 'name';
}

foreach ($tables as $tableObj) {
    if (!$tableKey || !isset($tableObj->$tableKey)) {
        continue;
    }

    $table = $tableObj->$tableKey;

    $output .= "-- --------------------------------------------------------\n";
    $output .= "-- Table structure for table `{$table}`\n";
    $output .= "-- --------------------------------------------------------\n";
    $output .= "DROP TABLE IF EXISTS `{$table}`;\n";

    if ($driver === 'sqlite') {
        $createStmt = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
        if ($createStmt && isset($createStmt->sql)) {
            $output .= $createStmt->sql . ";\n\n";
        }
    } else {
        $createStmt = DB::selectOne("SHOW CREATE TABLE `{$table}`");
        $createKey = 'Create Table';
        if (isset($createStmt->$createKey)) {
            $output .= $createStmt->$createKey . ";\n\n";
        }
    }

    $output .= "-- Dumping data for table `{$table}`\n";
    $rows = DB::table($table)->get();

    if ($rows->count() > 0) {
        foreach ($rows->chunk(100) as $chunk) {
            foreach ($chunk as $row) {
                $rowArray = (array) $row;
                $columns = array_map(fn($col) => "`{$col}`", array_keys($rowArray));
                $values = array_map(function ($val) use ($pdo) {
                    if (is_null($val)) {
                        return 'NULL';
                    }
                    return $pdo->quote((string) $val);
                }, array_values($rowArray));

                $output .= "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
        }
    }
    $output .= "\n";
}

if ($driver !== 'sqlite') {
    $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";
}

file_put_contents($filename, $output);
echo "Backup SQL exported to {$filename} (" . filesize($filename) . " bytes)" . PHP_EOL;
