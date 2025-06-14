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

                // Header styling
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF3F51B5'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Data styling
                if ($highestRow > 1) {
                    $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FFCCCCCC'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                // Summary row
                $summaryRow = $highestRow + 2;
                $sheet->setCellValue('A' . $summaryRow, 'TOTAL DATA:');
                $sheet->setCellValue('B' . $summaryRow, $totalAlumni);
                $sheet->setCellValue('C' . $summaryRow, 'alumni');

                // Merge cells untuk summary
                $sheet->mergeCells('B' . $summaryRow . ':C' . $summaryRow);

                // Summary styling
                $sheet->getStyle('A' . $summaryRow . ':C' . $summaryRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['argb' => 'FF3F51B5'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE8F0FF'],
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['argb' => 'FF3F51B5'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Tanggal laporan
                $dateRow = $summaryRow + 1;
                $sheet->setCellValue('A' . $dateRow, 'Tanggal Laporan: ' . now()->format('d M Y H:i:s'));
                $sheet->mergeCells('A' . $dateRow . ':D' . $dateRow);
                $sheet->getStyle('A' . $dateRow)->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'italic' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
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
