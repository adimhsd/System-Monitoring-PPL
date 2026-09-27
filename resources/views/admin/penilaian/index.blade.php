@extends('layouts.app')

@section('title', 'Rekapitulasi Nilai PPL Mahasiswa')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Rekapitulasi Nilai PPL Mahasiswa Fakultas</h4>
        <p class="text-muted mb-0 fs-7">Kalkulasi nilai per mahasiswa: <strong>60% Nilai Mitra + 40% Nilai DPL</strong> & Konversi Nilai Huruf.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.export.nilai.pdf') }}" class="btn btn-outline-danger btn-touch rounded-3 fw-semibold">
            📄 Cetak Rekap PDF
        </a>
        <a href="{{ route('admin.export.nilai.excel') }}" class="btn btn-outline-success btn-touch rounded-3 fw-semibold">
            📊 Export Excel
        </a>
        <button type="button" class="btn btn-outline-primary btn-touch rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalGradeScale">
            ⚙️ Skala Nilai Huruf
        </button>
    </div>
</div>

<!-- Ringkasan Statistik Penilaian Header Cards -->
<div class="row g-3 mb-4">
    <!-- Stat Card 1: Status Nilai Mahasiswa -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-primary fs-7 fw-bold">Status Nilai Mhs</span>
                <span class="fs-4">👨‍🎓</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['mhs_sudah_lengkap'] }} <span class="fs-6 text-muted font-normal">/ {{ $statsSummary['total_mahasiswa'] }}</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">✅ Lengkap: {{ $statsSummary['mhs_sudah_lengkap'] }}</span>
                <span class="text-danger fw-semibold">⏳ Belum: {{ $statsSummary['mhs_belum'] }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 2: Penilaian DPL -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-info fs-7 fw-bold">Penilaian DPL</span>
                <span class="fs-4">👨‍🏫</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['dpl_sudah'] }} <span class="fs-6 text-muted font-normal">/ {{ $statsSummary['total_dpl'] }} DPL</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">✅ Input: {{ $statsSummary['dpl_sudah'] }} DPL</span>
                <span class="text-danger fw-semibold">⏳ Belum: {{ $statsSummary['dpl_belum'] }} DPL</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 3: Penilaian PIC Mitra -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-success fs-7 fw-bold">Penilaian PIC Mitra</span>
                <span class="fs-4">🏢</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['mitra_sudah'] }} <span class="fs-6 text-muted font-normal">/ {{ $statsSummary['total_mitra'] }} Mitra</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">✅ Input: {{ $statsSummary['mitra_sudah'] }} Mitra</span>
                <span class="text-danger fw-semibold">⏳ Belum: {{ $statsSummary['mitra_belum'] }} Mitra</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 4: Rata-Rata Nilai Akhir -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-dark fs-7 fw-bold">Rata-Rata Nilai PPL</span>
                <span class="fs-4">📊</span>
            </div>
            <h3 class="fw-bold text-warning mb-1">
                {{ $statsSummary['rata_rata_nilai'] }} <span class="fs-7 text-muted font-normal">/ 100</span>
            </h3>
            <span class="text-muted fs-8">Dari {{ $statsSummary['mhs_sudah_lengkap'] }} Mahasiswa Dinilai</span>
        </div>
    </div>
</div>

<!-- Rekapitulasi Distribusi Nilai Huruf Mutu -->
<div class="card card-custom p-3 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="fw-bold fs-7 text-secondary">🏷️ Distribusi Huruf Mutu Mahasiswa (Rekap Sistem):</span>
        <span class="badge bg-light text-dark border fs-8">Total Terkonversi: {{ array_sum($statsSummary['rekap_huruf']) }} Mahasiswa</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @foreach($statsSummary['rekap_huruf'] as $huruf => $jumlah)
            <div class="d-flex align-items-center bg-light border rounded-pill px-3 py-1">
                <span class="badge bg-primary rounded-pill me-2 fs-8">{{ $huruf }}</span>
                <span class="fw-bold text-dark fs-7 me-1">{{ $jumlah }}</span>
                <span class="text-muted fs-8">Mhs</span>
            </div>
        @endforeach
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('admin.penilaian.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama Mahasiswa / NIM..." value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-3">
            <select name="prodi" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Prodi --</option>
                <option value="Manajemen" {{ request('prodi') == 'Manajemen' ? 'selected' : '' }}>Manajemen</option>
                <option value="Akuntansi" {{ request('prodi') == 'Akuntansi' ? 'selected' : '' }}>Akuntansi</option>
                <option value="Bisnis Digital" {{ request('prodi') == 'Bisnis Digital' ? 'selected' : '' }}>Bisnis Digital</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="kelompok_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Kelompok --</option>
                @foreach($kelompokList as $k)
                    <option value="{{ $k->id }}" {{ request('kelompok_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelompok }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-grid">
            <button type="submit" class="btn btn-sm btn-secondary fw-semibold">Filter Data</button>
        </div>
    </form>
</div>

<!-- Table Rekapitulasi Nilai Per Mahasiswa -->
<div class="card card-custom overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-4">NIM & Nama Mahasiswa</th>
                    <th>Kelompok & Mitra</th>
                    <th>Nilai Mitra (60%)</th>
                    <th>Nilai DPL (40%)</th>
                    <th>Nilai Akhir (100%)</th>
                    <th class="text-end pe-4">Grade Huruf</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswaList as $mhs)
                    @php
                        $p = $mhs->penilaian;
                        $nilaiMitra = $p ? $p->total_nilai_mitra : null;
                        $nilaiDpl = $p ? $p->total_nilai_dpl : null;
                        $nilaiAkhir = ($nilaiMitra !== null && $nilaiDpl !== null) ? round(($nilaiMitra * 0.60) + ($nilaiDpl * 0.40), 2) : null;
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $mhs->nama }}</div>
                            <div class="text-muted fs-8">NIM: <code>{{ $mhs->nim }}</code> ({{ $mhs->prodi }})</div>
                        </td>
                        <td>
                            @if($mhs->kelompok)
                                <div class="fw-semibold text-dark">{{ $mhs->kelompok->nama_kelompok }}</div>
                                <div class="text-muted fs-8">🏢 {{ $mhs->kelompok->mitra->nama_mitra ?? '-' }}</div>
                            @else
                                <span class="badge bg-light text-muted border">Belum Ada Kelompok</span>
                            @endif
                        </td>
                        <td>
                            @if($nilaiMitra !== null)
                                <span class="fw-bold text-success">{{ $nilaiMitra }}</span>
                                <span class="fs-8 text-muted">({{ round($nilaiMitra * 0.60, 2) }})</span>
                            @else
                                <span class="badge bg-light text-muted border">Belum Ada</span>
                            @endif
                        </td>
                        <td>
                            @if($nilaiDpl !== null)
                                <span class="fw-bold text-primary">{{ $nilaiDpl }}</span>
                                <span class="fs-8 text-muted">({{ round($nilaiDpl * 0.40, 2) }})</span>
                            @else
                                <span class="badge bg-light text-muted border">Belum Ada</span>
                            @endif
                        </td>
                        <td>
                            @if($nilaiAkhir !== null)
                                <strong class="fs-6 text-dark">{{ $nilaiAkhir }}</strong>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($p && $p->nilai_huruf)
                                <span class="badge bg-primary fs-6 px-3 py-1 fw-bold">{{ $p->nilai_huruf }}</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border">Belum Lengkap</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data nilai mahasiswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3 px-1">
    <div class="text-muted fs-7">
        Menampilkan <strong>{{ $mahasiswaList->firstItem() ?? 0 }}</strong> – <strong>{{ $mahasiswaList->lastItem() ?? 0 }}</strong> dari <strong>{{ $mahasiswaList->total() }}</strong> Mahasiswa
    </div>
    <div>
        {{ $mahasiswaList->links() }}
    </div>
</div>

<!-- Modal Konfigurasi Skala Nilai Huruf -->
<div class="modal fade" id="modalGradeScale" tabindex="-1" aria-labelledby="modalGradeScaleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-custom">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalGradeScaleLabel">Konfigurasi Skala Nilai Huruf</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.penilaian.scale.update') }}" method="POST">
                @csrf
                <div class="modal-body fs-7">
                    <p class="text-muted mb-3">Aturan konversi nilai akhir angka ke huruf fakultas saat ini:</p>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle text-center mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nilai Huruf</th>
                                    <th>Batas Min</th>
                                    <th>Batas Max</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($skalaHuruf as $index => $item)
                                    <tr>
                                        <td>
                                            <input type="text" name="skala[{{ $index }}][huruf]" class="form-control form-control-sm text-center fw-bold" value="{{ $item['huruf'] }}" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="skala[{{ $index }}][min]" class="form-control form-control-sm text-center" value="{{ $item['min'] }}" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="skala[{{ $index }}][max]" class="form-control form-control-sm text-center" value="{{ $item['max'] }}" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Simpan & Terapkan Ulang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
