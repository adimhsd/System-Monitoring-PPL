<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MitraExport;
use App\Exports\MitraTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\MitraImport;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class MitraController extends Controller
{
    /**
     * Tampilkan daftar Master Mitra.
     */
    public function index(Request $request)
    {
        $query = Mitra::with('picUser');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mitra', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $mitraList = $query->latest()->paginate(20)->withQueryString();

        // Ringkasan Statistik Data Mitra
        $statsSummary = [
            'total_mitra' => Mitra::count(),
            'mitra_skpd' => Mitra::where('kategori', 'SKPD')->count(),
            'mitra_swasta' => Mitra::where('kategori', 'Swasta')->count(),
            'mitra_umkm' => Mitra::where('kategori', 'UMKM')->count(),
            'mitra_ber_pic' => Mitra::whereNotNull('pic_user_id')->count(),
            'mitra_tanpa_pic' => Mitra::whereNull('pic_user_id')->count(),
            'mitra_terplot' => Mitra::whereHas('kelompokPpl')->count(),
            'mitra_standby' => Mitra::doesntHave('kelompokPpl')->count(),
            'mitra_dengan_wa' => Mitra::whereHas('picUser', function ($q) {
                $q->whereNotNull('no_hp')->where('no_hp', '!=', '-')->where('no_hp', '!=', '');
            })->count(),
            'mitra_dengan_alamat' => Mitra::whereNotNull('alamat')->where('alamat', '!=', '-')->where('alamat', '!=', '')->count(),
        ];

        return view('admin.mitra.index', compact('mitraList', 'statsSummary'));
    }

    /**
     * Form Tambah Mitra Baru.
     */
    public function create()
    {
        return view('admin.mitra.create');
    }

    /**
     * Simpan Data Mitra Baru beserta Akun PIC Mitra.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_mitra' => ['required', 'string', 'max:150'],
            'kategori' => ['required', Rule::in(['SKPD', 'Swasta', 'UMKM'])],
            'alamat' => ['nullable', 'string'],
            'pic_nama' => ['required', 'string', 'max:100'],
            'pic_username' => ['nullable', 'string', 'max:50'],
            'pic_password' => ['nullable', 'string', 'min:6'],
            'pic_no_hp' => ['nullable', 'string', 'max:20'],
        ], [
            'nama_mitra.required' => 'Nama mitra instansi wajib diisi.',
            'pic_nama.required' => 'Nama PIC Mitra wajib diisi.',
            'pic_password.min' => 'Password PIC minimal harus 6 karakter.',
        ]);

        $usernamePic = trim((string) $request->pic_username);
        if (empty($usernamePic)) {
            $usernamePic = 'pic_' . Str::slug($request->nama_mitra, '_');
        }

        // Ensure username is unique for the new PIC user
        $originalUsername = $usernamePic;
        $counter = 1;
        while (User::withTrashed()->where('username', $usernamePic)->exists()) {
            $usernamePic = $originalUsername . '_' . $counter;
            $counter++;
        }

        $passwordInput = !empty($request->pic_password) ? $request->pic_password : 'password123';

        // Create PIC User Account
        $picUser = User::create([
            'username' => $usernamePic,
            'password' => Hash::make($passwordInput),
            'role' => 'pic_mitra',
            'nama_lengkap' => $request->pic_nama,
            'no_hp' => $request->pic_no_hp,
            'must_change_password' => true,
            'is_active' => true,
        ]);

        // Create Mitra Record
        Mitra::create([
            'nama_mitra' => $request->nama_mitra,
            'kategori' => $request->kategori,
            'alamat' => $request->alamat,
            'pic_user_id' => $picUser->id,
        ]);

        return redirect()->route('admin.mitra.index')
            ->with('success', 'Data Mitra baru beserta Akun PIC berhasil ditambahkan.');
    }

    /**
     * Form Edit Mitra.
     */
    public function edit(Mitra $mitra)
    {
        $mitra->load('picUser');

        return view('admin.mitra.edit', compact('mitra'));
    }

    /**
     * Update Data Mitra & Akun PIC Mitra.
     */
    public function update(Request $request, Mitra $mitra)
    {
        $request->validate([
            'nama_mitra' => ['required', 'string', 'max:150'],
            'kategori' => ['required', Rule::in(['SKPD', 'Swasta', 'UMKM'])],
            'alamat' => ['nullable', 'string'],
            'pic_nama' => ['required', 'string', 'max:100'],
            'pic_username' => ['required', 'string', 'max:50'],
            'pic_password' => ['nullable', 'string', 'min:6'],
            'pic_no_hp' => ['nullable', 'string', 'max:20'],
        ], [
            'nama_mitra.required' => 'Nama mitra instansi wajib diisi.',
            'pic_nama.required' => 'Nama PIC Mitra wajib diisi.',
            'pic_username.required' => 'Username login PIC wajib diisi.',
        ]);

        $mitra->load('picUser');
        $picUser = $mitra->picUser;

        if ($picUser) {
            // Update existing PIC account
            $usernameTaken = User::withTrashed()
                ->where('username', $request->pic_username)
                ->where('id', '!=', $picUser->id)
                ->exists();

            $picPayload = [
                'nama_lengkap' => $request->pic_nama,
                'no_hp' => $request->pic_no_hp,
            ];

            if (!$usernameTaken) {
                $picPayload['username'] = $request->pic_username;
            }

            if ($request->filled('pic_password')) {
                $picPayload['password'] = Hash::make($request->pic_password);
            }

            $picUser->update($picPayload);
        } else {
            // Create new PIC user if missing
            $usernamePic = $request->pic_username;
            $originalUsername = $usernamePic;
            $counter = 1;
            while (User::withTrashed()->where('username', $usernamePic)->exists()) {
                $usernamePic = $originalUsername . '_' . $counter;
                $counter++;
            }

            $passwordInput = !empty($request->pic_password) ? $request->pic_password : 'password123';

            $picUser = User::create([
                'username' => $usernamePic,
                'password' => Hash::make($passwordInput),
                'role' => 'pic_mitra',
                'nama_lengkap' => $request->pic_nama,
                'no_hp' => $request->pic_no_hp,
                'must_change_password' => true,
                'is_active' => true,
            ]);

            $mitra->pic_user_id = $picUser->id;
        }

        $mitra->update([
            'nama_mitra' => $request->nama_mitra,
            'kategori' => $request->kategori,
            'alamat' => $request->alamat,
            'pic_user_id' => $picUser->id ?? $mitra->pic_user_id,
        ]);

        return redirect()->route('admin.mitra.index')
            ->with('success', 'Data Mitra & Akun PIC berhasil diperbarui.');
    }

    /**
     * Hapus Mitra (Soft Delete).
     */
    public function destroy(Mitra $mitra)
    {
        $mitra->delete();

        return redirect()->route('admin.mitra.index')
            ->with('success', 'Data Mitra berhasil dihapus.');
    }

    /**
     * Export Excel Data Mitra.
     */
    public function exportExcel()
    {
        return Excel::download(new MitraExport, 'Master_Data_Mitra_PPL.xlsx');
    }

    /**
     * Download Template Excel Import Mitra.
     */
    public function downloadTemplate()
    {
        return Excel::download(new MitraTemplateExport, 'Template_Import_Mitra_PPL.xlsx');
    }

    /**
     * Import Excel Data Mitra.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'file_excel.max' => 'Ukuran file Excel maksimal 5MB.',
        ]);

        try {
            Excel::import(new MitraImport, $request->file('file_excel'));

            return redirect()->route('admin.mitra.index')
                ->with('success', 'Data Mitra Instansi dari file Excel berhasil diimpor ke sistem.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.mitra.index')
                ->with('error', 'Gagal mengimpor file Excel Mitra: ' . $e->getMessage());
        }
    }
}
