<?php

namespace Tests\Feature;

use App\Exports\DplExport;
use App\Exports\MahasiswaExport;
use App\Exports\MitraExport;
use App\Imports\DplImport;
use App\Imports\MahasiswaImport;
use App\Imports\MitraImport;
use App\Models\KelompokPpl;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExcelImportExportRoundtripTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $pic = User::create([
            'username' => 'pic_test',
            'password' => Hash::make('password'),
            'role' => 'pic_mitra',
            'nama_lengkap' => 'PIC Test',
            'no_hp' => '08123456789',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra Test',
            'kategori' => 'SKPD',
            'alamat' => 'Jl. Test No. 1',
            'pic_user_id' => $pic->id,
        ]);

        $dpl = User::create([
            'username' => 'dpl_test',
            'password' => Hash::make('password'),
            'role' => 'dpl',
            'nama_lengkap' => 'DPL Test',
            'nip_nidn' => '198001012005011099',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $ketua = User::create([
            'username' => 'ketua_test',
            'password' => Hash::make('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Kelompok Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $kelompok = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok Test',
            'mitra_id' => $mitra->id,
            'dpl_id' => $dpl->id,
            'ketua_user_id' => $ketua->id,
            'tahun_akademik' => '2025/2026',
            'status' => 'aktif',
        ]);

        Mahasiswa::create([
            'kelompok_id' => $kelompok->id,
            'nim' => '2026000001',
            'nama' => 'Mahasiswa Test',
            'jenis_kelamin' => 'Laki-laki',
            'prodi' => 'Manajemen',
            'konsentrasi' => 'Keuangan',
            'no_hp' => '08123456789',
        ]);
    }

    public function test_mitra_export_and_import_headings_are_matching(): void
    {
        $export = new MitraExport();
        $headings = $export->headings();
        $firstItem = $export->collection()->first();
        $mappedRow = $export->map($firstItem);

        $slugifiedRow = [];
        foreach ($headings as $index => $heading) {
            $slugKey = str_replace([' ', '/', '-'], ['_', '_', '_'], strtolower($heading));
            $slugifiedRow[$slugKey] = $mappedRow[$index];
        }

        $import = new MitraImport();
        $resultModel = $import->model($slugifiedRow);

        $this->assertNotNull($resultModel);
        $this->assertInstanceOf(Mitra::class, $resultModel);
        $this->assertEquals($firstItem->nama_mitra, $resultModel->nama_mitra);
    }

    public function test_dpl_export_and_import_headings_are_matching(): void
    {
        $export = new DplExport();
        $headings = $export->headings();
        $firstItem = $export->collection()->first();
        $mappedRow = $export->map($firstItem);

        $slugifiedRow = [];
        foreach ($headings as $index => $heading) {
            $slugKey = str_replace([' ', '/', '-'], ['_', '_', '_'], strtolower($heading));
            $slugifiedRow[$slugKey] = $mappedRow[$index];
        }

        $import = new DplImport();
        $resultModel = $import->model($slugifiedRow);

        $this->assertNotNull($resultModel);
        $this->assertInstanceOf(User::class, $resultModel);
        $this->assertEquals($firstItem->username, $resultModel->username);
    }

    public function test_mahasiswa_export_and_import_headings_are_matching(): void
    {
        $export = new MahasiswaExport();
        $headings = $export->headings();
        $firstItem = $export->collection()->first();
        $mappedRow = $export->map($firstItem);

        $slugifiedRow = [];
        foreach ($headings as $index => $heading) {
            $slugKey = str_replace([' ', '/', '-'], ['_', '_', '_'], strtolower($heading));
            $slugifiedRow[$slugKey] = $mappedRow[$index];
        }

        $import = new MahasiswaImport();
        $resultModel = $import->model($slugifiedRow);

        $this->assertNotNull($resultModel);
        $this->assertInstanceOf(Mahasiswa::class, $resultModel);
        $this->assertEquals($firstItem->nim, $resultModel->nim);
    }
}
