@extends('layouts.app')

@section('title', 'Ringkasan Kelola User System')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">⚙️ Ringkasan Kelola User Sistem</h4>
        <p class="text-muted mb-0 fs-7">Ikhtisar ringkasan seluruh akun pengguna terdaftar pada Sistem Monitoring & Penilaian PPL FEB UNIKU.</p>
    </div>
    <div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-touch text-white rounded-3 fw-semibold">
            + Tambah Akun Baru
        </a>
    </div>
</div>

<!-- Stat Cards Summary per Kategori Akun -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fs-7 fw-semibold">Akun Admin</span>
                <span class="fs-4">🛡️</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">{{ $stats['total_admin'] }}</h3>
            <span class="text-muted fs-8">Administrator Utama</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('admin.users.dpl') }}" class="text-decoration-none">
            <div class="card card-custom p-3 border-start border-4 border-primary h-100 hover-shadow transition">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-primary fs-7 fw-bold">Akun DPL</span>
                    <span class="fs-4">👨‍🏫</span>
                </div>
                <h3 class="fw-bold text-primary mb-0">{{ $stats['total_dpl'] }}</h3>
                <span class="text-muted fs-8">Dosen Pembimbing Lapangan →</span>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('admin.users.pic') }}" class="text-decoration-none">
            <div class="card card-custom p-3 border-start border-4 border-success h-100 hover-shadow transition">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-success fs-7 fw-bold">Akun PIC Mitra</span>
                    <span class="fs-4">🏢</span>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $stats['total_pic'] }}</h3>
                <span class="text-muted fs-8">Pembimbing Lapangan Mitra →</span>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('admin.users.kelompok') }}" class="text-decoration-none">
            <div class="card card-custom p-3 border-start border-4 border-warning h-100 hover-shadow transition">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-dark fs-7 fw-bold">Akun Kelompok</span>
                    <span class="fs-4">👥</span>
                </div>
                <h3 class="fw-bold text-warning mb-0">{{ $stats['total_kelompok'] }}</h3>
                <span class="text-muted fs-8">Akun Kelompok PPL →</span>
            </div>
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-5">
            <input type="text" name="search" class="form-control fs-7" placeholder="Cari nama, username, NIP/NIDN..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-4">
            <select name="role" class="form-select fs-7">
                <option value="">-- Semua Role Kategori --</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin Unit PPL</option>
                <option value="dpl" {{ request('role') == 'dpl' ? 'selected' : '' }}>DPL (Dosen)</option>
                <option value="pic_mitra" {{ request('role') == 'pic_mitra' ? 'selected' : '' }}>PIC Mitra</option>
                <option value="ketua_kelompok" {{ request('role') == 'ketua_kelompok' ? 'selected' : '' }}>Kelompok PPL</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary fs-7 w-100">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary fs-7">Reset</a>
        </div>
    </form>
</div>

<!-- Desktop Table -->
<div class="card card-custom overflow-hidden d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary text-uppercase">
                <tr>
                    <th class="ps-4">Nama Lengkap</th>
                    <th>Username</th>
                    <th>Role Kategori</th>
                    <th>NIP / NIDN / No HP</th>
                    <th>Status Akun</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="ps-4 fw-semibold text-dark">{{ $u->nama_lengkap }}</td>
                        <td><code>{{ $u->username }}</code></td>
                        <td>
                            <span class="badge badge-role-{{ $u->role }} rounded-pill px-3">
                                {{ strtoupper(str_replace('_', ' ', $u->role)) }}
                            </span>
                        </td>
                        <td class="text-secondary">
                            <div>NIP: {{ $u->nip_nidn ?? '-' }}</div>
                            <div class="fs-8 text-muted">HP: {{ $u->no_hp ?? '-' }}</div>
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
                                @if(auth()->id() !== $u->id)
                                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada akun pengguna yang sesuai.</td>
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
                    <h6 class="fw-bold mb-0 text-dark">{{ $u->nama_lengkap }}</h6>
                    <small class="text-muted">@ {{ $u->username }}</small>
                </div>
                <span class="badge badge-role-{{ $u->role }}">
                    {{ strtoupper(str_replace('_', ' ', $u->role)) }}
                </span>
            </div>
            <p class="text-secondary fs-7 mb-2">NIP/HP: {{ $u->nip_nidn ?? $u->no_hp ?? '-' }}</p>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary w-100">Edit</a>
            </div>
        </div>
    @empty
        <div class="card card-custom p-4 text-center text-muted fs-7">
            Belum ada akun pengguna yang sesuai.
        </div>
    @endforelse
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3 px-1">
    <div class="text-muted fs-7">
        Menampilkan <strong>{{ $users->firstItem() ?? 0 }}</strong> – <strong>{{ $users->lastItem() ?? 0 }}</strong> dari <strong>{{ $users->total() }}</strong> Akun User
    </div>
    <div>
        {{ $users->links() }}
    </div>
</div>
@endsection
