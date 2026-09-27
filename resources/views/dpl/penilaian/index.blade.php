@extends('layouts.app')

@section('title', 'Input & Rekap Nilai — DPL')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Input & Rekap Nilai Mahasiswa Bimbingan</h4>
        <p class="text-muted mb-0 fs-7">Input <strong>Nilai Laporan DPL (40%)</strong>. <strong>Nilai Mitra (60%)</strong> diisi oleh PIC Mitra, lalu Nilai Akhir & Grade terhitung otomatis.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('dpl.penilaian.export', request()->query()) }}" class="btn btn-outline-success btn-touch rounded-3 fw-semibold">
            📊 Export Rekap Nilai (Excel)
        </a>
    </div>
</div>

@include('penilaian._errors')

<!-- Statistik DPL -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <span class="text-primary fs-7 fw-bold">Kelompok Bimbingan</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $statsSummary['total_kelompok'] }}</h3>
            <span class="text-muted fs-8">{{ $statsSummary['kelompok_selesai'] }} kelompok selesai dinilai</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <span class="text-info fs-7 fw-bold">Total Mahasiswa Bimbingan</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $statsSummary['total_mahasiswa'] }}</h3>
            <span class="text-muted fs-8">Nilai DPL terisi: {{ $statsSummary['dpl_terisi'] }} | Nilai Mitra terisi: {{ $statsSummary['mitra_terisi'] }}</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        @php $semuaSelesai = $statsSummary['total_mahasiswa'] > 0 && $statsSummary['mhs_belum'] === 0; @endphp
        <div class="card card-custom p-3 border-start border-4 border-{{ $semuaSelesai ? 'success' : 'warning' }} h-100">
            <span class="text-{{ $semuaSelesai ? 'success' : 'warning' }} fs-7 fw-bold">Sudah Dinilai</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $statsSummary['mhs_lengkap'] }} <span class="fs-6 text-muted fw-normal">/ {{ $statsSummary['total_mahasiswa'] }}</span></h3>
            <span class="text-muted fs-8">Progres: {{ $statsSummary['persen_lengkap'] }}% | 🔒 Final: {{ $statsSummary['terkunci'] }}</span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-{{ $statsSummary['mhs_belum'] === 0 ? 'success' : 'danger' }} h-100">
            <span class="text-{{ $statsSummary['mhs_belum'] === 0 ? 'success' : 'danger' }} fs-7 fw-bold">Belum Dinilai</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $statsSummary['mhs_belum'] }}</h3>
            <span class="text-muted fs-8">{{ $statsSummary['mhs_belum'] === 0 ? 'Semua nilai telah lengkap!' : 'Menunggu input penilaian' }}</span>
        </div>
    </div>
</div>

@include('penilaian._standar-huruf')

@if($kelompokTerpilih)
    <!-- Berkas & Luaran Kelompok sebagai bahan Nilai Laporan -->
    <div class="card card-custom p-3 mb-3 bg-light border">
        <h6 class="fw-bold text-dark mb-2">📁 Luaran {{ $kelompokTerpilih->nama_kelompok }} — {{ $kelompokTerpilih->mitra->nama_mitra ?? '-' }}</h6>
        <div class="d-flex flex-wrap gap-3 fs-7">
            <div>
                <strong>Laporan PDF:</strong>
                @if($kelompokTerpilih->luaran && $kelompokTerpilih->luaran->file_laporan_pdf)
                    <a href="{{ route('luaran.pdf.download', $kelompokTerpilih->luaran) }}" target="_blank" class="text-primary fw-semibold ms-1">📄 Unduh & Periksa PDF Laporan</a>
                @else
                    <span class="text-danger ms-1">⚠️ Kelompok belum mengunggah PDF laporan.</span>
                @endif
            </div>
            <div>
                <strong>Video YouTube:</strong>
                @if($kelompokTerpilih->luaran && $kelompokTerpilih->luaran->url_video)
                    <a href="{{ $kelompokTerpilih->luaran->url_video }}" target="_blank" class="text-danger fw-semibold ms-1">🎬 Tonton Video YouTube</a>
                @else
                    <span class="text-danger ms-1">⚠️ Kelompok belum mengunggah video.</span>
                @endif
            </div>
        </div>
    </div>
@endif

<!-- Filter -->
<div class="card card-custom p-3 mb-3">
    <form action="{{ route('dpl.penilaian.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-3">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama Mahasiswa / NIM..." value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-3">
            <select name="kelompok_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Kelompok Bimbingan --</option>
                @foreach($kelompokList as $k)
                    <option value="{{ $k->id }}" @selected(request('kelompok_id') == $k->id)>{{ $k->nama_kelompok }}</option>
                @endforeach
            </select>
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
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Status Nilai --</option>
                <option value="belum" @selected(request('status') == 'belum')>Belum Lengkap</option>
                <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                <option value="locked" @selected(request('status') == 'locked')>Terkunci (Final)</option>
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
        <div class="col-12 col-md-1 d-grid">
            <button type="submit" class="btn btn-sm btn-secondary fw-semibold">Filter</button>
        </div>
    </form>
</div>

<!-- Aksi Massal -->
<form id="bulkForm" action="{{ route('dpl.penilaian.bulk-lock') }}" method="POST" class="d-flex flex-wrap align-items-center gap-2 mb-3">
    @csrf
    <span class="text-muted fs-8">Aksi untuk mahasiswa terpilih:</span>
    <button type="submit" class="btn btn-sm btn-outline-success fw-semibold" onclick="return confirm('Kunci semua nilai terpilih? Nilai terkunci hanya dapat dibuka oleh Admin. Nilai yang belum lengkap akan dilewati.')">🔒 Kunci Semua Nilai Terpilih</button>
</form>

@include('penilaian._tabel', ['role' => 'dpl'])
@include('penilaian._modal-input', ['role' => 'dpl'])
@endsection
