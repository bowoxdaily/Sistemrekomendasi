<?php

namespace Database\Seeders;

use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Illuminate\Database\Seeder;

class QuestionnaireQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questionnaire = Questionnaire::first();

        $questions = [
            // Minat dan Bakat
            [
                'question_text' => 'Seberapa tinggi minat Anda dalam menganalisis dan memecahkan masalah?',
                'question_type' => 'scale',
                'weight' => 1.5,
                'criteria_type' => 'problem_solving'
            ],
            [
                'question_text' => 'Seberapa nyaman Anda bekerja dalam tim?',
                'question_type' => 'scale',
                'weight' => 1.4,
                'criteria_type' => 'teamwork'
            ],
            [
                'question_text' => 'Seberapa baik kemampuan komunikasi Anda?',
                'question_type' => 'scale',
                'weight' => 1.3,
                'criteria_type' => 'communication'
            ],
            [
                'question_text' => 'Seberapa tinggi kreativitas Anda dalam menyelesaikan tugas?',
                'question_type' => 'scale',
                'weight' => 1.3,
                'criteria_type' => 'creativity'
            ],
            [
                'question_text' => 'Seberapa baik Anda dalam mengorganisir dan mengelola waktu?',
                'question_type' => 'scale',
                'weight' => 1.2,
                'criteria_type' => 'organization'
            ],

            // Preferensi Kerja
            [
                'question_text' => 'Bidang pekerjaan yang paling Anda minati:',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Bekerja dengan angka dan analisis', 'value' => 5],
                    ['text' => 'Bekerja dengan orang dan komunikasi', 'value' => 4],
                    ['text' => 'Bekerja dengan kreativitas dan desain', 'value' => 3],
                    ['text' => 'Bekerja dengan sistem dan organisasi', 'value' => 2],
                    ['text' => 'Bekerja dengan penjualan dan persuasi', 'value' => 1],
                ],
                'weight' => 1.5,
                'criteria_type' => 'work_preference'
            ],
            [
                'question_text' => 'Lingkungan kerja yang Anda sukai:',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Kantor dengan suasana dinamis', 'value' => 5],
                    ['text' => 'Lingkungan yang terstruktur', 'value' => 4],
                    ['text' => 'Lapangan atau mobilitas tinggi', 'value' => 3],
                    ['text' => 'Kombinasi kantor dan lapangan', 'value' => 2],
                    ['text' => 'Tempat yang tenang dan fokus', 'value' => 1],
                ],
                'weight' => 1.3,
                'criteria_type' => 'work_environment'
            ],
            [
                'question_text' => 'Aspek pekerjaan yang paling penting bagi Anda:',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Gaji dan tunjangan', 'value' => 5],
                    ['text' => 'Kesempatan pengembangan karir', 'value' => 4],
                    ['text' => 'Work-life balance', 'value' => 3],
                    ['text' => 'Stabilitas pekerjaan', 'value' => 2],
                    ['text' => 'Lingkungan kerja yang nyaman', 'value' => 1],
                ],
                'weight' => 1.4,
                'criteria_type' => 'job_priority'
            ],
            [
                'question_text' => 'Tantangan yang ingin Anda hadapi dalam pekerjaan:',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Mencapai target dan deadline', 'value' => 5],
                    ['text' => 'Mengelola tim dan proyek', 'value' => 4],
                    ['text' => 'Belajar hal baru terus-menerus', 'value' => 3],
                    ['text' => 'Memecahkan masalah kompleks', 'value' => 2],
                    ['text' => 'Menghadapi situasi yang dinamis', 'value' => 1],
                ],
                'weight' => 1.2,
                'criteria_type' => 'challenges'
            ],

            // Pertanyaan terkait Education
            [
                'question_text' => 'Apa tingkat pendidikan terakhir Anda?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'S2/S3', 'value' => 5],
                    ['text' => 'S1/D4', 'value' => 4],
                    ['text' => 'D3', 'value' => 3],
                    ['text' => 'SMA/SMK', 'value' => 2],
                    ['text' => 'Lainnya', 'value' => 1],
                ],
                'weight' => 2.0,
                'criteria_type' => 'education'
            ],
            [
                'question_text' => 'Bagaimana prestasi akademik Anda?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'IPK > 3.5', 'value' => 5],
                    ['text' => 'IPK 3.0 - 3.5', 'value' => 4],
                    ['text' => 'IPK 2.5 - 3.0', 'value' => 3],
                    ['text' => 'IPK < 2.5', 'value' => 2],
                ],
                'weight' => 1.5,
                'criteria_type' => 'education'
            ],

            // Pertanyaan terkait Experience
            [
                'question_text' => 'Berapa lama pengalaman kerja/magang Anda?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => '> 2 tahun', 'value' => 5],
                    ['text' => '1-2 tahun', 'value' => 4],
                    ['text' => '6-12 bulan', 'value' => 3],
                    ['text' => '< 6 bulan', 'value' => 2],
                    ['text' => 'Belum pernah', 'value' => 1],
                ],
                'weight' => 2.0,
                'criteria_type' => 'experience'
            ],
            [
                'question_text' => 'Berapa banyak proyek yang pernah Anda kerjakan?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => '> 5 proyek', 'value' => 5],
                    ['text' => '3-5 proyek', 'value' => 4],
                    ['text' => '1-2 proyek', 'value' => 3],
                    ['text' => 'Belum pernah', 'value' => 1],
                ],
                'weight' => 1.5,
                'criteria_type' => 'experience'
            ],

            // Pertanyaan terkait Technical
            [
                'question_text' => 'Bagaimana tingkat kemampuan teknis Anda?',
                'question_type' => 'scale',
                'weight' => 2.0,
                'criteria_type' => 'technical'
            ],
            [
                'question_text' => 'Berapa banyak tools/teknologi yang Anda kuasai?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => '> 5 tools', 'value' => 5],
                    ['text' => '3-5 tools', 'value' => 4],
                    ['text' => '1-2 tools', 'value' => 3],
                    ['text' => 'Masih belajar', 'value' => 2],
                ],
                'weight' => 1.5,
                'criteria_type' => 'technical'
            ]
        ];

        foreach ($questions as $question) {
            $question['questionnaire_id'] = $questionnaire->id;
            QuestionnaireQuestion::create($question);
        }
    }
}
