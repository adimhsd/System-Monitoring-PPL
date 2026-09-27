<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Plotting & Pemetaan Penempatan PPL — FEB UNIKU</title>
    <style>
        @page {
            margin: 1.2cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.3;
        }
        .header-kop {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .title-doc {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-doc h4 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .title-doc p {
            margin: 3px 0 0 0;
            font-size: 8.5pt;
            color: #64748b;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            vertical-align: top;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .badge-status {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .badge-warning {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fde047;
        }
        .signature-section {
            width: 100%;
            margin-top: 25px;
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
            padding: 5px;
            font-size: 8.5pt;
        }
        .signature-space {
            height: 55px;
        }
    </style>
</head>
<body>

    <!-- Header Kop Surat -->
    <table class="header-kop">
        <tr>
            <td style="width: 12%; text-align: center; vertical-align: middle;">
                @if(isset($logoUnikuBase64) && $logoUnikuBase64)
                    <img src="{{ $logoUnikuBase64 }}" style="max-height: 60px; width: auto;" alt="Logo UNIKU">
                @endif
            </td>
            <td style="width: 88%; text-align: center; vertical-align: middle;">
                <h3 style="margin: 0; font-size: 13pt; font-weight: bold; color: #0f172a; text-transform: uppercase;">FAKULTAS EKONOMI DAN BISNIS</h3>
                <h4 style="margin: 2px 0 0 0; font-size: 10.5pt; font-weight: normal; color: #334155;">UNIVERSITAS KUNINGAN</h4>
                <p style="margin: 2px 0 0 0; font-size: 7.5pt; color: #64748b;">Jl. Cut Nyak Dhien No. 36A Cijoho, Kuningan, Jawa Barat 45513 | Email: feb@uniku.ac.id</p>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="title-doc">
        <h4>LAPORAN PLOTTING & PEMETAAN PENEMPATAN PPL</h4>
        <p>Tahun Akademik {{ date('Y') }}/{{ date('Y')+1 }} — Cetak: {{ date('d F Y H:i') }} WIB</p>
    </div>

    <!-- Ringkasan Statistik -->
    <table class="summary-box">
        <tr>
            <td style="width: 33%;"><strong>Total Kelompok PPL:</strong> {{ $plottingList->count() }} Kelompok</td>
            <td style="width: 33%;"><strong>Total Mahasiswa Diplotkan:</strong> {{ $totalMahasiswa }} Mahasiswa</td>
            <td style="width: 34%;"><strong>Total Mitra Penempatan:</strong> {{ $totalMitra }} Instansi/Perusahaan</td>
        </tr>
    </table>

    <!-- Tabel Data Plotting -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 18%;">Kelompok & Akun</th>
                <th style="width: 25%;">Mitra Penempatan</th>
                <th style="width: 23%;">Pembimbing Lapangan</th>
                <th style="width: 30%;">Anggota Mahasiswa (NIM - Nama)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plottingList as $idx => $k)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong style="color: #0f172a;">{{ $k->nama_kelompok }}</strong><br>
                        <span style="color: #64748b; font-size: 7.5pt;">Akun: {{ $k->ketua->username ?? '-' }}</span><br>
                        <span style="color: #64748b; font-size: 7.5pt;">TA {{ $k->tahun_akademik }}</span>
                    </td>
                    <td>
                        @if($k->mitra)
                            <strong style="color: #1e293b;">{{ $k->mitra->nama_mitra }}</strong><br>
                            <span style="color: #64748b; font-size: 7.5pt;">Kategori: {{ $k->mitra->kategori }}</span><br>
                            <span style="color: #64748b; font-size: 7.5pt;">PIC: {{ $k->mitra->picUser->nama_lengkap ?? '-' }}</span>
                        @else
                            <span class="badge-status badge-warning">&bull; Belum Diplotkan</span>
                        @endif
                    </td>
                    <td>
                        @if($k->dpl)
                            <strong style="color: #1e293b;">DPL: {{ $k->dpl->nama_lengkap }}</strong><br>
                            <span style="color: #64748b; font-size: 7.5pt;">NIP/NIDN: {{ $k->dpl->nip_nidn ?? '-' }}</span>
                        @else
                            <span class="badge-status badge-warning">&bull; Belum Diplotkan</span>
                        @endif
                    </td>
                    <td>
                        @if($k->anggota->count() > 0)
                            <div style="font-weight: bold; margin-bottom: 2px; color: #0284c7;">
                                {{ $k->anggota->count() }} Mahasiswa:
                            </div>
                            @foreach($k->anggota as $mhs)
                                <div style="font-size: 7.5pt; color: #334155;">
                                    &bull; <strong>{{ $mhs->nim }}</strong> - {{ $mhs->nama }} ({{ $mhs->prodi }})
                                </div>
                            @endforeach
                        @else
                            <span class="badge-status badge-warning">&bull; Belum Ada Anggota</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 15px; color: #64748b;">Belum ada data kelompok PPL untuk diplotkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Lembar Pengesahan -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td></td>
                <td>
                    Kuningan, {{ date('d F Y') }}<br>
                    <strong>Administrator / Panitia PPL</strong><br>
                    Fakultas Ekonomi dan Bisnis
                    <div class="signature-space"></div>
                    <strong><u>Unit Pengelola PPL FEB UNIKU</u></strong>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
