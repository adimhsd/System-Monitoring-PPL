@extends('layouts.app')

@section('title', 'Luaran Kelompok Bimbingan DPL')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Luaran Akhir Kelompok Bimbingan DPL</h4>
    <p class="text-muted mb-0 fs-7">Verifikasi dokumen Laporan PDF, Link Video YouTube, & Poster Kegiatan PPL kelompok bimbingan Anda.</p>
</div>

<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-4">Nama Kelompok</th>
                    <th>Mitra Penempatan</th>
                    <th>1. Laporan PDF (Max 5MB)</th>
                    <th>2. Video YouTube</th>
                    <th>3. Poster Kegiatan (Max 3MB)</th>
                    <th class="text-end pe-4">Status Upload</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelompokList as $kelompok)
                    @php
                        $luaran = $kelompok->luaran;
                        $hasPdf = $luaran && !empty($luaran->file_laporan_pdf);
                        $hasVideo = $luaran && !empty($luaran->url_video);
                        $hasPoster = $luaran && !empty($luaran->file_poster);
                        $isLengkap = $hasPdf && $hasVideo && $hasPoster;
                        $isParsial = !$isLengkap && ($hasPdf || $hasVideo || $hasPoster);
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $kelompok->nama_kelompok }}</div>
                            <div class="text-muted fs-8">👑 Ketua: {{ $kelompok->ketua->nama_lengkap }}</div>
                        </td>
                        <td>{{ $kelompok->mitra->nama_mitra }}</td>
                        <td>
                            @if($hasPdf)
                                <a href="{{ route('luaran.pdf.download', $luaran) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    📄 Unduh PDF
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Belum Unggah</span>
                            @endif
                        </td>
                        <td>
                            @if($hasVideo)
                                <a href="{{ $luaran->url_video }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    🎬 Tonton YouTube
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Belum Isi Link</span>
                            @endif
                        </td>
                        <td>
                            @if($hasPoster)
                                <a href="{{ route('luaran.poster.show', $luaran) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    🖼️ Lihat Poster
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Belum Unggah</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($isLengkap)
                                <span class="badge bg-success">
                                    Lengkap ({{ $luaran->uploaded_at?->format('d/m/Y') }})
                                </span>
                            @elseif($isParsial)
                                <span class="badge bg-info text-dark">Parsial</span>
                            @else
                                <span class="badge bg-warning text-dark">Belum Lengkap</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada kelompok bimbingan PPL.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
