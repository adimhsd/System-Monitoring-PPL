<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        return $this->showLoginForm();
    }

    public function store(Request $request)
    {
        return $this->login($request);
    }

    public function destroy(Request $request)
    {
        return $this->logout($request);
    }

    /**
     * Tampilkan Halaman Login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses Login dengan Rate Limiting & Session Regeneration.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('username')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            throw ValidationException::withMessages([
                'username' => ["Terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam {$minutes} menit."],
            ]);
        }

        $user = User::where('username', $request->username)->first();

        if (! $user || ! $user->is_active) {
            RateLimiter::hit($throttleKey, 900); // Lockout 15 menit (900 detik)

            throw ValidationException::withMessages([
                'username' => ['Username tidak ditemukan atau akun Anda tidak aktif.'],
            ]);
        }

        if (! Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 900);

            throw ValidationException::withMessages([
                'username' => ['Username atau password yang Anda masukkan salah.'],
            ]);
        }

        // Login berhasil, bersihkan rate limiter
        RateLimiter::clear($throttleKey);

        // Regenerate session ID untuk keamanan
        $request->session()->regenerate();

        // Cek jika wajib ganti password
        if ($user->must_change_password) {
            return redirect()->route('password.change')
                ->with('info', 'Selamat datang! Demi keamanan, Anda diwajibkan memperbarui password akun Anda.');
        }

        return $this->redirectBasedOnRole($user)->with('success', 'Berhasil login. Selamat datang, ' . $user->nama_lengkap . '!');
    }

    /**
     * Logout User & Invalidate Session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Form Ganti Password Wajib.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Update Password Wajib / Mandiri.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak sesuai.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Password Anda telah berhasil diperbarui.');
    }

    /**
     * Form Lupa Password.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Helper Redirect Sesuai Role.
     */
    protected function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dpl' => redirect()->route('dpl.dashboard'),
            'pic_mitra' => redirect()->route('pic.dashboard'),
            'ketua_kelompok' => redirect()->route('ketua.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
