<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSubmenuAndCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_access_users_overview_summary(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Kelola User Sistem');
        $response->assertSee('Akun DPL');
        $response->assertSee('Akun PIC Mitra');
        $response->assertSee('Akun Kelompok');
    }

    public function test_admin_can_access_dpl_user_category(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/dpl');

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun DPL');
        $response->assertSee('Total:');
    }

    public function test_admin_can_access_pic_user_category(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/pic');

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun PIC Pembimbing Mitra');
        $response->assertSee('Total:');
    }

    public function test_admin_can_access_kelompok_user_category(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/kelompok');

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Kelompok PPL');
        $response->assertSee('Total:');
    }
}
