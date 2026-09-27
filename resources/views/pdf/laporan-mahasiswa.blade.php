<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Master Data Mahasiswa PPL — FEB UNIKU</title>
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
        <h4>LAPORAN MASTER DATA MAHASISWA PPL FEB UNIKU</h4>
        <p>Tahun Akademik {{ date('Y') }}/{{ date('Y')+1 }} — Dicetak: {{ date('d F Y H:i') }} WIB</p>
    </div>

    <!-- Ringkasan Statistik -->
    <table class="summary-box">
        <tr>
            <td style="width: 33%;"><strong>Total Mahasiswa:</strong> {{ $totalMahasiswa }} Mahasiswa</td>
            <td style="width: 33%;"><strong>Sudah Memiliki Kelompok:</strong> {{ $totalAssigned }} Mahasiswa</td>
            <td style="width: 34%;"><strong>Belum Diplotkan:</strong> {{ $totalUnassigned }} Mahasiswa</td>
        </tr>
    </table>

    <!-- Tabel Data Mahasiswa -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">NIM</th>
                <th style="width: 22%;">Nama Lengkap</th>
                <th style="width: 10%;">Gender</th>
                <th style="width: 14%;">Program Studi</th>
                <th style="width: 14%;">Konsentrasi</th>
                <th style="width: 11%;">No. HP / WA</th>
                <th style="width: 13%;">Kelompok PPL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswaList as $idx => $mhs)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center"><strong>{{ $mhs->nim }}</strong></td>
                    <td><strong style="color: #0f172a;">{{ $mhs->nama }}</strong></td>
                    <td class="text-center">{{ $mhs->jenis_kelamin ?? 'Laki-laki' }}</td>
                    <td class="text-center">{{ $mhs->prodi }}</td>
                    <td class="text-center">{{ $mhs->konsentrasi ?? '-' }}</td>
                    <td class="text-center">{{ $mhs->no_hp ?? '-' }}</td>
                    <td class="text-center">
                        @if($mhs->kelompok)
                            <span class="badge-status badge-success">{{ $mhs->kelompok->nama_kelompok }}</span>
                        @else
                            <span class="badge-status badge-warning">&bull; Belum Diplotkan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #64748b;">Belum ada data mahasiswa.</td>
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
