<?php

namespace Database\Seeders;

use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionnaireQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questionnaire = Questionnaire::first();

        $questions = [
            // Pertanyaan Skala (1-5)
            [
                'question_text' => 'Seberapa tertarik Anda dengan pemrograman komputer?',
                'question_type' => 'scale',
                'weight' => 1.5,
                'criteria_type' => 'benefit',
            ],
            [
                'question_text' => 'Seberapa nyaman Anda bekerja dalam tim?',
                'question_type' => 'scale',
                'weight' => 1.2,
                'criteria_type' => 'benefit',
            ],
            [
                'question_text' => 'Seberapa baik kemampuan analisis data Anda?',
                'question_type' => 'scale',
                'weight' => 1.3,
                'criteria_type' => 'benefit',
            ],
            [
                'question_text' => 'Seberapa baik kemampuan komunikasi verbal Anda?',
                'question_type' => 'scale',
                'weight' => 1.4,
                'criteria_type' => 'benefit',
            ],
            [
                'question_text' => 'Seberapa baik Anda dalam mengelola waktu?',
                'question_type' => 'scale',
                'weight' => 1.2,
                'criteria_type' => 'benefit',
            ],

            // Pertanyaan Pilihan Ganda
            [
                'question_text' => 'Bidang pekerjaan yang paling Anda minati:',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Teknologi Informasi', 'value' => 5],
                    ['text' => 'Keuangan & Akuntansi', 'value' => 4],
                    ['text' => 'Marketing & Komunikasi', 'value' => 3],
                    ['text' => 'Desain & Kreatif', 'value' => 2],
                    ['text' => 'Manajemen & Administrasi', 'value' => 1],
                ],
                'weight' => 0.3,
                'criteria_type' => 'benefit',
            ],
            [
                'question_text' => 'Lingkungan kerja yang Anda sukai:',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Startup yang dinamis', 'value' => 5],
                    ['text' => 'Perusahaan besar yang stabil', 'value' => 4],
                    ['text' => 'Bekerja secara remote/WFH', 'value' => 3],
                    ['text' => 'Kantor dengan jam kerja fleksibel', 'value' => 2],
                    ['text' => 'Perusahaan dengan struktur tradisional', 'value' => 1],
                ],
                'weight' => 1.3,
                'criteria_type' => 'benefit',
            ],
            [
                'question_text' => 'Preferensi gaya bekerja Anda:',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Bekerja mandiri', 'value' => 5],
                    ['text' => 'Kolaborasi tim', 'value' => 4],
                    ['text' => 'Kombinasi keduanya', 'value' => 3],
                    ['text' => 'Dibimbing mentor', 'value' => 2],
                    ['text' => 'Mengikuti prosedur yang jelas', 'value' => 1],
                ],
                'weight' => 1.2,
                'criteria_type' => 'benefit',
            ],
        ];

        foreach ($questions as $question) {
            $question['questionnaire_id'] = $questionnaire->id;
            QuestionnaireQuestion::create($question);
        }
    }
}
