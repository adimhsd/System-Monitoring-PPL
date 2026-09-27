@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<!-- Metric Cards Top Overview -->
<div class="row g-3 mb-4">
    <!-- Card Total Mahasiswa -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fs-7 fw-semibold">Total Mahasiswa</span>
                    <h2 class="fw-bold mb-0 text-warning">{{ $totalMahasiswa }}</h2>
                    <div class="fs-8 text-muted mt-1">
                        <span class="text-success fw-semibold">{{ $totalMahasiswaPlotting }} Ada Kelompok</span> | 
                        <span class="text-secondary fw-semibold">{{ $totalMahasiswaUnassigned }} Belum</span>
                    </div>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning fs-3">
                    👨‍🎓
                </div>
            </div>
        </div>
    </div>

    <!-- Card Total Kelompok -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fs-7 fw-semibold">Total Kelompok PPL</span>
                    <h2 class="fw-bold mb-0 text-primary">{{ $totalKelompok }}</h2>
                    <div class="fs-8 text-muted mt-1">Kelompok Terdaftar</div>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary fs-3">
                    👥
                </div>
            </div>
        </div>
    </div>

    <!-- Card Total Mitra PPL -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fs-7 fw-semibold">Total Mitra PPL</span>
                    <h2 class="fw-bold mb-0 text-success">{{ $totalMitra }}</h2>
                    <div class="fs-8 text-muted mt-1">Instansi & SKPD</div>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success fs-3">
                    🏢
                </div>
            </div>
        </div>
    </div>

    <!-- Card Total DPL -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted fs-7 fw-semibold">Total DPL Aktif</span>
                    <h2 class="fw-bold mb-0 text-info">{{ $totalDpl }}</h2>
                    <div class="fs-8 text-muted mt-1">Dosen Pembimbing</div>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info fs-3">
                    👨‍🏫
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 2: Ringkasan Rekapitulasi Penilaian PPL & Luaran Akhir PPL -->
<div class="row g-3 mb-4">
    <!-- Card Rekapitulasi Penilaian PPL Mahasiswa -->
    <div class="col-12 col-xl-6">
        <div class="card card-custom p-4 h-100 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">📝 Rekapitulasi Penilaian PPL Mahasiswa</h5>
                    <small class="text-muted">Progres penilaian 60% Mitra + 40% DPL Fakultas</small>
                </div>
                <a href="{{ route('admin.penilaian.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                    Detail Rekap →
                </a>
            </div>

            <!-- Stats Sub-Grid Penilaian -->
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-3">
                    <div class="p-2 bg-light rounded-3 text-center border">
                        <span class="text-muted fs-8 d-block">Sudah Dinilai</span>
                        <strong class="text-success fs-6">{{ $rekapPenilaian['mhs_sudah'] }}</strong>
                        <span class="fs-8 text-muted">/ {{ $rekapPenilaian['total_mhs'] }} Mhs</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 bg-light rounded-3 text-center border">
                        <span class="text-muted fs-8 d-block">Penilaian DPL</span>
                        <strong class="text-info fs-6">{{ $rekapPenilaian['dpl_sudah'] }}</strong>
                        <span class="fs-8 text-muted">/ {{ $rekapPenilaian['total_dpl'] }} DPL</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 bg-light rounded-3 text-center border">
                        <span class="text-muted fs-8 d-block">Penilaian Mitra</span>
                        <strong class="text-success fs-6">{{ $rekapPenilaian['mitra_sudah'] }}</strong>
                        <span class="fs-8 text-muted">/ {{ $rekapPenilaian['total_mitra'] }} Mitra</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 bg-warning bg-opacity-10 rounded-3 text-center border border-warning border-opacity-20">
                        <span class="text-dark fs-8 d-block fw-semibold">Rata-Rata</span>
                        <strong class="text-warning fs-6">{{ $rekapPenilaian['rata_rata'] }}</strong>
                        <span class="fs-8 text-muted">/ 100</span>
                    </div>
                </div>
            </div>

            <!-- Distribusi Huruf Mutu Badges -->
            <div class="pt-2 border-top">
                <span class="text-muted fs-8 fw-semibold d-block mb-2">Distribusi Mutu Nilai (A - E):</span>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($rekapPenilaian['huruf'] as $huruf => $jumlah)
                        <div class="d-flex align-items-center bg-white border rounded-pill px-2 py-1 fs-8">
                            <span class="badge bg-primary me-1">{{ $huruf }}</span>
                            <span class="fw-bold text-dark">{{ $jumlah }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Card Rekapitulasi Luaran Akhir PPL -->
    <div class="col-12 col-xl-6">
        <div class="card card-custom p-4 h-100 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">📂 Rekapitulasi Luaran Akhir PPL Fakultas</h5>
                    <small class="text-muted">Progres pengumpulan Laporan PDF & Video YouTube</small>
                </div>
                <a href="{{ route('admin.luaran.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                    Detail Luaran →
                </a>
            </div>

            <!-- Stats Sub-Grid Luaran -->
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-3">
                    <div class="p-2 bg-light rounded-3 text-center border">
                        <span class="text-muted fs-8 d-block">Luaran Lengkap</span>
                        <strong class="text-success fs-6">{{ $rekapLuaran['luaran_lengkap'] }}</strong>
                        <span class="fs-8 text-muted">/ {{ $rekapLuaran['total_kelompok'] }} Klp</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 bg-light rounded-3 text-center border">
                        <span class="text-muted fs-8 d-block">PDF Terkumpul</span>
                        <strong class="text-primary fs-6">{{ $rekapLuaran['pdf_terkumpul'] }}</strong>
                        <span class="fs-8 text-muted">/ {{ $rekapLuaran['total_kelompok'] }} PDF</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 bg-light rounded-3 text-center border">
                        <span class="text-muted fs-8 d-block">Video YouTube</span>
                        <strong class="text-danger fs-6">{{ $rekapLuaran['video_terkumpul'] }}</strong>
                        <span class="fs-8 text-muted">/ {{ $rekapLuaran['total_kelompok'] }} Link</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2 bg-success bg-opacity-10 rounded-3 text-center border border-success border-opacity-20">
                        <span class="text-success fs-8 d-block fw-semibold">Progres</span>
                        <strong class="text-success fs-6">{{ $rekapLuaran['persentase'] }}%</strong>
                        <span class="fs-8 text-muted">Kelengkapan</span>
                    </div>
                </div>
            </div>

            <!-- Visual Progress Bar -->
            <div class="pt-2 border-top">
                <div class="d-flex justify-content-between text-muted fs-8 mb-1">
                    <span>Persentase Kelengkapan Pengumpulan Luaran Wajib</span>
                    <strong class="text-dark">{{ $rekapLuaran['persentase'] }}%</strong>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $rekapLuaran['persentase'] }}%;" aria-valuenow="{{ $rekapLuaran['persentase'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Widget Monitoring Keterlambatan Logbook (> 24 Jam) -->
<div class="card card-custom p-4 mb-4 border-start border-4 border-danger">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">⚠️ Pemantauan Keterlambatan Logbook Harian</h5>
            <p class="text-muted fs-7 mb-0">Kelompok PPL aktif yang belum menginputkan logbook untuk hari ini ({{ date('d F Y') }}):</p>
        </div>
        <span class="badge bg-warning text-dark px-3 py-2 fs-7">{{ $kelompokBelumIsiLogbook->count() }} Kelompok</span>
    </div>

    @if($kelompokBelumIsiLogbook->isEmpty())
        <div class="alert alert-success mb-0 fs-7">
            🎉 Hebat! Seluruh kelompok PPL aktif sudah menginputkan logbook kegiatan harian hari ini.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle fs-7 mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th>Kelompok</th>
                        <th>Mitra Penempatan</th>
                        <th>DPL Pembimbing</th>
                        <th>Ketua Kelompok</th>
                        <th class="text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelompokBelumIsiLogbook as $k)
                        <tr>
                            <td class="fw-bold text-dark">{{ $k->nama_kelompok }}</td>
                            <td>{{ $k->mitra->nama_mitra ?? '-' }}</td>
                            <td>{{ $k->dpl->nama_lengkap ?? '-' }}</td>
                            <td>{{ $k->ketua->nama_lengkap ?? '-' }} ({{ $k->ketua->no_hp ?? '-' }})</td>
                            <td class="text-end">
                                <span class="badge bg-warning text-dark">Belum Isi Logbook</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
