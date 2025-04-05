<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings, WithStyles
{
    protected $attendanceData;
    protected $dateRange;

    public function __construct($attendanceData, $dateRange)
    {
        $this->attendanceData = $attendanceData;
        $this->dateRange = $dateRange;
    }

    public function collection()
    {
        $rows = [];

        // Jika data kosong, tetap buat satu baris kosong agar format tetap terlihat
        if (empty($this->attendanceData)) {
            $emptyRow = ["-", "-", "-", "-"];

            foreach ($this->dateRange as $day) {
                $emptyRow[] = "-"; // Kolom tanggal tetap ada
            }

            return collect([$emptyRow]);
        }

        // Tambahkan data karyawan
        foreach ($this->attendanceData as $data) {
            $row = [
                $data['name'],
                $data['nip'],
                $data['division'],
                $data['position'],
            ];

            foreach ($data['dates'] as $date => $status) {
                $row[] = $status ?: "-"; // Jika status kosong, beri tanda "-"
            }

            // Statistik Kehadiran
            $row[] = $data['stats']['H'] ?? 0;
            $row[] = $data['stats']['T'] ?? 0;
            $row[] = $data['stats']['I'] ?? 0;
            $row[] = $data['stats']['S'] ?? 0;
            $row[] = $data['stats']['A'] ?? 0;
            $row[] = $data['stats']['L'] ?? 0;

            $rows[] = $row;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        $headings = ["Nama", "NIP", "Divisi", "Jabatan"];

        foreach ($this->dateRange as $day) {
            $formattedDate = Carbon::parse($day['date'])->format('d/m');
            $dayName = Carbon::parse($day['date'])->isoFormat('ddd');
            $headings[] = "$formattedDate ($dayName)";
        }

        return array_merge($headings, ["H", "T", "I", "S", "A", "L"]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Buat header tebal
        ];
    }
}

