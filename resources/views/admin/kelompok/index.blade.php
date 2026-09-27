@extends('layouts.app')

@section('title', 'Data Kelompok PPL')

@section('content')
<div class="mb-4">
    <div class="mb-3">
        <h4 class="fw-bold mb-1">Data Master & Akun Kelompok PPL</h4>
        <p class="text-muted mb-0 fs-7">Kelola akun kelompok mandiri (kredensial login) yang digunakan oleh seluruh mahasiswa anggota kelompok PPL, serta fitur ekspor/impor Excel.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.kelompok.create') }}" class="btn btn-primary btn-touch text-white rounded-3 fw-semibold">
            + Buat Akun Kelompok
        </a>
        <a href="{{ route('admin.plotting.index') }}" class="btn btn-outline-info btn-touch rounded-3 fw-semibold">
            🗺️ Plotting Kelompok
        </a>
        <button type="button" class="btn btn-outline-success btn-touch rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalImportExcelKelompok">
            📥 Impor Excel
        </button>
        <a href="{{ route('admin.kelompok.export') }}" class="btn btn-outline-secondary btn-touch rounded-3 fw-semibold">
            📊 Export Excel
        </a>
    </div>
</div>

<!-- Ringkasan Statistik Data Kelompok PPL Header Cards -->
<div class="row g-3 mb-4">
    <!-- Stat Card 1: Total Kelompok & Status -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-primary fs-7 fw-bold">Total Kelompok PPL</span>
                <span class="fs-4">👥</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['total_kelompok'] }} <span class="fs-6 text-muted font-normal">Kelompok</span>
            </h3>
            <div class="d-flex justify-content-between fs-8 text-secondary">
                <span>🟢 Aktif: {{ $statsSummary['kelompok_aktif'] }}</span>
                <span>🏁 Selesai: {{ $statsSummary['kelompok_selesai'] }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 2: Penugasan DPL -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-success fs-7 fw-bold">Penugasan DPL Pembimbing</span>
                <span class="fs-4">👨‍🏫</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['kelompok_ber_dpl'] }} <span class="fs-6 text-muted font-normal">Ber-DPL</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">✅ Ber-DPL: {{ $statsSummary['kelompok_ber_dpl'] }}</span>
                <span class="text-warning fw-semibold">⚠️ Tanpa DPL: {{ $statsSummary['kelompok_tanpa_dpl'] }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 3: Penugasan Mitra -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-info fs-7 fw-bold">Penempatan Mitra</span>
                <span class="fs-4">🏢</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['kelompok_ber_mitra'] }} <span class="fs-6 text-muted font-normal">Ber-Mitra</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-info fw-semibold">📌 Ber-Mitra: {{ $statsSummary['kelompok_ber_mitra'] }}</span>
                <span class="text-secondary fw-semibold">⏳ Belum: {{ $statsSummary['kelompok_tanpa_mitra'] }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 4: Total Anggota & Kelengkapan Plotting -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-warning fs-7 fw-bold">Kelengkapan Plotting</span>
                <span class="fs-4">🎓</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['total_anggota'] }} <span class="fs-6 text-muted font-normal">Mahasiswa</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">⭐ Plot Lengkap: {{ $statsSummary['kelompok_lengkap'] }}</span>
                <span class="text-primary fw-semibold">🎓 Total Anggota: {{ $statsSummary['total_anggota'] }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('admin.kelompok.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama Kelompok / Username Login..." value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-3">
            <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Tahun --</option>
                <option value="2026/2027" {{ request('tahun') == '2026/2027' ? 'selected' : '' }}>2026/2027</option>
                <option value="2025/2026" {{ request('tahun') == '2025/2026' ? 'selected' : '' }}>2025/2026</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Status --</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-grid">
            <button type="submit" class="btn btn-sm btn-secondary fw-semibold">Filter Data</button>
        </div>
    </form>
</div>

<!-- Table Data Kelompok -->
<div class="card card-custom overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-4">Nama Kelompok</th>
                    <th>Username Akun</th>
                    <th>Mitra Penempatan</th>
                    <th>DPL Pembimbing</th>
                    <th>Jumlah Anggota</th>
                    <th>Status Plotting</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelompokList as $k)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">👥 {{ $k->nama_kelompok }}</div>
                            <div class="text-muted fs-8">Tahun: {{ $k->tahun_akademik }}</div>
                        </td>
                        <td>
                            <code>{{ $k->ketua->username ?? '-' }}</code>
                        </td>
                        <td>
                            @if($k->mitra)
                                <div class="fw-semibold text-dark">{{ $k->mitra->nama_mitra }}</div>
                            @else
                                <span class="badge bg-light text-muted border">Belum Diplotkan</span>
                            @endif
                        </td>
                        <td>
                            @if($k->dpl)
                                <div class="fw-semibold text-dark">{{ $k->dpl->nama_lengkap }}</div>
                            @else
                                <span class="badge bg-light text-muted border">Belum Diplotkan</span>
                            @endif
                        </td>
                        <td>
                            @php $count = $k->anggota->count(); @endphp
                            @if($count > 15)
                                <span class="badge bg-danger px-3 py-1">🔥 {{ $count }} Mahasiswa</span>
                            @elseif($count > 10)
                                <span class="badge bg-warning text-dark px-3 py-1">⚠️ {{ $count }} Mahasiswa</span>
                            @elseif($count > 0)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-1">🟢 {{ $count }} Mahasiswa</span>
                            @else
                                <span class="badge bg-light text-muted border">0 Mahasiswa</span>
                            @endif
                        </td>
                        <td>
                            @if($k->mitra && $k->dpl && $k->anggota->count() > 0)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20">Lengkap</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20">Belum Lengkap</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.kelompok.logbook.pdf', $k) }}" class="btn btn-sm btn-outline-danger" title="Cetak Laporan Logbook PDF">
                                    📄 Logbook PDF
                                </a>
                                <a href="{{ route('admin.kelompok.show', $k) }}" class="btn btn-sm btn-outline-info">
                                    👁️ Detail
                                </a>
                                <a href="{{ route('admin.kelompok.edit', $k) }}" class="btn btn-sm btn-outline-primary">
                                    🔑 Akun & Kredensial
                                </a>
                                <a href="{{ route('admin.plotting.edit', $k) }}" class="btn btn-sm btn-outline-success">
                                    🗺️ Plotting
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada kelompok PPL yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3 px-1">
    <div class="text-muted fs-7">
        Menampilkan <strong>{{ $kelompokList->firstItem() ?? 0 }}</strong> – <strong>{{ $kelompokList->lastItem() ?? 0 }}</strong> dari <strong>{{ $kelompokList->total() }}</strong> Kelompok
    </div>
    <div>
        {{ $kelompokList->links() }}
    </div>
</div>

<!-- Modal Impor Excel Kelompok -->
<div class="modal fade" id="modalImportExcelKelompok" tabindex="-1" aria-labelledby="modalImportExcelKelompokLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-custom">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalImportExcelKelompokLabel">📥 Impor Data Kelompok PPL dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kelompok.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body fs-7">
                    <p class="text-muted mb-2">Unggah berkas Excel (<code>.xlsx</code>, <code>.xls</code>, atau <code>.csv</code>) sesuai dengan format template resmi Kelompok PPL.</p>

                    <div class="mb-3">
                        <a href="{{ route('admin.kelompok.template') }}" class="btn btn-sm btn-light border text-primary fw-semibold w-100 py-2">
                            📥 Download Template Resmi Impor Kelompok (.xlsx)
                        </a>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border fs-8">
                        <strong class="d-block mb-1 text-dark">📋 Urutan Kolom Template Impor Kelompok:</strong>
                        <ol class="mb-0 ps-3 text-secondary">
                            <li><code>ID Kelompok</code> (Dikosongkan saat tambah data baru)</li>
                            <li><code>Nama Kelompok</code> (Wajib)</li>
                            <li><code>Tahun Akademik</code> (Contoh: <code>2026/2027</code>)</li>
                            <li><code>Status Kelompok</code> (<code>aktif</code> / <code>selesai</code>)</li>
                            <li><code>Username Ketua</code> (Username akun login kelompok)</li>
                            <li><code>Password Ketua</code> (Default: <code>password123</code> jika dikosongkan)</li>
                            <li><code>Nama DPL</code> (Opsional, dicari berdasarkan NIP/Nama)</li>
                            <li><code>Nama Mitra</code> (Opsional, dicari berdasarkan Nama Mitra)</li>
                        </ol>
                    </div>

                    <div class="mb-3">
                        <label for="file_excel_kelompok" class="form-label fw-semibold text-secondary">Pilih Berkas Excel:</label>
                        <input type="file" name="file_excel" id="file_excel_kelompok" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold">Unggah & Impor Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
