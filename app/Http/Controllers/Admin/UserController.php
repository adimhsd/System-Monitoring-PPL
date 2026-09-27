<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan Ringkasan Jumlah Akun & Daftar Pengguna Sistem.
     */
    public function index(Request $request)
    {
        // Ringkasan Jumlah Akun per Kategori
        $stats = [
            'total_all' => User::count(),
            'total_admin' => User::where('role', 'admin')->count(),
            'total_dpl' => User::where('role', 'dpl')->count(),
            'total_pic' => User::where('role', 'pic_mitra')->count(),
            'total_kelompok' => User::where('role', 'ketua_kelompok')->count(),
        ];

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('nip_nidn', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('stats', 'users'));
    }

    /**
     * Tampilkan Daftar Akun DPL (Dosen Pembimbing Lapangan).
     */
    public function categoryDpl(Request $request)
    {
        $query = User::where('role', 'dpl');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('nip_nidn', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $totalDpl = User::where('role', 'dpl')->count();

        return view('admin.users.dpl', compact('users', 'totalDpl'));
    }

    /**
     * Tampilkan Daftar Akun PIC Mitra Instansi.
     */
    public function categoryPic(Request $request)
    {
        $query = User::where('role', 'pic_mitra')->with('mitraPic');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhereHas('mitraPic', function ($m) use ($search) {
                      $m->where('nama_mitra', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $totalPic = User::where('role', 'pic_mitra')->count();

        return view('admin.users.pic', compact('users', 'totalPic'));
    }

    /**
     * Tampilkan Daftar Akun Kelompok PPL.
     */
    public function categoryKelompok(Request $request)
    {
        $query = User::where('role', 'ketua_kelompok')->with('kelompokKetua');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhereHas('kelompokKetua', function ($k) use ($search) {
                      $k->where('nama_kelompok', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $totalKelompok = User::where('role', 'ketua_kelompok')->count();

        return view('admin.users.kelompok', compact('users', 'totalKelompok'));
    }

    /**
     * Form Tambah Pengguna Baru.
     */
    public function create(Request $request)
    {
        $defaultRole = $request->query('role', 'dpl');
        return view('admin.users.create', compact('defaultRole'));
    }

    /**
     * Simpan Pengguna Baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'dpl', 'pic_mitra', 'ketua_kelompok'])],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'nip_nidn' => ['nullable', 'string', 'max:30'],
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'nip_nidn' => $request->nip_nidn,
            'must_change_password' => true,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun Pengguna baru berhasil dibuat.');
    }

    /**
     * Form Edit Pengguna.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update Pengguna.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'dpl', 'pic_mitra', 'ketua_kelompok'])],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'nip_nidn' => ['nullable', 'string', 'max:30'],
            'is_active' => ['required', 'boolean'],
            'new_password' => ['nullable', 'string', 'min:8'],
        ]);

        $data = [
            'username' => $request->username,
            'role' => $request->role,
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'nip_nidn' => $request->nip_nidn,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('new_password')) {
            $data['password'] = Hash::make($request->new_password);
            $data['must_change_password'] = true;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data Pengguna berhasil diperbarui.');
    }

    /**
     * Hapus Pengguna (Soft Delete).
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun Pengguna berhasil dihapus.');
    }
}
