<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Nilai PPL — {{ $kelompok->nama_kelompok }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #111;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h3 { margin: 0; font-size: 13pt; text-transform: uppercase; }
        .header h4 { margin: 2px 0; font-size: 11pt; font-weight: normal; }
        .header p { margin: 0; font-size: 8.5pt; color: #555; }
        
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            text-decoration: underline;
            margin-bottom: 15px;
        }
        
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        
        .score-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .score-table th, .score-table td {
            border: 1px solid #333;
            padding: 5px 6px;
            text-align: left;
        }
        .score-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center { text-align: center !important; }
        .font-bold { font-weight: bold; }
        
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .space-sig {
            height: 50px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>FAKULTAS EKONOMI DAN BISNIS</h3>
        <h4>UNIVERSITAS KUNINGAN</h4>
        <p>Jl. Cut Nyak Dhien No. 36A Cijoho, Kuningan, Jawa Barat | Website: feb.uniku.ac.id</p>
    </div>

    <div class="title">
        BERITA ACARA & LEMBAR NILAI RESMI PPL
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 18%;"><strong>Nama Kelompok</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 30%;">{{ $kelompok->nama_kelompok }}</td>
            <td style="width: 18%;"><strong>Tahun Akademik</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 30%;">{{ $kelompok->tahun_akademik }}</td>
        </tr>
        <tr>
            <td><strong>Mitra Penempatan</strong></td>
            <td>:</td>
            <td>{{ $kelompok->mitra->nama_mitra }} ({{ $kelompok->mitra->kategori }})</td>
            <td><strong>DPL Fakultas</strong></td>
            <td>:</td>
            <td>{{ $kelompok->dpl->nama_lengkap }}</td>
        </tr>
        <tr>
            <td><strong>Ketua Kelompok</strong></td>
            <td>:</td>
            <td>{{ $kelompok->ketua->nama_lengkap }}</td>
            <td><strong>PIC Mitra</strong></td>
            <td>:</td>
            <td>{{ $kelompok->mitra->picUser->nama_lengkap ?? '-' }}</td>
        </tr>
    </table>

    <p style="margin-bottom: 5px;"><strong>Daftar Nilai Mahasiswa (60% Mitra + 40% DPL):</strong></p>
    <table class="score-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 18%;">NIM</th>
                <th style="width: 32%;">Nama Mahasiswa</th>
                <th style="width: 15%;">Prodi</th>
                <th style="width: 10%;">Mitra (60%)</th>
                <th style="width: 10%;">DPL (40%)</th>
                <th style="width: 10%;">Nilai Akhir</th>
                <th style="width: 8%;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelompok->anggota as $idx => $mhs)
                @php
                    $p = $mhs->penilaian;
                    $mitra = $p ? $p->total_nilai_mitra : null;
                    $dpl = $p ? $p->total_nilai_dpl : null;
                    $nilaiAkhir = ($mitra !== null && $dpl !== null) ? round(($mitra * 0.60) + ($dpl * 0.40), 2) : null;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $mhs->nim }}</td>
                    <td>{{ $mhs->nama }}</td>
                    <td class="text-center">{{ $mhs->prodi }}</td>
                    <td class="text-center">{{ $mitra ?? '-' }}</td>
                    <td class="text-center">{{ $dpl ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $nilaiAkhir ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $p->nilai_huruf ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Pembimbing Lapangan Mitra</strong>
                <div class="space-sig"></div>
                <strong><u>{{ $kelompok->mitra->picUser->nama_lengkap ?? '.....................................' }}</u></strong><br>
                NIP/NIDN: {{ $kelompok->mitra->picUser->nip_nidn ?? '-' }}
            </td>
            <td>
                Kuningan, {{ date('d F Y') }}<br>
                <strong>Dosen Pembimbing Lapangan (DPL)</strong>
                <div class="space-sig"></div>
                <strong><u>{{ $kelompok->dpl->nama_lengkap }}</u></strong><br>
                NIP/NIDN: {{ $kelompok->dpl->nip_nidn ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>
