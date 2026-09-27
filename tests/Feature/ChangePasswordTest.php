<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected User $ketuaUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->ketuaUser = User::create([
            'username' => 'ketua_pass_test',
            'password' => Hash::make('OldPassword123'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Ketua Password Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);
    }

    public function test_student_can_access_change_password_form(): void
    {
        $response = $this->actingAs($this->ketuaUser)->get('/change-password-form');

        $response->assertStatus(200);
        $response->assertSee('Perbarui Password Akun');
    }

    public function test_student_can_update_password_successfully(): void
    {
        $response = $this->actingAs($this->ketuaUser)->post('/change-password', [
            'current_password' => 'OldPassword123',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertRedirect('/ketua/dashboard');

        $this->ketuaUser->refresh();
        $this->assertTrue(Hash::check('NewPassword123', $this->ketuaUser->password));
    }
}
