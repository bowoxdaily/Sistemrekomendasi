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
        // Get jurusan name from relationship instead of just ID
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
            $row->nisn ?? $row->nis, // Use nisn if available, otherwise use nis
            // Use the jurusan name retrieved above 
            $jurusan, // Now using the properly retrieved jurusan name
            // Make sure we only get the year from tanggal_lulus
            $row->tanggal_lulus ? date('Y', strtotime($row->tanggal_lulus)) : '-',
            // Convert status to a more readable format 
            ucfirst(str_replace('_', ' ', $row->status_setelah_lulus)),
            $row->jenis_kelamin,
            $row->tanggal_lahir ? date('d-m-Y', strtotime($row->tanggal_lahir)) : '-',
        ];
        
        switch ($this->reportType) {
            case 'employment':
                $employmentData = $row->dataKerja ?? null;
                return array_merge($baseData, [
                    isset($employmentData) ? ($employmentData->nama_perusahaan ?? '-') : '-',
                    isset($employmentData) ? ($employmentData->posisi ?? '-') : '-',
                    isset($employmentData) ? ($employmentData->jenis_pekerjaan ?? '-') : '-',
                    isset($employmentData) && isset($employmentData->tanggal_mulai) ? date('d-m-Y', strtotime($employmentData->tanggal_mulai)) : '-',
                    isset($employmentData) && isset($employmentData->gaji) ? 'Rp ' . number_format($employmentData->gaji, 0, ',', '.') : '-',
                    isset($employmentData) ? ($employmentData->sesuai_jurusan ?? '-') : '-',
                    isset($employmentData) ? ($employmentData->kompetensi_dibutuhkan ?? '-') : '-',
                ]);
                
            case 'education':
                $educationData = $row->dataKuliah ?? null;
                return array_merge($baseData, [
                    isset($educationData) ? ($educationData->nama_pt ?? '-') : '-',
                    isset($educationData) ? ($educationData->jurusan ?? '-') : '-',
                    isset($educationData) ? ($educationData->jenjang ?? '-') : '-',
                    isset($educationData) ? ($educationData->tahun_masuk ?? '-') : '-',
                    isset($educationData) ? ($educationData->status_beasiswa ?? '-') : '-',
                    isset($educationData) ? ($educationData->nama_beasiswa ?? '-') : '-',
                    isset($educationData) ? ($educationData->prestasi_akademik ?? '-') : '-',
                ]);
                
            case 'raw':
                $employmentData = $row->dataKerja ?? null;
                $educationData = $row->dataKuliah ?? null;
                
                return array_merge($baseData, [
                    // Employment data
                    isset($employmentData) ? ($employmentData->nama_perusahaan ?? '-') : '-',
                    isset($employmentData) ? ($employmentData->posisi ?? '-') : '-',
                    isset($employmentData) ? ($employmentData->jenis_pekerjaan ?? '-') : '-',
                    isset($employmentData) && isset($employmentData->tanggal_mulai) ? date('d-m-Y', strtotime($employmentData->tanggal_mulai)) : '-',
                    isset($employmentData) && isset($employmentData->gaji) ? 'Rp ' . number_format($employmentData->gaji, 0, ',', '.') : '-',
                    isset($employmentData) ? ($employmentData->sesuai_jurusan ?? '-') : '-',
                    isset($employmentData) ? ($employmentData->kompetensi_dibutuhkan ?? '-') : '-',
                    
                    // Education data
                    isset($educationData) ? ($educationData->nama_pt ?? '-') : '-',
                    isset($educationData) ? ($educationData->jurusan ?? '-') : '-',
                    isset($educationData) ? ($educationData->jenjang ?? '-') : '-',
                    isset($educationData) ? ($educationData->tahun_masuk ?? '-') : '-',
                    isset($educationData) ? ($educationData->status_beasiswa ?? '-') : '-',
                    isset($educationData) ? ($educationData->nama_beasiswa ?? '-') : '-',
                    isset($educationData) ? ($educationData->prestasi_akademik ?? '-') : '-',
                ]);
                
            case 'unemployment':
            case 'general':
            default:
                return $baseData;
        }
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
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Get the highest row and column
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // First apply the header styling to the first row (before title insertion)
                $headerRange = 'A1:' . $highestColumn . '1';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF3F51B5'], // Deep blue color
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                
                // Add title rows before the header
                $sheet->insertNewRowBefore(1, 3);
                
                // Now the original header row is at row 4
                // Update the header styling
                $headerRange = 'A4:' . $highestColumn . '4';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF3F51B5'], // Deep blue color
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                
                // Determine report title based on report type
                $reportTitle = match($this->reportType) {
                    'employment' => 'LAPORAN DATA PEKERJAAN ALUMNI',
                    'education' => 'LAPORAN DATA PENDIDIKAN ALUMNI', 
                    'unemployment' => 'LAPORAN DATA ALUMNI BELUM BEKERJA',
                    'raw' => 'DATA LENGKAP ALUMNI',
                    default => 'LAPORAN TRACER STUDY',
                };
                
                // Set up report header in the newly inserted rows
                $sheet->setCellValue('A1', $reportTitle);
                $sheet->setCellValue('A2', 'SISTEM TRACER STUDY & REKOMENDASI KARIR');
                $sheet->setCellValue('A3', 'Tanggal: ' . date('d-m-Y H:i'));
                
                // Style the title rows
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->mergeCells('A3:' . $highestColumn . '3');
                
                $sheet->getStyle('A1:' . $highestColumn . '3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Make title row 1 stand out with bigger font and background
                $sheet->getStyle('A1')->getFont()->setSize(16);
                $sheet->getStyle('A2')->getFont()->setSize(14);
                
                // Background color for the title
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFDBE1FF'], // Light blue background
                    ],
                    'font' => [
                        'color' => ['argb' => 'FF3F51B5'], // Dark blue text
                    ],
                ]);
                
                // Apply auto filter to the header row
                $sheet->setAutoFilter($headerRange);
                
                // Freeze the header row for easier navigation
                $sheet->freezePane('A5');
                
                // Apply zebra striping to data rows
                for ($row = 5; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF5F7FF'], // Light blue for even rows
                            ],
                        ]);
                    }
                }
                
                // Apply borders to all cells
                $tableRange = 'A1:' . $highestColumn . $highestRow;
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFD1D1D1'],
                        ],
                    ],
                ]);
                
                // Enable text wrapping for data cells
                $dataRange = 'A5:' . $highestColumn . $highestRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'alignment' => [
                        'wrapText' => true,
                        'vertical' => Alignment::VERTICAL_TOP,
                    ],
                ]);
                
                // Set column widths for optimal display
                $sheet->getColumnDimension('A')->setWidth(10); // No column
                $sheet->getColumnDimension('B')->setWidth(30); // Name
                $sheet->getColumnDimension('C')->setWidth(15); // NISN
                $sheet->getColumnDimension('D')->setWidth(25); // Jurusan
                $sheet->getColumnDimension('E')->setWidth(15); // Year
                $sheet->getColumnDimension('F')->setWidth(20); // Status
                
                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(25);
                $sheet->getRowDimension(4)->setRowHeight(30); // Header row
                
                // Auto-height for data rows
                for ($row = 5; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
                
                // Make sure we use the correct row counting starting from index 1
                // Get the actual data row count (excluding header)
                $dataStartRow = 5; // First row of actual data (after headers and titles)
                $actualDataCount = ($highestRow - $dataStartRow + 1); // Count from index 1

                // Add summary at the very bottom with fixed calculation
                if ($actualDataCount > 0) {
                    // Position summary two rows below the last data row
                    $summaryRow = $highestRow + 2;
                    
                    // Create a bold, highlighted summary row
                    $sheet->setCellValue('A' . $summaryRow, 'TOTAL DATA:');
                    $sheet->setCellValue('B' . $summaryRow, $actualDataCount);
                    $sheet->setCellValue('C' . $summaryRow, 'alumni');
                    
                    // Style the summary row to stand out clearly at the bottom
                    $sheet->getStyle('A' . $summaryRow . ':C' . $summaryRow)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFE8F0FF'],
                        ],
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF3F51B5'],
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    
                    // Set row height for summary and merge cells for a cleaner look
                    $sheet->getRowDimension($summaryRow)->setRowHeight(25);
                    $sheet->mergeCells('B' . $summaryRow . ':C' . $summaryRow);
                }
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
