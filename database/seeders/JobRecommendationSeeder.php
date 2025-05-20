<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobRecommendation;
use Illuminate\Support\Facades\DB;

class JobRecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Temporarily disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clean existing job records for consistent development
        JobRecommendation::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $jobs = [
            // Tech Jobs
            [
                'name' => 'Software Developer',
                'description' => 'Merancang, mengembangkan, dan memelihara aplikasi perangkat lunak sesuai dengan kebutuhan pengguna.',
                'industry_type' => 'Teknologi Informasi',
                'average_salary' => 9000000,
                'requirements' => [
                    'Pendidikan minimal S1 Teknik Informatika atau jurusan serupa',
                    'Memahami minimal satu bahasa pemrograman (Java, Python, JavaScript, dll)',
                    'Memiliki kemampuan berpikir logis dan memecahkan masalah',
                    'Kemampuan bekerja dalam tim'
                ],
                'skills_needed' => [
                    'Pemrograman', 'Algoritma', 'Struktur Data', 'Git', 'Database'
                ],
                'criteria_values' => [
                    'education' => 4,
                    'experience' => 3,
                    'technical' => 5,
                    'soft_skills' => 3
                ]
            ],
            [
                'name' => 'Data Analyst',
                'description' => 'Menganalisis data untuk mengidentifikasi tren dan pola yang dapat membantu perusahaan dalam pengambilan keputusan.',
                'industry_type' => 'Teknologi Informasi',
                'average_salary' => 8500000,
                'requirements' => [
                    'Pendidikan minimal S1 di bidang Statistik, Matematika, atau IT',
                    'Kemampuan analisis statistik dan interpretasi data',
                    'Pemahaman tentang database dan SQL',
                    'Kemampuan visualisasi data yang baik'
                ],
                'skills_needed' => [
                    'SQL', 'Excel', 'Python/R', 'Statistik', 'Visualisasi Data', 'Power BI/Tableau'
                ],
                'criteria_values' => [
                    'education' => 4,
                    'experience' => 3,
                    'technical' => 4,
                    'soft_skills' => 3
                ]
            ],
            [
                'name' => 'UI/UX Designer',
                'description' => 'Merancang antarmuka dan pengalaman pengguna untuk aplikasi atau situs web agar mudah digunakan dan menarik.',
                'industry_type' => 'Desain',
                'average_salary' => 8000000,
                'requirements' => [
                    'Pendidikan minimal D3/S1 Desain, Komunikasi Visual, atau bidang terkait',
                    'Memiliki portofolio desain yang baik',
                    'Pemahaman tentang prinsip-prinsip desain dan UX',
                    'Kemampuan menggunakan perangkat lunak desain'
                ],
                'skills_needed' => [
                    'Adobe XD', 'Figma', 'Sketch', 'Wireframing', 'Prototyping', 'User Research'
                ],
                'criteria_values' => [
                    'education' => 3,
                    'experience' => 4,
                    'technical' => 4,
                    'soft_skills' => 4
                ]
            ],

            // Business Jobs
            [
                'name' => 'Marketing Specialist',
                'description' => 'Mengembangkan dan melaksanakan strategi pemasaran untuk mempromosikan produk atau layanan perusahaan.',
                'industry_type' => 'Pemasaran',
                'average_salary' => 7500000,
                'requirements' => [
                    'Pendidikan minimal S1 Marketing, Komunikasi, atau bidang terkait',
                    'Pemahaman tentang prinsip pemasaran dan perilaku konsumen',
                    'Kreativitas dan kemampuan komunikasi yang baik',
                    'Kemampuan analisis data pemasaran'
                ],
                'skills_needed' => [
                    'Digital Marketing', 'Social Media', 'Content Creation', 'SEO', 'Market Research'
                ],
                'criteria_values' => [
                    'education' => 3,
                    'experience' => 4,
                    'technical' => 3,
                    'soft_skills' => 5
                ]
            ],
            [
                'name' => 'Financial Analyst',
                'description' => 'Menganalisis data keuangan dan memberikan rekomendasi untuk pengambilan keputusan investasi dan keuangan perusahaan.',
                'industry_type' => 'Keuangan',
                'average_salary' => 9500000,
                'requirements' => [
                    'Pendidikan minimal S1 Keuangan, Akuntansi, atau Ekonomi',
                    'Pemahaman mendalam tentang analisis keuangan dan pasar modal',
                    'Kemampuan analisis data yang kuat',
                    'Kemampuan komunikasi keuangan yang baik'
                ],
                'skills_needed' => [
                    'Financial Modeling', 'Excel Advanced', 'Valuasi', 'Accounting', 'Investment Analysis'
                ],
                'criteria_values' => [
                    'education' => 5,
                    'experience' => 4,
                    'technical' => 4,
                    'soft_skills' => 3
                ]
            ],
            [
                'name' => 'HR Specialist',
                'description' => 'Mengelola proses perekrutan, pengembangan karyawan, dan masalah kepegawaian dalam organisasi.',
                'industry_type' => 'Sumber Daya Manusia',
                'average_salary' => 7000000,
                'requirements' => [
                    'Pendidikan minimal S1 Psikologi, Manajemen SDM, atau bidang terkait',
                    'Pemahaman tentang praktik dan peraturan SDM',
                    'Kemampuan interpersonal yang kuat',
                    'Kemampuan manajemen konflik yang baik'
                ],
                'skills_needed' => [
                    'Recruitment', 'Employee Relations', 'Training & Development', 'HRIS', 'Labor Law'
                ],
                'criteria_values' => [
                    'education' => 4,
                    'experience' => 3,
                    'technical' => 2,
                    'soft_skills' => 5
                ]
            ],

            // Creative Jobs
            [
                'name' => 'Content Creator',
                'description' => 'Menciptakan konten menarik dan relevan untuk berbagai platform digital seperti blog, media sosial, dan website.',
                'industry_type' => 'Digital Media',
                'average_salary' => 6500000,
                'requirements' => [
                    'Pendidikan minimal D3/S1 Komunikasi, Jurnalistik, atau bidang terkait',
                    'Kemampuan menulis yang sangat baik',
                    'Kreativitas dan inovasi dalam pembuatan konten',
                    'Pemahaman tentang SEO dan engagement audience'
                ],
                'skills_needed' => [
                    'Copywriting', 'Storytelling', 'SEO', 'Social Media', 'Basic Design', 'Video Editing'
                ],
                'criteria_values' => [
                    'education' => 2,
                    'experience' => 3,
                    'technical' => 3,
                    'soft_skills' => 5
                ]
            ],
            [
                'name' => 'Graphic Designer',
                'description' => 'Membuat desain visual untuk berbagai media seperti iklan, website, brosur, dan lainnya.',
                'industry_type' => 'Desain',
                'average_salary' => 7000000,
                'requirements' => [
                    'Pendidikan minimal D3/S1 Desain Grafis, Seni Visual, atau bidang terkait',
                    'Portofolio desain yang kuat',
                    'Penguasaan perangkat lunak desain',
                    'Kreativitas dan pemahaman tentang elemen desain'
                ],
                'skills_needed' => [
                    'Adobe Creative Suite', 'Typography', 'Color Theory', 'Branding', 'Layout Design'
                ],
                'criteria_values' => [
                    'education' => 3,
                    'experience' => 4,
                    'technical' => 5,
                    'soft_skills' => 3
                ]
            ],

            // Education Jobs
            [
                'name' => 'Guru/Pengajar',
                'description' => 'Mendidik siswa di berbagai tingkat pendidikan dengan fokus pada pengembangan pengetahuan dan keterampilan.',
                'industry_type' => 'Pendidikan',
                'average_salary' => 5500000,
                'requirements' => [
                    'Pendidikan minimal S1 di bidang yang diajarkan atau Pendidikan',
                    'Sertifikasi guru (jika diperlukan)',
                    'Kemampuan komunikasi dan pengajaran yang baik',
                    'Kesabaran dan dedikasi untuk pendidikan'
                ],
                'skills_needed' => [
                    'Pengajaran', 'Manajemen Kelas', 'Evaluasi Pembelajaran', 'Kurikulum', 'Komunikasi'
                ],
                'criteria_values' => [
                    'education' => 5,
                    'experience' => 3,
                    'technical' => 2,
                    'soft_skills' => 5
                ]
            ],
            [
                'name' => 'Customer Service Representative',
                'description' => 'Menjadi penghubung antara perusahaan dan pelanggan, menangani pertanyaan, keluhan, dan memberikan informasi produk.',
                'industry_type' => 'Layanan Pelanggan',
                'average_salary' => 5000000,
                'requirements' => [
                    'Pendidikan minimal SMA/SMK/D3',
                    'Kemampuan komunikasi yang sangat baik',
                    'Kesabaran dan empati dalam menghadapi pelanggan',
                    'Kemampuan menyelesaikan masalah dengan cepat'
                ],
                'skills_needed' => [
                    'Komunikasi', 'Problem Solving', 'Product Knowledge', 'CRM Software', 'Multitasking'
                ],
                'criteria_values' => [
                    'education' => 2,
                    'experience' => 2,
                    'technical' => 2,
                    'soft_skills' => 5
                ]
            ],
        ];

        foreach ($jobs as $job) {
            JobRecommendation::create($job);
        }

        $this->command->info('Job recommendations seeded successfully!');
    }
}
