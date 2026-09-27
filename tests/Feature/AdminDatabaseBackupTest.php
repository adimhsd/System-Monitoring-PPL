<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDatabaseBackupTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $dpl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        if ($this->admin) {
            $this->admin->update(['must_change_password' => false, 'is_active' => true]);
        } else {
            $this->admin = User::create([
                'username' => 'admin_test_backup',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nama_lengkap' => 'Admin Backup Test',
                'must_change_password' => false,
                'is_active' => true,
            ]);
        }

        $this->dpl = User::where('role', 'dpl')->first();
        if ($this->dpl) {
            $this->dpl->update(['must_change_password' => false, 'is_active' => true]);
        } else {
            $this->dpl = User::create([
                'username' => 'dpl_test_backup',
                'password' => Hash::make('password'),
                'role' => 'dpl',
                'nama_lengkap' => 'DPL Backup Test',
                'must_change_password' => false,
                'is_active' => true,
            ]);
        }
    }

    public function test_admin_can_download_database_backup_sql(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/backup/download');

        $expectedFilename = 'attachment; filename="file_backup_[' . date('d-m-Y') . '].sql"';
        $response->assertHeader('content-disposition', $expectedFilename);

        $content = $response->streamedContent();
        $this->assertStringContainsString('-- Database Backup - Sistem Monitoring PPL FEB UNIKU', $content);
        $this->assertStringContainsString('DROP TABLE IF EXISTS', $content);
        $this->assertStringContainsString('users', $content);
    }

    public function test_non_admin_cannot_download_database_backup(): void
    {
        $response = $this->actingAs($this->dpl)->get('/admin/backup/download');
        $response->assertRedirect(route('dpl.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_guest_cannot_download_database_backup(): void
    {
        $response = $this->get('/admin/backup/download');
        $response->assertRedirect('/login');
    }
}
