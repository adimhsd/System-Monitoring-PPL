@extends('layouts.app')

@section('title', 'Penilaian Mahasiswa — DPL')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Penilaian Mahasiswa PPL Dual-Source — Dosen Pembimbing (DPL)</h4>
    <p class="text-muted mb-0 fs-7">Input skor penilaian per mahasiswa (Bobot DPL: <strong>40%</strong> + Mitra: <strong>60%</strong>).</p>
</div>

<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-4">Nama Kelompok</th>
                    <th>Mitra Penempatan</th>
                    <th>Luaran Kelompok (PDF & Video)</th>
                    <th>Jumlah Mahasiswa</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelompokList as $kelompok)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $kelompok->nama_kelompok }}</div>
                            <div class="text-muted fs-8">👑 Ketua: {{ $kelompok->ketua->nama_lengkap ?? '-' }}</div>
                        </td>
                        <td>{{ $kelompok->mitra->nama_mitra ?? '-' }}</td>
                        <td>
                            @if($kelompok->luaran && $kelompok->luaran->file_laporan_pdf)
                                <a href="{{ route('luaran.pdf.download', $kelompok->luaran) }}" target="_blank" class="badge bg-primary bg-opacity-10 text-primary border me-1 text-decoration-none">📄 PDF Terunggah</a>
                            @else
                                <span class="badge bg-light text-muted border me-1">Belum Ada PDF</span>
                            @endif

                            @if($kelompok->luaran && $kelompok->luaran->url_video)
                                <a href="{{ $kelompok->luaran->url_video }}" target="_blank" class="badge bg-danger bg-opacity-10 text-danger border text-decoration-none">🎬 YouTube</a>
                            @else
                                <span class="badge bg-light text-muted border">Belum Ada Video</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary px-3 py-1">{{ $kelompok->anggota->count() }} Mahasiswa</span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('dpl.penilaian.edit', $kelompok) }}" class="btn btn-sm btn-primary">
                                ✏️ Input/Edit Nilai DPL
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada kelompok bimbingan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
