<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Nilai PPL Mahasiswa Fakultas</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
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
        
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 15px;
        }
        
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-data th, .table-data td {
            border: 1px solid #333;
            padding: 4px 5px;
            text-align: left;
        }
        .table-data th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center { text-align: center !important; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h3>FAKULTAS EKONOMI DAN BISNIS — UNIVERSITAS KUNINGAN</h3>
        <h4>REKAPITULASI NILAI AKHIR MAHASISWA PRAKTIK PENGENALAN LAPANGAN (PPL)</h4>
    </div>

    <div class="title">
        TAHUN AKADEMIK {{ date('Y') }}/{{ date('Y')+1 }}
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">NIM</th>
                <th style="width: 22%;">Nama Mahasiswa</th>
                <th style="width: 12%;">Prodi</th>
                <th style="width: 16%;">Nama Kelompok</th>
                <th style="width: 14%;">Mitra</th>
                <th style="width: 8%;">Mitra (60%)</th>
                <th style="width: 8%;">DPL (40%)</th>
                <th style="width: 7%;">Nilai Akhir</th>
                <th style="width: 5%;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mahasiswaList as $idx => $mhs)
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
                    <td>{{ $mhs->kelompok->nama_kelompok ?? '-' }}</td>
                    <td>{{ $mhs->kelompok->mitra->nama_mitra ?? '-' }}</td>
                    <td class="text-center">{{ $mitra ?? '-' }}</td>
                    <td class="text-center">{{ $dpl ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $nilaiAkhir ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $p->nilai_huruf ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                Kuningan, {{ date('d F Y') }}<br>
                <strong>Ketua Unit PPL FEB UNIKU</strong>
                <br><br><br><br>
                <strong><u>Administrator PPL FEB</u></strong>
            </td>
        </tr>
    </table>

</body>
</html>
