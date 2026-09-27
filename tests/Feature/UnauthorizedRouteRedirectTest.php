<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnauthorizedRouteRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_accessing_pic_prefix_redirects_to_admin_dashboard_with_error(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Accessing /pic directly
        $response = $this->actingAs($admin)->get('/pic');
        $response->assertRedirect('/admin/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_admin_accessing_pic_dashboard_redirects_to_admin_dashboard_with_error(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Accessing /pic/dashboard directly
        $response = $this->actingAs($admin)->get('/pic/dashboard');
        $response->assertRedirect('/admin/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_admin_accessing_invalid_url_redirects_to_admin_dashboard_with_error(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Accessing non-existent URL
        $response = $this->actingAs($admin)->get('/invalid-page-does-not-exist');
        $response->assertRedirect('/admin/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_dpl_accessing_admin_dashboard_redirects_to_dpl_dashboard(): void
    {
        $dpl = User::where('role', 'dpl')->first();

        $response = $this->actingAs($dpl)->get('/admin/dashboard');
        $response->assertRedirect('/dpl/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_pic_accessing_dpl_dashboard_redirects_to_pic_dashboard(): void
    {
        $pic = User::where('role', 'pic_mitra')->first();

        $response = $this->actingAs($pic)->get('/dpl/dashboard');
        $response->assertRedirect('/pic/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_student_accessing_admin_dashboard_redirects_to_student_dashboard(): void
    {
        $student = User::where('role', 'ketua_kelompok')->first();

        $response = $this->actingAs($student)->get('/admin/dashboard');
        $response->assertRedirect('/ketua/dashboard');
        $response->assertSessionHas('error');
    }
}
