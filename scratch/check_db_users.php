<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "DPL Users in DB:\n";
$dplUsers = User::where('role', 'dpl')->get(['id', 'username', 'nama_lengkap', 'nip_nidn']);
foreach ($dplUsers as $u) {
    echo "ID: {$u->id} | Username: {$u->username} | Name: {$u->nama_lengkap} | NIP: {$u->nip_nidn}\n";
}

echo "\nChecking if DPL_PPL41 exists in DB under any role:\n";
$target = User::where('username', 'DPL_PPL41')->first();
if ($target) {
    echo "FOUND: ID {$target->id} | Role: {$target->role} | Username: {$target->username} | Name: {$target->nama_lengkap}\n";
} else {
    echo "DPL_PPL41 does NOT exist in DB.\n";
}
