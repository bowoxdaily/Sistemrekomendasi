<?php

namespace App\Exports;

use App\Models\Jurusan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AkunSiswaTemplate implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template Siswa' => new AkunSiswaTemplateSheet(),
            
        ];
    }
}

// Sheet 1: Template Input Siswa
class AkunSiswaTemplateSheet implements FromCollection, WithHeadings, WithColumnWidths, WithStyles
{
    public function collection()
    {
        return new Collection([
            [
                'nisn' => '1234567890',
                'nama_lengkap' => 'John Doe',
                'email' => 'siswa@example.com',
                'password' => 'password123',
                'jurusan' => 'TKJ (kode Jurusan)' // kode jurusan
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'nisn',
            'nama_lengkap',
            'email',
            'password',
            'jurusan' // isikan dengan kode jurusan, contoh: RPL, TKJ, AK
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // nisn
            'B' => 30, // nama_lengkap
            'C' => 30, // email
            'D' => 20, // password
            'E' => 20, // jurusan (kode)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // Heading
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFCCE5FF']
                ]
            ],
            2 => [ // Baris contoh
                'font' => ['italic' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFEAF4FC']
                ]
            ]
        ];
    }
}

// Sheet 2: Menampilkan daftar kode jurusan dari database

