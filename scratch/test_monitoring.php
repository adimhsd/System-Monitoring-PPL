<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MonitoringDpl;

echo "MonitoringDpl count: " . MonitoringDpl::count() . PHP_EOL;
echo "SUCCESS!" . PHP_EOL;
