<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

try {
    echo "Checking table monitoring_dpl existence..." . PHP_EOL;
    if (!Schema::hasTable('monitoring_dpl')) {
        echo "Table monitoring_dpl NOT found! Running artisan migrate..." . PHP_EOL;
        Artisan::call('migrate', ['--force' => true]);
        echo Artisan::output() . PHP_EOL;
    } else {
        echo "Table monitoring_dpl already exists!" . PHP_EOL;
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
