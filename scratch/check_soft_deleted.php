<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$softDeleted = User::onlyTrashed()->get();

echo "Total Soft Deleted Users in DB: " . $softDeleted->count() . "\n";
foreach ($softDeleted as $u) {
    echo "Trashed User ID: {$u->id} | Role: {$u->role} | Username: '{$u->username}' | Name: '{$u->nama_lengkap}' | Deleted At: {$u->deleted_at}\n";
}
