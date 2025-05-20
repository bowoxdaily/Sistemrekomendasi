<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Temporarily disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Delete existing records to avoid duplicates
        Jurusan::truncate();
        
        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $jurusanData = [
            [
                'kode' => 'RPL',
                'nama' => 'Rekayasa Perangkat Lunak',
                'deskripsi' => 'Program keahlian yang mempelajari pengembangan perangkat lunak termasuk pemrograman, desain aplikasi, dan manajemen proyek IT.'
            ],
            [
                'kode' => 'TKJ',
                'nama' => 'Teknik Komputer dan Jaringan',
                'deskripsi' => 'Program keahlian yang mempelajari perakitan komputer, instalasi jaringan, dan administrasi sistem.'
            ],
            [
                'kode' => 'MM',
                'nama' => 'Multimedia',
                'deskripsi' => 'Program keahlian yang mempelajari produksi konten digital, desain grafis, animasi, dan pengembangan media interaktif.'
            ],
            [
                'kode' => 'AKL',
                'nama' => 'Akuntansi dan Keuangan Lembaga',
                'deskripsi' => 'Program keahlian yang mempelajari pencatatan transaksi keuangan, pembuatan laporan keuangan, dan pengelolaan kas.'
            ],
            [
                'kode' => 'OTKP',
                'nama' => 'Otomatisasi dan Tata Kelola Perkantoran',
                'deskripsi' => 'Program keahlian yang mempelajari administrasi perkantoran, kesekretariatan, dan penggunaan perangkat teknologi kantor.'
            ],
            [
                'kode' => 'BDP',
                'nama' => 'Bisnis Daring dan Pemasaran',
                'deskripsi' => 'Program keahlian yang mempelajari strategi pemasaran digital, e-commerce, dan manajemen bisnis online.'
            ],
            [
                'kode' => 'TB',
                'nama' => 'Tata Boga',
                'deskripsi' => 'Program keahlian yang mempelajari pengolahan makanan, pastry, tata hidang, dan manajemen usaha kuliner.'
            ],
            [
                'kode' => 'HTL',
                'nama' => 'Perhotelan',
                'deskripsi' => 'Program keahlian yang mempelajari layanan akomodasi, tata graha, front office, dan manajemen hotel.'
            ],
            [
                'kode' => 'TSM',
                'nama' => 'Teknik Sepeda Motor',
                'deskripsi' => 'Program keahlian yang mempelajari perawatan dan perbaikan sepeda motor, sistem kelistrikan, dan mesin.'
            ],
            [
                'kode' => 'TKR',
                'nama' => 'Teknik Kendaraan Ringan',
                'deskripsi' => 'Program keahlian yang mempelajari perawatan dan perbaikan mobil, sistem transmisi, dan kelistrikan otomotif.'
            ],
            [
                'kode' => 'TAV',
                'nama' => 'Teknik Audio Video',
                'deskripsi' => 'Program keahlian yang mempelajari instalasi, perawatan, dan perbaikan perangkat audio visual dan elektronika.'
            ],
            [
                'kode' => 'TBSM',
                'nama' => 'Teknik dan Bisnis Sepeda Motor',
                'deskripsi' => 'Program keahlian yang mempelajari servis, perbaikan sepeda motor serta pengembangan bisnis jasa otomotif.'
            ],
            [
                'kode' => 'AP',
                'nama' => 'Akomodasi Perhotelan',
                'deskripsi' => 'Program keahlian yang mempelajari layanan dalam bidang perhotelan, tata graha, dan pengelolaan akomodasi.'
            ],
            [
                'kode' => 'UPW',
                'nama' => 'Usaha Perjalanan Wisata',
                'deskripsi' => 'Program keahlian yang mempelajari manajemen perjalanan, layanan pemandu wisata, dan pengelolaan bisnis perjalanan.'
            ],
            [
                'kode' => 'APHP',
                'nama' => 'Agribisnis Pengolahan Hasil Pertanian',
                'deskripsi' => 'Program keahlian yang mempelajari pengolahan, pengawetan, dan pengembangan produk hasil pertanian.'
            ]
        ];

        foreach ($jurusanData as $jurusan) {
            // Check if record already exists to avoid duplicates
            $exists = Jurusan::where('kode', $jurusan['kode'])->exists();
            if (!$exists) {
                Jurusan::create($jurusan);
            }
        }

        $this->command->info('Jurusan seeded successfully!');
    }
}
