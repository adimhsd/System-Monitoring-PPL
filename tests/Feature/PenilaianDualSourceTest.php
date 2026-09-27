<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\PenilaianPpl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestPplData;

class PenilaianDualSourceTest extends TestCase
{
    use RefreshDatabase, CreatesTestPplData;

    protected User $dpl;
    protected User $pic;
    protected KelompokPpl $kelompok;
    protected AnggotaKelompok $mhs;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->createTestPplData();

        $this->dpl = $this->dplUser;
        $this->pic = $this->picUser;
        $this->kelompok = $this->testKelompok;

        // Ensure this kelompok is linked to this DPL and PIC
        $this->kelompok->update([
            'dpl_id' => $this->dpl->id,
            'mitra_id' => $this->testMitra->id,
        ]);

        $this->mhs = AnggotaKelompok::where('kelompok_id', $this->kelompok->id)->first() ?? AnggotaKelompok::create([
            'kelompok_id' => $this->kelompok->id,
            'nim' => '2026000001',
            'nama' => 'Mahasiswa Test',
            'prodi' => 'Bisnis Digital',
            'konsentrasi' => 'Pemasaran Digital',
        ]);
    }

    public function test_pic_mitra_can_input_mitra_scores_sixty_percent(): void
    {
        $response = $this->actingAs($this->pic)->post("/pic/penilaian/{$this->kelompok->id}", [
            'nilai' => [
                $this->mhs->id => [
                    'kedisiplinan' => 90,
                    'etika' => 85,
                    'kerjasama' => 95,
                    'hasil_kerja' => 90,
                    'catatan' => 'Sangat baik.',
                ],
            ],
        ]);

        $response->assertRedirect('/pic/penilaian');

        // Average Mitra score = (90 + 85 + 95 + 90) / 4 = 90.00
        $this->assertDatabaseHas('penilaian_ppl', [
            'anggota_kelompok_id' => $this->mhs->id,
            'total_nilai_mitra' => 90.00,
        ]);
    }

    public function test_dpl_can_input_dpl_scores_forty_percent_and_calculate_final_grade(): void
    {
        // 1. First, Mitra evaluates student: 90.00
        $this->actingAs($this->pic)->post("/pic/penilaian/{$this->kelompok->id}", [
            'nilai' => [
                $this->mhs->id => [
                    'kedisiplinan' => 90,
                    'etika' => 90,
                    'kerjasama' => 90,
                    'hasil_kerja' => 90,
                ],
            ],
        ]);

        // 2. Next, DPL evaluates student: 80.00
        $response = $this->actingAs($this->dpl)->put("/dpl/penilaian/{$this->kelompok->id}", [
            'nilai' => [
                $this->mhs->id => [
                    'kedisiplinan' => 80,
                    'etika' => 80,
                    'kerjasama' => 80,
                    'hasil_kerja' => 80,
                ],
            ],
        ]);

        $response->assertRedirect("/dpl/penilaian/{$this->kelompok->id}/edit");

        // Combined Final Score = (90 * 0.60) + (80 * 0.40) = 54 + 32 = 86.00
        // Scale: 81.0 - 100.0 => A
        $this->assertDatabaseHas('penilaian_ppl', [
            'anggota_kelompok_id' => $this->mhs->id,
            'total_nilai_mitra' => 90.00,
            'total_nilai_dpl' => 80.00,
            'nilai_huruf' => 'A',
        ]);
    }

    public function test_grade_conversion_rules_according_to_user_standard(): void
    {
        $this->assertEquals('A', PenilaianPpl::konversiNilaiHuruf(85.00));
        $this->assertEquals('AB', PenilaianPpl::konversiNilaiHuruf(77.50));
        $this->assertEquals('B', PenilaianPpl::konversiNilaiHuruf(72.00));
        $this->assertEquals('BC', PenilaianPpl::konversiNilaiHuruf(65.00));
        $this->assertEquals('C', PenilaianPpl::konversiNilaiHuruf(60.00));
        $this->assertEquals('CD', PenilaianPpl::konversiNilaiHuruf(53.50));
        $this->assertEquals('D', PenilaianPpl::konversiNilaiHuruf(48.00));
        $this->assertEquals('E', PenilaianPpl::konversiNilaiHuruf(30.00));
    }

    public function test_admin_can_update_grade_scale_config(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/penilaian/scale', [
            'skala' => [
                ['min' => 81.00, 'max' => 100.00, 'huruf' => 'A'],
                ['min' => 75.00, 'max' => 80.99,  'huruf' => 'AB'],
                ['min' => 69.00, 'max' => 74.99,  'huruf' => 'B'],
                ['min' => 63.00, 'max' => 68.99,  'huruf' => 'BC'],
                ['min' => 57.00, 'max' => 62.99,  'huruf' => 'C'],
                ['min' => 51.00, 'max' => 56.99,  'huruf' => 'CD'],
                ['min' => 45.00, 'max' => 50.99,  'huruf' => 'D'],
                ['min' => 0.00,  'max' => 44.99,  'huruf' => 'E'],
            ],
        ]);

        $response->assertRedirect('/admin/penilaian');
    }
}
