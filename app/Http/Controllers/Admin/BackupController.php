<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    /**
     * Download database dump in SQL format (file_backup.sql).
     */
    public function downloadBackup(): StreamedResponse
    {
        $filename = 'file_backup_[' . date('d-m-Y') . '].sql';

        return response()->streamDownload(function () {
            $pdo = DB::getPdo();
            $driver = DB::getDriverName();

            echo "-- ========================================================\n";
            echo "-- Database Backup - Sistem Monitoring PPL FEB UNIKU\n";
            echo "-- Date: " . date('Y-m-d H:i:s') . "\n";
            echo "-- Database Driver: " . $driver . "\n";
            echo "-- ========================================================\n\n";

            if ($driver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $tableKey = 'name';
            } else {
                echo "SET FOREIGN_KEY_CHECKS = 0;\n\n";
                $tables = DB::select('SHOW TABLES');
                if (empty($tables)) {
                    return;
                }
                $tableKey = array_keys((array) $tables[0])[0] ?? null;
            }

            foreach ($tables as $tableObj) {
                if (! $tableKey || ! isset($tableObj->$tableKey)) {
                    continue;
                }

                $table = $tableObj->$tableKey;

                echo "-- --------------------------------------------------------\n";
                echo "-- Table structure for table `{$table}`\n";
                echo "-- --------------------------------------------------------\n";
                echo "DROP TABLE IF EXISTS `{$table}`;\n";

                if ($driver === 'sqlite') {
                    $createStmt = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                    if ($createStmt && isset($createStmt->sql)) {
                        echo $createStmt->sql . ";\n\n";
                    }
                } else {
                    $createStmt = DB::selectOne("SHOW CREATE TABLE `{$table}`");
                    $createKey = 'Create Table';
                    if (isset($createStmt->$createKey)) {
                        echo $createStmt->$createKey . ";\n\n";
                    }
                }

                echo "-- Dumping data for table `{$table}`\n";
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

                            echo "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                        }
                    }
                }
                echo "\n";
            }

            if ($driver !== 'sqlite') {
                echo "SET FOREIGN_KEY_CHECKS = 1;\n";
            }
        }, $filename, [
            'Content-Type' => 'text/x-sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
