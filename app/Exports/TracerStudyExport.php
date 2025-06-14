<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting; // Added for column formatting
use Maatwebsite\Excel\Concerns\WithColumnWidths; // Added for custom column widths
use Maatwebsite\Excel\Concerns\WithEvents; // Added for events
use Maatwebsite\Excel\Events\AfterSheet; // Added for after sheet event
use PhpOffice\PhpSpreadsheet\Style\NumberFormat; // Added for number formatting
use PhpOffice\PhpSpreadsheet\Style\Alignment; // Added for alignment
use PhpOffice\PhpSpreadsheet\Style\Border; // Added for borders
use PhpOffice\PhpSpreadsheet\Style\Fill; // Added for fill
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Students;

class TracerStudyExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithColumnFormatting, WithColumnWidths, WithEvents
{
    protected $reportType;
    protected $students;
    protected $reportData;
    
    public function __construct($reportType, $students = null, $reportData = null)
    {
        $this->reportType = $reportType;
        $this->students = $students;
        $this->reportData = $reportData;
    }
    
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->reportType === 'raw') {
            return Students::with(['dataKerja', 'dataKuliah'])->get();
        }
        
        return $this->students ?: collect(); // Fixed typo: student → students
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        $baseHeadings = [
            'ID',
            'Nama Lengkap',
            'NISN',
            'Jurusan',
            'Tahun Lulus',
            'Status Setelah Lulus',
            'Jenis Kelamin',
            'Tanggal Lahir',
        ];
        
        switch ($this->reportType) {
            case 'employment':
                return array_merge($baseHeadings, [
                    'Nama Perusahaan',
                    'Posisi',
                    'Jenis Pekerjaan',
                    'Tanggal Mulai',
                    'Gaji',
                    'Sesuai Jurusan',
                    'Kompetensi Dibutuhkan',
                ]);
                
            case 'education':
                return array_merge($baseHeadings, [
                    'Nama Perguruan Tinggi',
                    'Jurusan Kuliah',
                    'Jenjang',
                    'Tahun Masuk',
                    'Status Beasiswa',
                    'Nama Beasiswa',
                    'Prestasi Akademik',
                ]);
                
            case 'raw':
                return array_merge($baseHeadings, [
                    // Employment data
                    'Nama Perusahaan',
                    'Posisi',
                    'Jenis Pekerjaan',
                    'Tanggal Mulai',
                    'Gaji',
                    'Sesuai Jurusan',
                    'Kompetensi Dibutuhkan',
                    
                    // Education data
                    'Nama Perguruan Tinggi',
                    'Jurusan Kuliah',
                    'Jenjang',
                    'Tahun Masuk',
                    'Status Beasiswa',
                    'Nama Beasiswa',
                    'Prestasi Akademik',
                ]);
                
            case 'unemployment':
            case 'general':
            default:
                return $baseHeadings;
        }
    }
    
    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        // $row adalah object Students dengan relasi dataKerja dan dataKuliah
        $jurusan = '';
        if (isset($row->jurusan_id)) {
            $jurusanModel = \App\Models\Jurusan::find($row->jurusan_id);
            $jurusan = $jurusanModel ? $jurusanModel->nama : '-';
        } else {
            $jurusan = $row->jurusan ?? '-';
        }
        $baseData = [
            $row->id,
            $row->nama_lengkap,
            $row->nisn ?? $row->nis ?? '-', // Pastikan NISN selalu muncul
            $jurusan,
            $row->tanggal_lulus ? date('Y', strtotime($row->tanggal_lulus)) : '-',
            ucfirst(str_replace('_', ' ', $row->status_setelah_lulus)),
            $row->jenis_kelamin,
            $row->tanggal_lahir ? date('d-m-Y', strtotime($row->tanggal_lahir)) : '-',
        ];
        // Data kerja
        $employmentData = $row->dataKerja ?? null;
        $employmentArr = [
            $employmentData->nama_perusahaan ?? '-',
            $employmentData->posisi ?? '-',
            $employmentData->jenis_pekerjaan ?? '-',
            isset($employmentData->tanggal_mulai) ? date('d-m-Y', strtotime($employmentData->tanggal_mulai)) : '-',
            isset($employmentData->gaji) ? 'Rp ' . number_format($employmentData->gaji, 0, ',', '.') : '-',
            $employmentData->sesuai_jurusan ?? '-',
            $employmentData->kompetensi_dibutuhkan ?? '-',
        ];
        // Data kuliah
        $educationData = $row->dataKuliah ?? null;
        $educationArr = [
            $educationData->nama_pt ?? '-',
            $educationData->jurusan ?? '-',
            $educationData->jenjang ?? '-',
            $educationData->tahun_masuk ?? '-',
            $educationData->status_beasiswa ?? '-',
            $educationData->nama_beasiswa ?? '-',
            $educationData->prestasi_akademik ?? '-',
        ];
        return array_merge($baseData, $employmentArr, $educationArr);
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        switch ($this->reportType) {
            case 'employment':
                return 'Data Pekerjaan Alumni';
            case 'education':
                return 'Data Pendidikan Alumni';
            case 'unemployment':
                return 'Data Alumni Belum Bekerja';
            case 'raw':
                return 'Data Lengkap Alumni';
            case 'general':
            default:
                return 'Laporan Tracer Study';
        }
    }
    
    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            // Removed 'E' format since it's now just a year, not a full date
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Date format for birth date
            'L' => '#,##0 "Rp";[Red]-#,##0 "Rp"', // Custom format for Indonesian Rupiah
        ];
    }
    
    /**
     * @return array
     */
    public function columnWidths(): array
    {
        // Modified column widths for single column layout
        return [
            'A' => 8,      // ID column - smaller
            'B' => 35,     // Name column - wider
            'C' => 15,      // NIS/NISN column
            'D' => 25,      // Department/Major column
            'E' => 50,     // Information column - much wider for combined data
            'F' => 15,      // Status - if needed
            'G' => 15,      // Additional info 1 - if needed
            'H' => 15,      // Additional info 2 - if needed
        ];
    }
    
    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                // Hitung total alumni dari koleksi data
                $totalAlumni = is_array($this->students) ? count($this->students) : $this->students->count();
                // Baris total alumni tepat di bawah data
                $summaryRow = $highestRow + 1;
                $sheet->setCellValue('A' . $summaryRow, 'TOTAL DATA:');
                $sheet->setCellValue('B' . $summaryRow, $totalAlumni);
                $sheet->setCellValue('C' . $summaryRow, 'alumni');
                $sheet->getStyle('A' . $summaryRow . ':C' . $summaryRow)->applyFromArray([
                    'font' => [ 'bold' => true, 'size' => 12 ],
                    'fill' => [ 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE8F0FF'] ],
                    'borders' => [ 'outline' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => 'FF3F51B5'] ] ],
                    'alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER ],
                ]);
                $sheet->mergeCells('B' . $summaryRow . ':C' . $summaryRow);
            },
        ];
    }
    
    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Apply basic styling to the header row
        return [
            1 => ['font' => ['bold' => true]], // Original header styling
            4 => ['font' => ['bold' => true]], // New header styling (after row insertions)
        ];
    }
}
