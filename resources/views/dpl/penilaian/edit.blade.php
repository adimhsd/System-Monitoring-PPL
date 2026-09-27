@extends('layouts.app')

@section('title', 'Input Penilaian DPL Per Mahasiswa')

@section('content')
<div class="mb-4">
    <a href="{{ route('dpl.penilaian.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Penilaian DPL</a>
    <h4 class="fw-bold mb-1 mt-1">Input Penilaian DPL — {{ $kelompok->nama_kelompok }}</h4>
    <p class="text-muted mb-0 fs-7">Input skor 4 komponen (0 - 100) per <strong>mahasiswa</strong> (Bobot DPL: <strong>40%</strong> + Mitra: <strong>60%</strong>).</p>
</div>

<!-- Card Mandatory Group Deliverables -->
<div class="card card-custom p-3 mb-4 bg-light border">
    <h6 class="fw-bold text-dark mb-2">📁 Berkas & Luaran Wajib Kelompok:</h6>
    <div class="d-flex flex-wrap gap-3 fs-7">
        <div>
            <strong>Laporan PDF:</strong>
            @if($kelompok->luaran && $kelompok->luaran->file_laporan_pdf)
                <a href="{{ route('luaran.pdf.download', $kelompok->luaran) }}" target="_blank" class="text-primary fw-semibold ms-1">📄 Unduh & Periksa PDF Laporan</a>
            @else
                <span class="text-danger ms-1">⚠️ Kelompok belum mengunggah PDF laporan.</span>
            @endif
        </div>
        <div>
            <strong>Video YouTube:</strong>
            @if($kelompok->luaran && $kelompok->luaran->url_video)
                <a href="{{ $kelompok->luaran->url_video }}" target="_blank" class="text-danger fw-semibold ms-1">🎬 Tonton Video YouTube</a>
            @else
                <span class="text-danger ms-1">⚠️ Kelompok belum mengunggah video.</span>
            @endif
        </div>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <form action="{{ route('dpl.penilaian.update', $kelompok) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle mb-0 fs-7">
                <thead class="bg-light text-secondary text-center">
                    <tr>
                        <th style="width: 20%;">Nama & NIM Mahasiswa</th>
                        <th style="width: 13%;">1. Kedisiplinan</th>
                        <th style="width: 13%;">2. Etika</th>
                        <th style="width: 13%;">3. Kerjasama</th>
                        <th style="width: 13%;">4. Hasil Kerja</th>
                        <th style="width: 9%;">Mitra (60%)</th>
                        <th style="width: 9%;">DPL (40%)</th>
                        <th style="width: 10%;">Nilai Akhir & Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelompok->anggota as $mhs)
                        @php
                            $p = $mhs->penilaian;
                            $nilaiMitra = $p ? $p->total_nilai_mitra : null;
                            $nilaiDpl = $p ? $p->total_nilai_dpl : null;
                            $nilaiAkhir = ($nilaiMitra !== null && $nilaiDpl !== null) ? round(($nilaiMitra * 0.60) + ($nilaiDpl * 0.40), 2) : null;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $mhs->nama }}</div>
                                <div class="text-muted fs-8">NIM: <code>{{ $mhs->nim }}</code> ({{ $mhs->prodi }})</div>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" name="nilai[{{ $mhs->id }}][kedisiplinan]" value="{{ old("nilai.{$mhs->id}.kedisiplinan", $p->dpl_skor_kedisiplinan ?? 85) }}" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" name="nilai[{{ $mhs->id }}][etika]" value="{{ old("nilai.{$mhs->id}.etika", $p->dpl_skor_etika ?? 85) }}" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" name="nilai[{{ $mhs->id }}][kerjasama]" value="{{ old("nilai.{$mhs->id}.kerjasama", $p->dpl_skor_kerjasama ?? 85) }}" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" name="nilai[{{ $mhs->id }}][hasil_kerja]" value="{{ old("nilai.{$mhs->id}.hasil_kerja", $p->dpl_skor_hasil_kerja ?? 85) }}" required>
                            </td>
                            <td class="text-center fw-bold text-success">
                                {{ $nilaiMitra ?? 'Belum' }}
                            </td>
                            <td class="text-center fw-bold text-primary">
                                {{ $nilaiDpl ?? '-' }}
                            </td>
                            <td class="text-center">
                                @if($nilaiAkhir !== null)
                                    <div class="fw-bold fs-6 text-dark">{{ $nilaiAkhir }}</div>
                                    <span class="badge bg-primary px-2 py-1 fs-8">{{ $p->nilai_huruf }}</span>
                                @else
                                    <span class="text-muted fs-8">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
            Simpan Penilaian DPL {{ $kelompok->nama_kelompok }} & Kalkulasi Nilai Akhir
        </button>
    </form>
</div>
@endsection
