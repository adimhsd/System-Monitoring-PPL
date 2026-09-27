<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Logbook Kegiatan Harian PPL — {{ $kelompok->nama_kelompok }}</title>
    <style>
        @page {
            margin: 1.2cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.4;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header-kop h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .header-kop h4 {
            margin: 3px 0 0 0;
            font-size: 11pt;
            font-weight: normal;
            color: #334155;
        }
        .header-kop p {
            margin: 2px 0 0 0;
            font-size: 8pt;
            color: #64748b;
        }
        .title-doc {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-doc h5 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
            text-decoration: underline;
            color: #0f172a;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        .meta-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .meta-table td.label {
            font-weight: bold;
            width: 22%;
            color: #334155;
        }
        .meta-table td.colon {
            width: 2%;
        }
        .logbook-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        .logbook-table th, .logbook-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: top;
        }
        .logbook-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5pt;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .badge-warning {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fde047;
        }
        .img-thumb {
            max-width: 75px;
            max-height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
        }
        .signature-section {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
            font-size: 9.5pt;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- Header Kop Surat -->
    <table style="width: 100%; border-bottom: 2px solid #0f172a; padding-bottom: 8px; margin-bottom: 15px;">
        <tr>
            <td style="width: 15%; text-align: center; vertical-align: middle;">
                @if(isset($logoUnikuBase64) && $logoUnikuBase64)
                    <img src="{{ $logoUnikuBase64 }}" style="max-height: 65px; width: auto;" alt="Logo UNIKU">
                @endif
            </td>
            <td style="width: 85%; text-align: center; vertical-align: middle;">
                <h3 style="margin: 0; font-size: 14pt; font-weight: bold; color: #0f172a; text-transform: uppercase;">FAKULTAS EKONOMI DAN BISNIS</h3>
                <h4 style="margin: 3px 0 0 0; font-size: 11pt; font-weight: normal; color: #334155;">UNIVERSITAS KUNINGAN</h4>
                <p style="margin: 2px 0 0 0; font-size: 8pt; color: #64748b;">Jl. Cut Nyak Dhien No. 36A Cijoho, Kuningan, Jawa Barat 45513 | Email: feb@uniku.ac.id</p>
            </td>
        </tr>
    </table>

    <div class="title-doc">
        <h5>LAPORAN LOGBOOK KEGIATAN HARIAN MAGANG PPL</h5>
        <p style="margin: 3px 0 0 0; font-size: 8.5pt; color: #64748b;">Tahun Akademik {{ $kelompok->tahun_akademik }}</p>
    </div>

    <!-- Metadata Kelompok -->
    <table class="meta-table">
        <tr>
            <td class="label">Nama Kelompok</td>
            <td class="colon">:</td>
            <td><strong>{{ $kelompok->nama_kelompok }}</strong></td>
            <td class="label">DPL Fakultas</td>
            <td class="colon">:</td>
            <td>{{ $kelompok->dpl->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Instansi Mitra</td>
            <td class="colon">:</td>
            <td>{{ $kelompok->mitra->nama_mitra ?? '-' }}</td>
            <td class="label">Pembimbing PIC Mitra</td>
            <td class="colon">:</td>
            <td>{{ $kelompok->mitra->picUser->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Mitra</td>
            <td class="colon">:</td>
            <td>{{ $kelompok->mitra->alamat ?? '-' }}</td>
            <td class="label">Username Akun</td>
            <td class="colon">:</td>
            <td>{{ $kelompok->ketua->username ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah Anggota</td>
            <td class="colon">:</td>
            <td>{{ $kelompok->anggota->count() }} Mahasiswa</td>
            <td class="label">Tahun Akademik</td>
            <td class="colon">:</td>
            <td>{{ $kelompok->tahun_akademik }}</td>
        </tr>
    </table>

    <!-- Tabel Kegiatan Harian -->
    <table class="logbook-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal & Waktu</th>
                <th style="width: 45%;">Deskripsi & Uraian Kegiatan</th>
                <th style="width: 15%;">Bukti Foto</th>
                <th style="width: 20%;">Status Approval</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logbookList as $index => $logbook)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $logbook->tanggal->format('d/m/Y') }}</strong><br>
                        <span style="font-size: 8pt; color: #64748b;">
                            Jam {{ \Carbon\Carbon::parse($logbook->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($logbook->waktu_selesai)->format('H:i') }} WIB
                        </span>
                        @if($logbook->terlambat)
                            <br><span style="color: #d97706; font-size: 7.5pt; font-weight: bold;">(Terlambat)</span>
                        @endif
                    </td>
                    <td>
                        {!! nl2br(e($logbook->deskripsi_kegiatan)) !!}
                    </td>
                    <td class="text-center">
                        @if($logbook->foto_base64)
                            <img src="{{ $logbook->foto_base64 }}" class="img-thumb" alt="Foto Dokumentasi">
                        @else
                            <span style="color: #94a3b8; font-size: 8pt; italic;">Tidak ada foto</span>
                        @endif
                    </td>
                    <td>
                        <div style="margin-bottom: 4px;">
                            @if($logbook->dilihat_mitra)
                                @if(($logbook->status_validasi_mitra ?? 'sesuai') === 'tidak_sesuai')
                                    <span class="badge-status badge-danger">&bull; PIC Mitra: Tidak Sesuai</span>
                                @else
                                    <span class="badge-status badge-success">&bull; PIC Mitra: Sesuai</span>
                                @endif
                                @if($logbook->catatan_mitra)
                                    <div style="font-size: 7.5pt; color: #475569; margin-top: 2px; font-style: italic;">"{{ $logbook->catatan_mitra }}"</div>
                                @endif
                            @else
                                <span class="badge-status badge-warning">&bull; Belum Di-Approve Mitra</span>
                            @endif
                        </div>
                        <div>
                            @if($logbook->dilihat_dpl)
                                <span class="badge-status badge-success">&bull; Approved DPL</span>
                            @else
                                <span class="badge-status badge-warning">&bull; Belum Di-Approve DPL</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 15px; color: #64748b;">Belum ada entri logbook harian yang dicatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Lembar Pengesahan -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Pembimbing / PIC Mitra</strong>
                    <div class="signature-space"></div>
                    <strong>( {{ $kelompok->mitra->picUser->nama_lengkap ?? $kelompok->mitra->pic_nama ?? '...................................................' }} )</strong>
                </td>
                <td>
                    Kuningan, {{ $tglCetak }}<br>
                    <strong>Dosen Pembimbing Lapangan (DPL)</strong>
                    <div class="signature-space"></div>
                    <strong>( {{ $kelompok->dpl->nama_lengkap ?? '...................................................' }} )</strong><br>
                    <span style="font-size: 8pt; color: #64748b;">NIP / NIDN: {{ $kelompok->dpl->nip_nidn ?? $kelompok->dpl->username ?? '-' }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
