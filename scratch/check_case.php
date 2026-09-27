<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$username = 'DPL_PPL41';

$exact = User::where('username', $username)->first();
$lower = User::whereRaw('LOWER(username) = ?', [strtolower($username)])->first();

echo "Exact match: " . ($exact ? "ID {$exact->id} ({$exact->username})" : "NONE") . "\n";
echo "Case-insensitive match: " . ($lower ? "ID {$lower->id} ({$lower->username})" : "NONE") . "\n";

echo "All usernames in DB currently:\n";
foreach (User::all() as $u) {
    echo "ID: {$u->id} | Role: {$u->role} | Username: '{$u->username}'\n";
}
