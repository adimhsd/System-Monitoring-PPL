<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "Total users in MySQL DB: " . User::count() . "\n";
$users = User::all();
foreach ($users as $u) {
    echo "ID: {$u->id} | Role: {$u->role} | Username: '{$u->username}' | Name: '{$u->nama_lengkap}' | NIP: '{$u->nip_nidn}'\n";
}
