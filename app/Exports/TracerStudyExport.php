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
            return Students::with(['dataKerja', 'dataKuliah', 'jurusan'])->get();
        }

        // Pastikan $this->students adalah collection
        if ($this->students) {
            return collect($this->students);
        }

        return collect(); // Return empty collection jika tidak ada data
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
                    'Kesesuaian Jurusan',
                    'Kompetensi Dibutuhkan',
                ]);

            case 'education':
                return array_merge($baseHeadings, [
                    'Nama Perguruan Tinggi',
                    'Program Studi',
                    'Jenjang',
                    'Tahun Masuk',
                    'Status Beasiswa',
                    'Nama Beasiswa',
                    'Prestasi Akademik',
                ]);

            case 'unemployment':
                return array_merge($baseHeadings, [
                    'Alamat',
                    'Durasi Lulus',
                    'Kontak',
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

            case 'general':
            default:
                return array_merge($baseHeadings, [
                    'Alamat',
                    'Kontak',
                ]);
        }
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        // Base data yang selalu ada
        $baseData = [
            $row->id,
            $row->nama_lengkap,
            $row->nisn ?? $row->nis ?? '-',
            $row->jurusan ? $row->jurusan->nama : '-', // Menggunakan relasi jurusan
            $row->tanggal_lulus ? date('Y', strtotime($row->tanggal_lulus)) : '-',
            ucfirst(str_replace('_', ' ', $row->status_setelah_lulus)),
            ucfirst($row->jenis_kelamin ?? '-'),
            $row->tanggal_lahir ? date('d-m-Y', strtotime($row->tanggal_lahir)) : '-',
        ];

        switch ($this->reportType) {
            case 'employment':
                $employmentData = $row->dataKerja ?? null;
                return array_merge($baseData, [
                    $employmentData->nama_perusahaan ?? '-',
                    $employmentData->posisi ?? '-',
                    $employmentData->jenis_pekerjaan ?? '-',
                    isset($employmentData->tanggal_mulai) ? date('d-m-Y', strtotime($employmentData->tanggal_mulai)) : '-',
                    isset($employmentData->gaji) ? 'Rp ' . number_format($employmentData->gaji, 0, ',', '.') : '-',
                    ($employmentData->sesuai_jurusan ?? '-') == 'ya' ? 'Sesuai Jurusan' : 'Tidak Sesuai',
                    $employmentData->kompetensi_dibutuhkan ?? '-',
                ]);

            case 'education':
                $educationData = $row->dataKuliah ?? null;
                return array_merge($baseData, [
                    $educationData->nama_pt ?? '-',
                    $educationData->jurusan ?? '-',
                    $educationData->jenjang ?? '-',
                    $educationData->tahun_masuk ?? '-',
                    ($educationData->status_beasiswa ?? '-') == 'ya' ? 'Beasiswa' : 'Non-Beasiswa',
                    ($educationData->status_beasiswa ?? '-') == 'ya' ? ($educationData->nama_beasiswa ?? '-') : '-',
                    $educationData->prestasi_akademik ?? '-',
                ]);

            case 'unemployment':
                // Untuk unemployment, tambahkan kolom alamat dan durasi lulus
                $duration = '-';
                if ($row->tanggal_lulus) {
                    $graduationDate = \Carbon\Carbon::parse($row->tanggal_lulus);
                    $now = \Carbon\Carbon::now();
                    $durationMonths = $graduationDate->diffInMonths($now);
                    if ($durationMonths < 12) {
                        $duration = $durationMonths . ' bulan';
                    } else {
                        $years = floor($durationMonths / 12);
                        $months = $durationMonths % 12;
                        $duration = $years . ' tahun ' . ($months > 0 ? $months . ' bulan' : '');
                    }
                }
                return array_merge($baseData, [
                    $row->alamat ?? '-',
                    $duration,
                    $row->no_hp ?? '-',
                ]);

            case 'raw':
                // Data lengkap (gabungan semua)
                $employmentData = $row->dataKerja ?? null;
                $educationData = $row->dataKuliah ?? null;
                $employmentArr = [
                    $employmentData->nama_perusahaan ?? '-',
                    $employmentData->posisi ?? '-',
                    $employmentData->jenis_pekerjaan ?? '-',
                    isset($employmentData->tanggal_mulai) ? date('d-m-Y', strtotime($employmentData->tanggal_mulai)) : '-',
                    isset($employmentData->gaji) ? 'Rp ' . number_format($employmentData->gaji, 0, ',', '.') : '-',
                    $employmentData->sesuai_jurusan ?? '-',
                    $employmentData->kompetensi_dibutuhkan ?? '-',
                ];
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

            case 'general':
            default:
                // Data umum dengan tambahan alamat dan kontak
                return array_merge($baseData, [
                    $row->alamat ?? '-',
                    $row->no_hp ?? '-',
                ]);
        }
    }

    /**
     * @return string
     */
    public function title(): string
    {
        switch ($this->reportType) {
            case 'employment':
                return 'Laporan Data Pekerjaan Alumni';
            case 'education':
                return 'Laporan Data Pendidikan Alumni';
            case 'unemployment':
                return 'Laporan Alumni Belum Bekerja';
            case 'raw':
                return 'Data Lengkap Alumni';
            case 'general':
            default:
                return 'Laporan Umum Alumni';
        }
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Tanggal lahir
            'M' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE, // Gaji (untuk employment)
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        // Base column widths
        $baseWidths = [
            'A' => 8,      // ID
            'B' => 25,     // Nama
            'C' => 15,     // NISN
            'D' => 25,     // Jurusan
            'E' => 12,     // Tahun Lulus
            'F' => 18,     // Status
            'G' => 15,     // Jenis Kelamin
            'H' => 15,     // Tanggal Lahir
        ];

        switch ($this->reportType) {
            case 'employment':
                return array_merge($baseWidths, [
                    'I' => 25,     // Perusahaan
                    'J' => 20,     // Posisi
                    'K' => 18,     // Jenis Pekerjaan
                    'L' => 15,     // Tanggal Mulai
                    'M' => 18,     // Gaji
                    'N' => 18,     // Kesesuaian
                    'O' => 30,     // Kompetensi
                ]);

            case 'education':
                return array_merge($baseWidths, [
                    'I' => 30,     // Perguruan Tinggi
                    'J' => 25,     // Program Studi
                    'K' => 12,     // Jenjang
                    'L' => 12,     // Tahun Masuk
                    'M' => 15,     // Status Beasiswa
                    'N' => 20,     // Nama Beasiswa
                    'O' => 20,     // Prestasi
                ]);

            case 'unemployment':
                return array_merge($baseWidths, [
                    'I' => 30,     // Alamat
                    'J' => 15,     // Durasi Lulus
                    'K' => 15,     // Kontak
                ]);

            case 'general':
                return array_merge($baseWidths, [
                    'I' => 30,     // Alamat
                    'J' => 15,     // Kontak
                ]);

            case 'raw':
            default:
                return array_merge($baseWidths, [
                    'I' => 25,     // Perusahaan
                    'J' => 20,     // Posisi
                    'K' => 18,     // Jenis Pekerjaan
                    'L' => 15,     // Tanggal Mulai
                    'M' => 18,     // Gaji
                    'N' => 18,     // Sesuai Jurusan
                    'O' => 30,     // Kompetensi
                    'P' => 30,     // PT
                    'Q' => 25,     // Jurusan Kuliah
                    'R' => 12,     // Jenjang
                    'S' => 12,     // Tahun Masuk
                    'T' => 15,     // Status Beasiswa
                    'U' => 20,     // Nama Beasiswa
                    'V' => 20,     // Prestasi
                ]);
        }
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Hitung total alumni langsung dari data students
                $totalAlumni = 0;
                if ($this->students) {
                    if (is_array($this->students)) {
                        $totalAlumni = count($this->students);
                    } elseif (is_object($this->students) && method_exists($this->students, 'count')) {
                        $totalAlumni = $this->students->count();
                    } elseif (is_object($this->students) && method_exists($this->students, 'toArray')) {
                        $totalAlumni = count($this->students->toArray());
                    }
                }

                // Logo/Header area (first add a title row)
                $sheet->insertNewRowBefore(1, 3);

                // Main title
                $sheet->setCellValue('A1', 'LAPORAN TRACER STUDY ALUMNI');
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF2E7D32'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension('1')->setRowHeight(30);

                // Subtitle
                $subtitle = '';
                switch ($this->reportType) {
                    case 'employment':
                        $subtitle = 'LAPORAN DATA PEKERJAAN ALUMNI';
                        break;
                    case 'education':
                        $subtitle = 'LAPORAN DATA PENDIDIKAN ALUMNI';
                        break;
                    case 'unemployment':
                        $subtitle = 'LAPORAN ALUMNI BELUM BEKERJA';
                        break;
                    case 'raw':
                        $subtitle = 'DATA LENGKAP ALUMNI';
                        break;
                    default:
                        $subtitle = 'LAPORAN UMUM ALUMNI';
                        break;
                }

                $sheet->setCellValue('A2', $subtitle);
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['argb' => 'FF2E7D32'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Info tanggal
                $sheet->setCellValue('A3', 'Digenerate pada: ' . now()->format('d F Y, H:i:s WIB'));
                $sheet->mergeCells('A3:' . $highestColumn . '3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'italic' => true,
                        'color' => ['argb' => 'FF666666'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Adjust row heights
                $sheet->getRowDimension('2')->setRowHeight(25);
                $sheet->getRowDimension('3')->setRowHeight(20);

                // Header data styling (now row 4)
                $headerRow = 4;
                $sheet->getStyle('A' . $headerRow . ':' . $highestColumn . $headerRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF3F51B5'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFFFF'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(25);

                // Data styling
                $dataStartRow = $headerRow + 1;
                if ($highestRow > $headerRow) {
                    // Zebra striping
                    for ($row = $dataStartRow; $row <= $highestRow + 3; $row++) {
                        $fillColor = ($row % 2 == 0) ? 'FFF8F9FA' : 'FFFFFFFF';
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => $fillColor],
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => 'FFE0E0E0'],
                                ],
                            ],
                            'alignment' => [
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => true,
                            ],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(20);
                    }

                    // Nama kolom styling (make names bold)
                    $sheet->getStyle('B' . $dataStartRow . ':B' . ($highestRow + 3))->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['argb' => 'FF2C3E50'],
                        ],
                    ]);
                }

                // Summary section
                $summaryStartRow = $highestRow + 6;

                // Summary header
                $sheet->setCellValue('A' . $summaryStartRow, 'RINGKASAN LAPORAN');
                $sheet->mergeCells('A' . $summaryStartRow . ':E' . $summaryStartRow);
                $sheet->getStyle('A' . $summaryStartRow . ':E' . $summaryStartRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF2E7D32'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['argb' => 'FF2E7D32'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($summaryStartRow)->setRowHeight(30);

                // Summary content
                $summaryDataRow = $summaryStartRow + 1;
                $sheet->setCellValue('A' . $summaryDataRow, 'Total Data Alumni:');
                $sheet->setCellValue('B' . $summaryDataRow, $totalAlumni);
                $sheet->setCellValue('C' . $summaryDataRow, 'orang');

                $sheet->setCellValue('A' . ($summaryDataRow + 1), 'Jenis Laporan:');
                $sheet->setCellValue('B' . ($summaryDataRow + 1), ucfirst(str_replace('_', ' ', $this->reportType)));

                $sheet->setCellValue('A' . ($summaryDataRow + 2), 'Tanggal Export:');
                $sheet->setCellValue('B' . ($summaryDataRow + 2), now()->format('d F Y, H:i:s'));

                // Summary styling
                $sheet->getStyle('A' . $summaryDataRow . ':C' . ($summaryDataRow + 2))->applyFromArray([
                    'font' => [
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF0F8F0'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF2E7D32'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Make summary labels bold
                $sheet->getStyle('A' . $summaryDataRow . ':A' . ($summaryDataRow + 2))->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FF2E7D32'],
                    ],
                ]);

                // Footer
                $footerRow = $summaryDataRow + 4;
                $sheet->setCellValue('A' . $footerRow, 'Generated by Sistem Tracer Study Alumni - SMK [Nama Sekolah]');
                $sheet->mergeCells('A' . $footerRow . ':' . $highestColumn . $footerRow);
                $sheet->getStyle('A' . $footerRow)->applyFromArray([
                    'font' => [
                        'size' => 9,
                        'italic' => true,
                        'color' => ['argb' => 'FF999999'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Freeze panes pada header data
                $sheet->freezePane('A' . ($headerRow + 1));
            },
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header styling akan ditangani di registerEvents
        ];
    }
}
