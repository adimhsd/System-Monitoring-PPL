@extends('layouts.app')

@section('title', 'Akun Kelompok')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">👥 Daftar Akun Kelompok PPL</h4>
        <p class="text-muted mb-0 fs-7">Kelola khusus akun pengguna independen kelompok PPL untuk mahasiswa anggota kelompok.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-3 py-2 fs-7 fw-bold text-dark">
            Total: {{ $totalKelompok }} Akun Kelompok
        </span>
        <a href="{{ route('admin.users.create', ['role' => 'ketua_kelompok']) }}" class="btn btn-warning btn-touch text-dark rounded-3 fw-semibold">
            + Tambah Akun Kelompok
        </a>
    </div>
</div>

<div class="card card-custom p-3 mb-4">
    <form action="{{ route('admin.users.kelompok') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-9">
            <input type="text" name="search" class="form-control fs-7" placeholder="Cari username akun kelompok, nama kelompok PPL..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-warning text-dark fs-7 w-100 fw-semibold">Cari Akun Kelompok</button>
            <a href="{{ route('admin.users.kelompok') }}" class="btn btn-outline-secondary fs-7">Reset</a>
        </div>
    </form>
</div>

<!-- Desktop Table -->
<div class="card card-custom overflow-hidden d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary text-uppercase">
                <tr>
                    <th class="ps-4">Username Akun Kelompok</th>
                    <th>Nama Kelompok PPL</th>
                    <th>Mitra / Instansi Magang</th>
                    <th>Status Akun</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">🔑 <code>{{ $u->username }}</code></div>
                        </td>
                        <td>
                            @if($u->kelompokKetua)
                                <div class="fw-semibold text-primary">👥 {{ $u->kelompokKetua->nama_kelompok }}</div>
                            @else
                                <div class="text-muted">{{ $u->nama_lengkap }}</div>
                            @endif
                        </td>
                        <td>
                            @if($u->kelompokKetua && $u->kelompokKetua->mitra)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-1">
                                    🏢 {{ $u->kelompokKetua->mitra->nama_mitra }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">Belum Diplotkan ke Mitra</span>
                            @endif
                        </td>
                        <td>
                            @if($u->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                            @if($u->must_change_password)
                                <span class="badge bg-warning text-dark ms-1" title="Wajib ganti password saat login">Ganti Password</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun kelompok ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada akun kelompok PPL yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile View -->
<div class="d-block d-md-none">
    @forelse($users as $u)
        <div class="card card-custom p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">👥 {{ $u->kelompokKetua->nama_kelompok ?? $u->nama_lengkap }}</h6>
                    <small class="text-muted">Username: @ {{ $u->username }}</small>
                </div>
                <span class="badge bg-warning text-dark">Kelompok</span>
            </div>
            <p class="text-secondary fs-7 mb-2">Mitra: {{ $u->kelompokKetua->mitra->nama_mitra ?? 'Belum Diplotkan' }}</p>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary w-100">Edit</a>
            </div>
        </div>
    @empty
        <div class="card card-custom p-4 text-center text-muted fs-7">
            Belum ada akun kelompok PPL yang terdaftar.
        </div>
    @endforelse
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3 px-1">
    <div class="text-muted fs-7">
        Menampilkan <strong>{{ $users->firstItem() ?? 0 }}</strong> – <strong>{{ $users->lastItem() ?? 0 }}</strong> dari <strong>{{ $users->total() }}</strong> Akun Kelompok PPL
    </div>
    <div>
        {{ $users->links() }}
    </div>
</div>
@endsection
