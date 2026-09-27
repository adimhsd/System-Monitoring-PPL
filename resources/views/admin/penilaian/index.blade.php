@extends('layouts.app')

@section('title', 'Input & Rekap Nilai PPL')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Input & Rekap Nilai PPL Mahasiswa</h4>
        <p class="text-muted mb-0 fs-7">Nilai Akhir = <strong>60% Nilai Mitra</strong> (PIC Mitra) + <strong>40% Nilai Laporan DPL</strong>. Nilai dapat disimpan sebagai Draft atau dikunci (Final).</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.penilaian.export', request()->query()) }}" class="btn btn-outline-success btn-touch rounded-3 fw-semibold">
            📊 Export Rekap Nilai (Excel)
        </a>
        <a href="{{ route('admin.export.nilai.pdf') }}" class="btn btn-outline-danger btn-touch rounded-3 fw-semibold">
            📄 Cetak Rekap PDF
        </a>
        <button type="button" class="btn btn-outline-primary btn-touch rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalGradeScale">
            ⚙️ Skala Nilai Huruf
        </button>
    </div>
</div>

@include('penilaian._errors')

<!-- Ringkasan Statistik Penilaian -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-primary fs-7 fw-bold">Status Nilai Mhs</span>
                <span class="fs-4">👨‍🎓</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['mhs_lengkap'] }} <span class="fs-6 text-muted fw-normal">/ {{ $statsSummary['total_mahasiswa'] }}</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">🔒 Final: {{ $statsSummary['terkunci'] }}</span>
                <span class="text-danger fw-semibold">⏳ Belum: {{ $statsSummary['mhs_belum'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-info fs-7 fw-bold">Total Kelompok PPL</span>
                <span class="fs-4">🏢</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['kelompok_selesai'] }} <span class="fs-6 text-muted fw-normal">/ {{ $statsSummary['total_kelompok'] }}</span>
            </h3>
            <span class="text-muted fs-8">{{ $statsSummary['persen_kelompok'] }}% kelompok selesai dinilai</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-success fs-7 fw-bold">Input Nilai per Sumber</span>
                <span class="fs-4">📝</span>
            </div>
            <div class="fs-7">
                <div class="d-flex justify-content-between"><span>Nilai Mitra (60%)</span><strong>{{ $statsSummary['mitra_terisi'] }} / {{ $statsSummary['total_mahasiswa'] }}</strong></div>
                <div class="d-flex justify-content-between"><span>Nilai DPL (40%)</span><strong>{{ $statsSummary['dpl_terisi'] }} / {{ $statsSummary['total_mahasiswa'] }}</strong></div>
                <div class="d-flex justify-content-between text-muted fs-8"><span>DPL sudah menginput</span><span>{{ $statsSummary['dpl_selesai'] }} / {{ $statsSummary['total_dpl'] }} DPL</span></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-dark fs-7 fw-bold">Rata-Rata Nilai PPL</span>
                <span class="fs-4">📊</span>
            </div>
            <h3 class="fw-bold text-warning mb-1">
                {{ number_format($statsSummary['rata_rata'], 2) }} <span class="fs-7 text-muted fw-normal">/ 100</span>
            </h3>
            <span class="text-muted fs-8">
                M: {{ $statsSummary['prodi']['Manajemen'] }} | A: {{ $statsSummary['prodi']['Akuntansi'] }} | BD: {{ $statsSummary['prodi']['Bisnis Digital'] }}
            </span>
        </div>
    </div>
</div>

<!-- Distribusi Huruf Mutu -->
<div class="card card-custom p-3 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="fw-bold fs-7 text-secondary">🏷️ Distribusi Huruf Mutu Mahasiswa:</span>
        <span class="badge bg-light text-dark border fs-8">Total Terkonversi: {{ array_sum($statsSummary['rekap_huruf']) }} Mahasiswa</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @foreach($statsSummary['rekap_huruf'] as $huruf => $jumlah)
            <a href="{{ route('admin.penilaian.index', ['huruf' => $huruf]) }}" class="d-flex align-items-center bg-light border rounded-pill px-3 py-1 text-decoration-none">
                <span class="badge bg-primary rounded-pill me-2 fs-8">{{ $huruf }}</span>
                <span class="fw-bold text-dark fs-7 me-1">{{ $jumlah }}</span>
                <span class="text-muted fs-8">Mhs</span>
            </a>
        @endforeach
    </div>
</div>

@include('penilaian._standar-huruf')

<!-- Filter -->
<div class="card card-custom p-3 mb-3">
    <form action="{{ route('admin.penilaian.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-3">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama Mahasiswa / NIM..." value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-2">
            <select name="prodi" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Prodi --</option>
                @foreach(['Manajemen' => 'S1 Manajemen', 'Akuntansi' => 'S1 Akuntansi', 'Bisnis Digital' => 'S1 Bisnis Digital'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('prodi') == $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="kelompok_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Kelompok --</option>
                @foreach($kelompokList as $k)
                    <option value="{{ $k->id }}" @selected(request('kelompok_id') == $k->id)>{{ $k->nama_kelompok }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="dpl_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua DPL --</option>
                @foreach($dplList as $d)
                    <option value="{{ $d->id }}" @selected(request('dpl_id') == $d->id)>{{ $d->nama_lengkap }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-1">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Status</option>
                <option value="belum" @selected(request('status') == 'belum')>Belum Lengkap</option>
                <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                <option value="locked" @selected(request('status') == 'locked')>Terkunci</option>
            </select>
        </div>
        <div class="col-6 col-md-1">
            <select name="huruf" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Grade</option>
                @foreach($skalaHuruf as $item)
                    <option value="{{ $item['huruf'] }}" @selected(request('huruf') == $item['huruf'])>{{ $item['huruf'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-1 d-grid">
            <button type="submit" class="btn btn-sm btn-secondary fw-semibold">Filter</button>
        </div>
    </form>
</div>

<!-- Aksi Massal -->
<form id="bulkForm" action="{{ route('admin.penilaian.bulk') }}" method="POST" class="d-flex flex-wrap align-items-center gap-2 mb-3">
    @csrf
    <span class="text-muted fs-8">Aksi untuk mahasiswa terpilih:</span>
    <button type="submit" name="aksi" value="lock" class="btn btn-sm btn-outline-success fw-semibold" onclick="return confirm('Kunci semua nilai terpilih? Nilai yang belum lengkap akan dilewati.')">🔒 Kunci Semua Nilai Terpilih</button>
    <button type="submit" name="aksi" value="unlock" class="btn btn-sm btn-outline-warning fw-semibold" onclick="return confirm('Buka kunci semua nilai terpilih?')">🔓 Buka Kunci Terpilih</button>
</form>

@include('penilaian._tabel', ['role' => 'admin'])
@include('penilaian._modal-input', ['role' => 'admin'])

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
                    <p class="text-muted mb-3">Aturan konversi nilai akhir angka ke huruf mutu fakultas. Perubahan langsung diterapkan ulang ke seluruh nilai.</p>
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
                                        <td><input type="text" name="skala[{{ $index }}][huruf]" class="form-control form-control-sm text-center fw-bold" value="{{ $item['huruf'] }}" required></td>
                                        <td><input type="number" step="0.01" name="skala[{{ $index }}][min]" class="form-control form-control-sm text-center" value="{{ $item['min'] }}" required></td>
                                        <td><input type="number" step="0.01" name="skala[{{ $index }}][max]" class="form-control form-control-sm text-center" value="{{ $item['max'] }}" required></td>
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
