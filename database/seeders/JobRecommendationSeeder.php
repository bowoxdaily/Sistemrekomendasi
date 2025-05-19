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
                'description' => 'Mengembangkan aplikasi dan sistem perangkat lunak.',
                'requirements' => [
                    'Gelar dalam Ilmu Komputer atau bidang terkait',
                    'Kemampuan pemrograman yang kuat',
                    'Pemahaman konsep pengembangan software'
                ],
                'skills_needed' => [
                    'Java',
                    'Python',
                    'JavaScript',
                    'Git',
                    'Database'
                ],
                'average_salary' => 12000000,
                'industry_type' => 'Teknologi Informasi',
                'criteria_values' => [
                    'education' => 5,      // Sangat penting
                    'experience' => 4,      // Penting
                    'technical' => 5        // Sangat penting
                ],

            ],
            [
                'name' => 'Data Analyst',
                'description' => 'Menganalisis data untuk insight bisnis.',
                'requirements' => [
                    'Gelar dalam Statistik atau Matematika',
                    'Kemampuan analisis data yang kuat'
                ],
                'skills_needed' => [
                    'SQL',
                    'Python',
                    'R',
                    'Excel',
                    'Statistik'
                ],
                'average_salary' => 10000000,
                'industry_type' => 'Teknologi Informasi',
                'criteria_values' => [
                    'education' => 5,
                    'experience' => 3,
                    'technical' => 4
                ],

            ],
            // Tambahkan pekerjaan lain dengan format yang sama
        ];

        foreach ($jobs as $job) {
            JobRecommendation::create($job);
        }
    }
}
