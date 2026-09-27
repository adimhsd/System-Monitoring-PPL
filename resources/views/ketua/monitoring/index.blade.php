@extends('layouts.app')

@section('title', 'Kunjungan & Monitoring DPL')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">📍 Kunjungan & Monitoring DPL Pembimbing</h4>
    <p class="text-muted mb-0 fs-7">Halaman verifikasi dan persetujuan kelompok atas kunjungan lapangan DPL ke lokasi PPL (Kunjungan 1 - Penyerahan & Kunjungan 2 - Penarikan).</p>
</div>

<!-- Header Card Status Kunjungan DPL -->
<div class="row g-3 mb-4">
    <!-- Kunjungan 1: Penyerahan -->
    <div class="col-12 col-md-6">
        <div class="card card-custom p-4 border-start border-4 {{ $penyerahanRecord ? ($penyerahanRecord->disetujui_kelompok ? 'border-success' : 'border-warning') : 'border-secondary' }} h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-3 py-1 mb-2">
                        🚀 Kunjungan 1 - Penyerahan Mahasiswa (Awal)
                    </span>
                    <h5 class="fw-bold text-dark mb-1">Kunjungan Penyerahan Awal PPL</h5>
                </div>
                <span class="fs-2">🚀</span>
            </div>

            @if($penyerahanRecord)
                <div class="fs-8 text-secondary mb-3">
                    <div>📅 <strong>Tanggal:</strong> {{ $penyerahanRecord->tanggal_kunjungan->format('d/m/Y') }}</div>
                    <div>👨‍🏫 <strong>DPL:</strong> {{ $penyerahanRecord->dpl->nama_lengkap ?? '-' }}</div>
                    <div>📝 <strong>Catatan:</strong> {{ Str::limit($penyerahanRecord->catatan_kunjungan, 80) }}</div>
                </div>

                @if($penyerahanRecord->disetujui_kelompok)
                    <div class="alert alert-success py-2 px-3 mb-0 fs-8 d-flex align-items-center justify-content-between">
                        <span>✅ <strong>Disetujui Kelompok</strong> ({{ $penyerahanRecord->tanggal_disetujui ? $penyerahanRecord->tanggal_disetujui->format('d/m/Y H:i') : '' }})</span>
                    </div>
                @else
                    <form action="{{ route('ketua.monitoring.approve', $penyerahanRecord) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengonfirmasi dan menyetujui kunjungan DPL ini?')">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm text-dark w-100 fw-bold">
                            ⚡ Konfirmasi & Setujui Kunjungan Penyerahan
                        </button>
                    </form>
                @endif
            @else
                <div class="alert alert-light border py-3 text-center text-muted mb-0 fs-7">
                    ⏳ DPL belum mengunggah bukti kunjungan penyerahan mahasiswa ke lokasi mitra.
                </div>
            @endif
        </div>
    </div>

    <!-- Kunjungan 2: Penarikan -->
    <div class="col-12 col-md-6">
        <div class="card card-custom p-4 border-start border-4 {{ $penarikanRecord ? ($penarikanRecord->disetujui_kelompok ? 'border-success' : 'border-warning') : 'border-secondary' }} h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-3 py-1 mb-2">
                        🏁 Kunjungan 2 - Penarikan Mahasiswa (Akhir)
                    </span>
                    <h5 class="fw-bold text-dark mb-1">Kunjungan Penarikan Akhir PPL</h5>
                </div>
                <span class="fs-2">🏁</span>
            </div>

            @if($penarikanRecord)
                <div class="fs-8 text-secondary mb-3">
                    <div>📅 <strong>Tanggal:</strong> {{ $penarikanRecord->tanggal_kunjungan->format('d/m/Y') }}</div>
                    <div>👨‍🏫 <strong>DPL:</strong> {{ $penarikanRecord->dpl->nama_lengkap ?? '-' }}</div>
                    <div>📝 <strong>Catatan:</strong> {{ Str::limit($penarikanRecord->catatan_kunjungan, 80) }}</div>
                </div>

                @if($penarikanRecord->disetujui_kelompok)
                    <div class="alert alert-success py-2 px-3 mb-0 fs-8 d-flex align-items-center justify-content-between">
                        <span>✅ <strong>Disetujui Kelompok</strong> ({{ $penarikanRecord->tanggal_disetujui ? $penarikanRecord->tanggal_disetujui->format('d/m/Y H:i') : '' }})</span>
                    </div>
                @else
                    <form action="{{ route('ketua.monitoring.approve', $penarikanRecord) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengonfirmasi dan menyetujui kunjungan DPL ini?')">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm text-dark w-100 fw-bold">
                            ⚡ Konfirmasi & Setujui Kunjungan Penarikan
                        </button>
                    </form>
                @endif
            @else
                <div class="alert alert-light border py-3 text-center text-muted mb-0 fs-7">
                    ⏳ DPL belum mengunggah bukti kunjungan penarikan mahasiswa setelah selesai PPL.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Daftar Riwayat Kunjungan Monitoring DPL -->
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3">📋 Riwayat Kunjungan Monitoring DPL ke {{ $kelompok->nama_kelompok }}</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-3">Jenis Kunjungan</th>
                    <th>Tanggal & Waktu</th>
                    <th>Dosen Pembimbing</th>
                    <th>Foto Dokumentasi</th>
                    <th>Catatan Evaluasi DPL</th>
                    <th class="text-end pe-3">Aksi Persetujuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monitoringList as $m)
                    <tr>
                        <td class="ps-3">
                            @if($m->jenis_kunjungan === 'penyerahan')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-3 py-1">
                                    🚀 Kunjungan 1 (Penyerahan)
                                </span>
                            @elseif($m->jenis_kunjungan === 'penarikan')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-3 py-1">
                                    🏁 Kunjungan 2 (Penarikan)
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-1">
                                    📍 Kunjungan Rutin
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $m->tanggal_kunjungan->format('d/m/Y') }}</div>
                            @if($m->waktu_kunjungan)
                                <div class="text-muted fs-8">🕒 {{ \Carbon\Carbon::parse($m->waktu_kunjungan)->format('H:i') }} WIB</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $m->dpl->nama_lengkap ?? '-' }}</div>
                        </td>
                        <td>
                            @if($m->foto_kunjungan)
                                <a href="{{ asset('storage/' . $m->foto_kunjungan) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $m->foto_kunjungan) }}" alt="Foto Dokumentasi" class="rounded border shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Tidak Ada Foto</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark">{{ $m->catatan_kunjungan }}</div>
                        </td>
                        <td class="text-end pe-3">
                            @if($m->disetujui_kelompok)
                                <span class="badge bg-success px-3 py-2">
                                    ✓ Disetujui
                                </span>
                                @if($m->tanggal_disetujui)
                                    <div class="fs-8 text-muted mt-1">{{ $m->tanggal_disetujui->format('d/m/Y H:i') }}</div>
                                @endif
                            @else
                                <form action="{{ route('ketua.monitoring.approve', $m) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui kunjungan DPL ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold px-3">
                                        ⚡ Setujui
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada kunjungan DPL yang dicatat untuk kelompok Anda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
