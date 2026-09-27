<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Export rekapitulasi nilai PPL ke Excel (.xlsx) dengan kop FEB UNIKU
 * (diadopsi dari SystemPenilaianPPL).
 */
class RekapNilaiExportService
{
    private const HEADERS = [
        'No',
        'NIM',
        'Nama Mahasiswa',
        'Program Studi',
        'Kelompok PPL',
        'Lokasi / Mitra',
        'Dosen Pembimbing (DPL)',
        'Nilai Mitra (60%)',
        'Nilai DPL (40%)',
        'Nilai Akhir',
        'Nilai Huruf',
        'Status Nilai',
    ];

    public static function download(Builder $query, ?string $keterangan = null): StreamedResponse
    {
        $filename = 'rekap_nilai_ppl_feb_uniku_' . date('Ymd_His') . '.xlsx';

        return response()->stream(function () use ($query, $keterangan) {
            $writer = new Xlsx(self::buildSpreadsheet($query, $keterangan));
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public static function buildSpreadsheet(Builder $query, ?string $keterangan = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Nilai PPL');

        $sheet->setCellValue('A1', 'FAKULTAS EKONOMI DAN BISNIS - UNIVERSITAS KUNINGAN');
        $sheet->setCellValue('A2', 'REKAPITULASI NILAI PRAKTIK PENGALAMAN LAPANGAN (PPL)');
        $sheet->setCellValue('A3', trim(($keterangan ? $keterangan . ' | ' : '') . 'Tanggal Cetak: ' . date('d/m/Y H:i')));

        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('1E40AF'));
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->setColor(new Color('6B7280'));

        $headerRow = 5;
        foreach (self::HEADERS as $colIndex => $headerText) {
            $sheet->setCellValue(chr(65 + $colIndex) . $headerRow, $headerText);
        }

        $sheet->getStyle("A{$headerRow}:L{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri', 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        $query = (clone $query)
            ->with(['kelompok.mitra', 'kelompok.dpl', 'penilaian'])
            ->reorder()
            ->orderBy('kelompok_id')
            ->orderBy('nim');

        $row = $headerRow + 1;
        $no = 1;

        foreach ($query->cursor() as $mhs) {
            $p = $mhs->penilaian;

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValueExplicit("B{$row}", (string) $mhs->nim, DataType::TYPE_STRING);
            $sheet->setCellValue("C{$row}", $mhs->nama);
            $sheet->setCellValue("D{$row}", $mhs->prodi ?? '-');
            $sheet->setCellValue("E{$row}", $mhs->kelompok?->nama_kelompok ?? '-');
            $sheet->setCellValue("F{$row}", $mhs->kelompok?->mitra?->nama_mitra ?? '-');
            $sheet->setCellValue("G{$row}", $mhs->kelompok?->dpl?->nama_lengkap ?? '-');

            foreach (['H' => $p?->nilai_mitra, 'I' => $p?->nilai_dpl, 'J' => $p?->nilai_akhir] as $col => $nilai) {
                if ($nilai !== null) {
                    $sheet->setCellValue("{$col}{$row}", (float) $nilai);
                    $sheet->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('0.00');
                } else {
                    $sheet->setCellValue("{$col}{$row}", '-');
                }
            }

            $sheet->setCellValue("K{$row}", $p?->nilai_huruf ?? '-');
            $sheet->setCellValue("L{$row}", match (true) {
                $p?->isLocked() => 'Final (Terkunci)',
                $p?->nilai_akhir !== null => 'Draft',
                default => 'Belum Lengkap',
            });

            $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            ]);
            foreach (['A', 'B', 'D', 'K', 'L'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $sheet->getStyle("H{$row}:J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getRowDimension($row)->setRowHeight(22);

            $row++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
