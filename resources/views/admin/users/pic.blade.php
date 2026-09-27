@extends('layouts.app')

@section('title', 'Akun PIC Mitra')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">🏢 Daftar Akun PIC Pembimbing Mitra</h4>
        <p class="text-muted mb-0 fs-7">Kelola khusus akun pengguna dengan role Pembimbing Lapangan Mitra Instansi/Perusahaan (PIC Mitra).</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2 fs-7 fw-bold">
            Total: {{ $totalPic }} Akun PIC
        </span>
        <a href="{{ route('admin.users.create', ['role' => 'pic_mitra']) }}" class="btn btn-success btn-touch text-white rounded-3 fw-semibold">
            + Tambah Akun PIC Mitra
        </a>
    </div>
</div>

<div class="card card-custom p-3 mb-4">
    <form action="{{ route('admin.users.pic') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-9">
            <input type="text" name="search" class="form-control fs-7" placeholder="Cari nama PIC, username, no HP, nama instansi mitra..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-success fs-7 w-100">Cari Akun PIC</button>
            <a href="{{ route('admin.users.pic') }}" class="btn btn-outline-secondary fs-7">Reset</a>
        </div>
    </form>
</div>

<!-- Desktop Table -->
<div class="card card-custom overflow-hidden d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary text-uppercase">
                <tr>
                    <th class="ps-4">Nama PIC Mitra</th>
                    <th>Username Login</th>
                    <th>Instansi / Perusahaan Tertaut</th>
                    <th>No. HP / Whatsapp</th>
                    <th>Status Akun</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">👤 {{ $u->nama_lengkap }}</div>
                        </td>
                        <td><code>{{ $u->username }}</code></td>
                        <td>
                            @if($u->mitraPic)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-1">
                                    🏢 {{ $u->mitraPic->nama_mitra }} ({{ $u->mitraPic->kategori }})
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">Belum Ditautkan ke Mitra</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $u->no_hp ?? '-' }}</div>
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
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun PIC Mitra ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada akun PIC Mitra yang terdaftar.</td>
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
                    <h6 class="fw-bold mb-0 text-dark">👤 {{ $u->nama_lengkap }}</h6>
                    <small class="text-muted">@ {{ $u->username }}</small>
                </div>
                <span class="badge bg-success">PIC Mitra</span>
            </div>
            <p class="text-secondary fs-7 mb-2">Instansi: {{ $u->mitraPic->nama_mitra ?? 'Belum ada' }} | HP: {{ $u->no_hp ?? '-' }}</p>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary w-100">Edit</a>
            </div>
        </div>
    @empty
        <div class="card card-custom p-4 text-center text-muted fs-7">
            Belum ada akun PIC Mitra yang terdaftar.
        </div>
    @endforelse
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3 px-1">
    <div class="text-muted fs-7">
        Menampilkan <strong>{{ $users->firstItem() ?? 0 }}</strong> – <strong>{{ $users->lastItem() ?? 0 }}</strong> dari <strong>{{ $users->total() }}</strong> Akun PIC Mitra
    </div>
    <div>
        {{ $users->links() }}
    </div>
</div>
@endsection
