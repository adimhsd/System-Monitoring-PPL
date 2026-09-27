<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sistem Monitoring PPL');
    }

    public function test_user_can_authenticate_and_redirect_to_dashboard(): void
    {
        $user = User::where('username', 'admin')->first();

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('username');
    }

    public function test_rbac_prevents_unauthorized_role_access(): void
    {
        $dplUser = User::where('username', 'dpl1')->first();

        // Login as DPL and attempt to access Admin Dashboard
        $response = $this->actingAs($dplUser)->get('/admin/dashboard');

        // Should redirect DPL to DPL Dashboard with error message
        $response->assertRedirect('/dpl/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_must_change_password_middleware_redirects_when_flag_is_true(): void
    {
        $user = User::where('username', 'admin')->first();
        $user->update(['must_change_password' => true]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertRedirect('/change-password');
    }
}
