<?php

namespace Database\Seeders;

use App\Models\JobRecommendation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobRecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = [
            [
                'name' => 'Software Developer',
                'description' => 'Mengembangkan aplikasi dan sistem perangkat lunak dengan fokus pada pemecahan masalah dan pengembangan solusi teknologi.',
                'requirements' => [
                    'Gelar dalam Ilmu Komputer atau bidang terkait',
                    'Kemampuan pemrograman yang kuat',
                    'Pemahaman konsep pengembangan software',
                    'Kemampuan debugging dan problem-solving'
                ],
                'skills_needed' => [
                    'Java',
                    'Python',
                    'JavaScript',
                    'Git',
                    'Database Management',
                    'Algoritma dan Struktur Data'
                ],
                'average_salary' => 12000000,
                'industry_type' => 'Teknologi Informasi',
                'criteria_values' => [
                    'programming_interest' => 5,    // Sangat tinggi
                    'teamwork' => 4,               // Tinggi
                    'data_analysis' => 4,          // Tinggi
                    'verbal_communication' => 3,    // Sedang
                    'time_management' => 4         // Tinggi
                ]
            ],
            [
                'name' => 'Data Analyst',
                'description' => 'Menganalisis data kompleks untuk memberikan insight bisnis dan membantu pengambilan keputusan.',
                'requirements' => [
                    'Gelar dalam Statistik, Matematika, atau bidang terkait',
                    'Kemampuan analisis data yang kuat',
                    'Pemahaman statistik',
                    'Kemampuan visualisasi data'
                ],
                'skills_needed' => [
                    'SQL',
                    'Python',
                    'R',
                    'Excel',
                    'Tableau',
                    'Statistical Analysis'
                ],
                'average_salary' => 10000000,
                'industry_type' => 'Teknologi Informasi',
                'criteria_values' => [
                    'programming_interest' => 3,    // Sedang
                    'teamwork' => 4,               // Tinggi
                    'data_analysis' => 5,          // Sangat tinggi
                    'verbal_communication' => 4,    // Tinggi
                    'time_management' => 4         // Tinggi
                ]
            ],
            [
                'name' => 'Project Manager',
                'description' => 'Memimpin dan mengelola proyek dari awal hingga selesai, memastikan timeline dan kualitas terjaga.',
                'requirements' => [
                    'Gelar dalam Manajemen atau bidang terkait',
                    'Sertifikasi manajemen proyek (diutamakan)',
                    'Pengalaman kepemimpinan',
                    'Kemampuan komunikasi yang kuat'
                ],
                'skills_needed' => [
                    'Project Management',
                    'Leadership',
                    'Risk Management',
                    'Stakeholder Management',
                    'Agile Methodologies'
                ],
                'average_salary' => 15000000,
                'industry_type' => 'Manajemen & Administrasi',
                'criteria_values' => [
                    'programming_interest' => 2,    // Rendah
                    'teamwork' => 5,               // Sangat tinggi
                    'data_analysis' => 3,          // Sedang
                    'verbal_communication' => 5,    // Sangat tinggi
                    'time_management' => 5         // Sangat tinggi
                ]
            ],
            [
                'name' => 'UI/UX Designer',
                'description' => 'Merancang antarmuka pengguna yang menarik dan mudah digunakan untuk aplikasi dan website.',
                'requirements' => [
                    'Gelar dalam Desain, HCI, atau bidang terkait',
                    'Portfolio desain yang kuat',
                    'Pemahaman UX principles',
                    'Kemampuan prototyping'
                ],
                'skills_needed' => [
                    'Figma',
                    'Adobe XD',
                    'User Research',
                    'Wireframing',
                    'Interaction Design'
                ],
                'average_salary' => 9000000,
                'industry_type' => 'Desain & Kreatif',
                'criteria_values' => [
                    'programming_interest' => 2,    // Rendah
                    'teamwork' => 4,               // Tinggi
                    'data_analysis' => 3,          // Sedang
                    'verbal_communication' => 4,    // Tinggi
                    'time_management' => 4         // Tinggi
                ]
            ],
            [
                'name' => 'Digital Marketing Specialist',
                'description' => 'Merencanakan dan melaksanakan strategi pemasaran digital untuk meningkatkan brand awareness dan penjualan.',
                'requirements' => [
                    'Gelar dalam Marketing atau bidang terkait',
                    'Pemahaman SEO/SEM',
                    'Pengalaman social media marketing',
                    'Kemampuan analisis metrik digital'
                ],
                'skills_needed' => [
                    'Google Analytics',
                    'Social Media Management',
                    'Content Marketing',
                    'Email Marketing',
                    'SEO/SEM'
                ],
                'average_salary' => 8000000,
                'industry_type' => 'Marketing & Komunikasi',
                'criteria_values' => [
                    'programming_interest' => 2,    // Rendah
                    'teamwork' => 4,               // Tinggi
                    'data_analysis' => 4,          // Tinggi
                    'verbal_communication' => 5,    // Sangat tinggi
                    'time_management' => 4         // Tinggi
                ]
            ]
        ];

        foreach ($jobs as $job) {
            JobRecommendation::create($job);
        }
    }
}
