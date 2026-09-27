@extends('layouts.app')

@section('title', 'Master Data Mitra')

@section('content')
<div class="mb-4">
    <div class="mb-3">
        <h4 class="fw-bold mb-1">Master Data Mitra PPL</h4>
        <p class="text-muted mb-0 fs-7">Kelola instansi SKPD, perusahaan swasta, dan UMKM tempat magang mahasiswa, serta fitur ekspor/impor Excel.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.mitra.create') }}" class="btn btn-primary btn-touch text-white rounded-3 fw-semibold">
            + Tambah Mitra Baru
        </a>
        <button type="button" class="btn btn-outline-success btn-touch rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalImportExcelMitra">
            📥 Impor Excel
        </button>
        <a href="{{ route('admin.mitra.export') }}" class="btn btn-outline-secondary btn-touch rounded-3 fw-semibold">
            📊 Export Excel
        </a>
    </div>
</div>

<!-- Ringkasan Statistik Data Mitra Header Cards -->
<div class="row g-3 mb-4">
    <!-- Stat Card 1: Total Mitra & Kategori -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-primary fs-7 fw-bold">Total Mitra Instansi</span>
                <span class="fs-4">🏢</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['total_mitra'] }} <span class="fs-6 text-muted font-normal">Mitra</span>
            </h3>
            <div class="d-flex justify-content-between fs-8 text-secondary">
                <span>🏛️ SKPD: {{ $statsSummary['mitra_skpd'] }}</span>
                <span>💼 Swasta: {{ $statsSummary['mitra_swasta'] }}</span>
                <span>🏪 UMKM: {{ $statsSummary['mitra_umkm'] }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 2: Akun PIC Mitra -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-success fs-7 fw-bold">Akun PIC Mitra</span>
                <span class="fs-4">👤</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['mitra_ber_pic'] }} <span class="fs-6 text-muted font-normal">Ber-PIC</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">✅ Ber-PIC: {{ $statsSummary['mitra_ber_pic'] }}</span>
                <span class="text-warning fw-semibold">⚠️ Tanpa PIC: {{ $statsSummary['mitra_tanpa_pic'] }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 3: Plotting Kelompok PPL -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-info fs-7 fw-bold">Penempatan Kelompok</span>
                <span class="fs-4">👥</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['mitra_terplot'] }} <span class="fs-6 text-muted font-normal">Mitra Terisi</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-info fw-semibold">📌 Terplot: {{ $statsSummary['mitra_terplot'] }}</span>
                <span class="text-secondary fw-semibold">⏳ Standby: {{ $statsSummary['mitra_standby'] }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 4: Kelengkapan Kontak & Alamat -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-warning fs-7 fw-bold">Kelengkapan Informasi</span>
                <span class="fs-4">📇</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['mitra_dengan_wa'] }} <span class="fs-6 text-muted font-normal">Terisi Kontak</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">📱 WA: {{ $statsSummary['mitra_dengan_wa'] }}</span>
                <span class="text-primary fw-semibold">📍 Alamat: {{ $statsSummary['mitra_dengan_alamat'] }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-3 mb-4">
    <form action="{{ route('admin.mitra.index') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-5">
            <input type="text" name="search" class="form-control fs-7" placeholder="Cari nama mitra atau alamat..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-4">
            <select name="kategori" class="form-select fs-7">
                <option value="">-- Semua Kategori --</option>
                <option value="SKPD" {{ request('kategori') == 'SKPD' ? 'selected' : '' }}>SKPD (Instansi Pemda)</option>
                <option value="Swasta" {{ request('kategori') == 'Swasta' ? 'selected' : '' }}>Swasta / Perusahaan</option>
                <option value="UMKM" {{ request('kategori') == 'UMKM' ? 'selected' : '' }}>UMKM</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary fs-7 w-100">Filter</button>
            <a href="{{ route('admin.mitra.index') }}" class="btn btn-outline-secondary fs-7">Reset</a>
        </div>
    </form>
</div>

<!-- Desktop View Table -->
<div class="card card-custom overflow-hidden d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light fs-7 text-uppercase text-secondary">
                <tr>
                    <th class="ps-4">Nama Mitra</th>
                    <th>Kategori</th>
                    <th>PIC Mitra (1-to-1)</th>
                    <th>Alamat</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="fs-7">
                @forelse($mitraList as $mitra)
                    <tr>
                        <td class="ps-4 fw-semibold text-dark">{{ $mitra->nama_mitra }}</td>
                        <td>
                            <span class="badge {{ $mitra->kategori === 'SKPD' ? 'bg-primary' : ($mitra->kategori === 'Swasta' ? 'bg-success' : 'bg-warning text-dark') }} rounded-pill px-3">
                                {{ $mitra->kategori }}
                            </span>
                        </td>
                        <td>
                            @if($mitra->picUser)
                                <div class="fw-semibold text-dark">{{ $mitra->picUser->nama_lengkap }}</div>
                                <div class="text-muted fs-8">@ {{ $mitra->picUser->username }} | {{ $mitra->picUser->no_hp ?? '-' }}</div>
                            @else
                                <span class="badge bg-light text-muted border">Belum Ditautkan</span>
                            @endif
                        </td>
                        <td class="text-secondary text-truncate" style="max-width: 250px;">{{ $mitra->alamat ?? '-' }}</td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.mitra.edit', $mitra) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.mitra.destroy', $mitra) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data mitra ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data mitra yang sesuai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile View Cards -->
<div class="d-block d-md-none">
    @forelse($mitraList as $mitra)
        <div class="card card-custom p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="fw-bold mb-0 text-dark">{{ $mitra->nama_mitra }}</h6>
                <span class="badge {{ $mitra->kategori === 'SKPD' ? 'bg-primary' : ($mitra->kategori === 'Swasta' ? 'bg-success' : 'bg-warning text-dark') }}">
                    {{ $mitra->kategori }}
                </span>
            </div>
            <p class="text-muted fs-7 mb-2">🏢 Alamat: {{ $mitra->alamat ?? '-' }}</p>
            <p class="text-muted fs-7 mb-3">👤 PIC: {{ $mitra->picUser->nama_lengkap ?? 'Belum ada' }}</p>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.mitra.edit', $mitra) }}" class="btn btn-sm btn-outline-primary w-100">Edit</a>
                <form action="{{ route('admin.mitra.destroy', $mitra) }}" method="POST" class="w-100" onsubmit="return confirm('Hapus data mitra?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <div class="card card-custom p-4 text-center text-muted fs-7">
            Belum ada data mitra yang sesuai.
        </div>
    @endforelse
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3 px-1">
    <div class="text-muted fs-7">
        Menampilkan <strong>{{ $mitraList->firstItem() ?? 0 }}</strong> – <strong>{{ $mitraList->lastItem() ?? 0 }}</strong> dari <strong>{{ $mitraList->total() }}</strong> Mitra Instansi
    </div>
    <div>
        {{ $mitraList->links() }}
    </div>
</div>

<!-- Modal Impor Excel Mitra -->
<div class="modal fade" id="modalImportExcelMitra" tabindex="-1" aria-labelledby="modalImportExcelMitraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-custom">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalImportExcelMitraLabel">📥 Impor Data Mitra dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.mitra.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body fs-7">
                    <p class="text-muted mb-2">Unggah berkas Excel (<code>.xlsx</code>, <code>.xls</code>, atau <code>.csv</code>) sesuai dengan format template resmi Mitra.</p>

                    <div class="mb-3">
                        <a href="{{ route('admin.mitra.template') }}" class="btn btn-sm btn-light border text-primary fw-semibold w-100 py-2">
                            📥 Download Template Resmi Impor Mitra (.xlsx)
                        </a>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border fs-8">
                        <strong class="d-block mb-1 text-dark">📋 Urutan Kolom Template Impor Mitra:</strong>
                        <ol class="mb-0 ps-3 text-secondary">
                            <li><code>ID Mitra</code> (Dikosongkan saat tambah data baru)</li>
                            <li><code>Nama Mitra Instansi</code> (Wajib)</li>
                            <li><code>Kategori</code> (SKPD / Swasta / UMKM)</li>
                            <li><code>Alamat</code> (Opsional)</li>
                            <li><code>Nama PIC Mitra</code> (Wajib)</li>
                            <li><code>Username PIC</code> (Opsional, dibuat otomatis jika kosong)</li>
                            <li><code>Password PIC</code> (Default: <code>password123</code> jika dikosongkan)</li>
                            <li><code>No HP PIC</code> (Opsional)</li>
                        </ol>
                    </div>

                    <div class="mb-3">
                        <label for="file_excel_mitra" class="form-label fw-semibold text-secondary">Pilih Berkas Excel:</label>
                        <input type="file" name="file_excel" id="file_excel_mitra" class="form-control" accept=".xlsx,.xls,.csv" required>
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
