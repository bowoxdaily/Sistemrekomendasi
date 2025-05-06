<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AkunSiswaTemplate implements FromCollection, WithHeadings, WithColumnWidths, WithStyles
{
    /**
     * Contoh data dummy untuk memandu user
     */
    public function collection()
    {
        return new Collection([
            [
                'nisn' => '1234567890',
                'email' => 'siswa@example.com',
                'password' => 'password123'
            ]
        ]);
    }

    /**
     * Judul kolom sesuai field yang dibutuhkan untuk import
     */
    public function headings(): array
    {
        return [
            'nisn',
            'email',
            'password'
        ];
    }

    /**
     * Lebar kolom agar tampilan Excel lebih rapi
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20, // nisn
            'B' => 30, // email
            'C' => 20, // password
        ];
    }

    /**
     * Styling untuk header dan baris contoh
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFCCE5FF']
                ]
            ],
            2 => [
                'font' => ['italic' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFEAF4FC']
                ]
            ]
        ];
    }
}
