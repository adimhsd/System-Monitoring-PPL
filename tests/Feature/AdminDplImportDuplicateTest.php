<?php

namespace Tests\Feature;

use App\Imports\DplImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDplImportDuplicateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_dpl_import_handles_duplicate_username_gracefully(): void
    {
        // User A with username DPL_PPL41
        User::create([
            'username' => 'DPL_PPL41',
            'password' => Hash::make('password'),
            'role' => 'dpl',
            'nama_lengkap' => 'User DPL A',
            'nip_nidn' => '11111111',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        // User B with NIP 22222222 and username dpl_user_b
        $userB = User::create([
            'username' => 'dpl_user_b',
            'password' => Hash::make('password'),
            'role' => 'dpl',
            'nama_lengkap' => 'User DPL B',
            'nip_nidn' => '22222222',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $import = new DplImport();

        // Row trying to update User B by NIP 22222222, but passes username DPL_PPL41 (already owned by User A)
        $res = $import->model([
            'nama_lengkap_dpl' => 'User DPL B Updated',
            'nip_nidn' => '22222222',
            'username' => 'DPL_PPL41',
            'no_hp_whatsapp' => '08123456789',
        ]);

        $this->assertNotNull($res);
        $this->assertEquals('User DPL B Updated', $userB->fresh()->nama_lengkap);
        $this->assertEquals('dpl_user_b', $userB->fresh()->username); // Username stays safe, not causing 1062 duplicate error!
    }

    public function test_dpl_import_generates_unique_username_for_new_user_if_auto_username_taken(): void
    {
        // User with auto-generated username dpl_88888888
        User::create([
            'username' => 'dpl_88888888',
            'password' => Hash::make('password'),
            'role' => 'dpl',
            'nama_lengkap' => 'User Existing',
            'nip_nidn' => '99999999',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $import = new DplImport();
        // New user with NIP 88888888 (auto-username dpl_88888888 is already taken by User Existing)
        $res = $import->model([
            'nama_lengkap_dpl' => 'User Brand New',
            'nip_nidn' => '88888888',
        ]);

        $this->assertNotNull($res);
        $this->assertEquals('dpl_88888888_1', $res->username); // Auto-suffixed to ensure uniqueness!
    }
}
