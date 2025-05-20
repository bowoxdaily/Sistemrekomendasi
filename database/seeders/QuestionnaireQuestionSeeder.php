<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Facades\DB;

class QuestionnaireQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // First, make sure we have at least one active questionnaire
        $questionnaire = Questionnaire::where('is_active', true)->first();
        
        if (!$questionnaire) {
            $questionnaire = Questionnaire::create([
                'title' => 'Kuesioner Rekomendasi Karir',
                'description' => 'Kuesioner ini dirancang untuk membantu menentukan karir yang sesuai dengan minat, kemampuan, dan latar belakang pendidikan Anda.',
                'is_active' => true,
                'created_by' => 1, // Assuming admin user ID is 1
            ]);
        }

        // Clean existing questions for consistency during development
        QuestionnaireQuestion::where('questionnaire_id', $questionnaire->id)->delete();

        // Education Questions
        $this->seedEducationQuestions($questionnaire->id);
        
        // Experience Questions
        $this->seedExperienceQuestions($questionnaire->id);
        
        // Technical Questions
        $this->seedTechnicalSkillsQuestions($questionnaire->id);
        
        // Soft Skills Questions
        $this->seedSoftSkillsQuestions($questionnaire->id);

        $this->command->info('Questionnaire questions seeded successfully!');
    }

    private function seedEducationQuestions($questionnaireId)
    {
        $questions = [
            [
                'question_text' => 'Apa tingkat pendidikan terakhir atau yang sedang Anda tempuh?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'SMA/SMK', 'value' => 1],
                    ['text' => 'D3/Diploma', 'value' => 2],
                    ['text' => 'S1/Sarjana', 'value' => 3],
                    ['text' => 'S2/Master', 'value' => 4],
                    ['text' => 'S3/Doktor', 'value' => 5]
                ],
                'weight' => 5,
                'criteria_type' => 'education'
            ],
            [
                'question_text' => 'Bagaimana nilai rata-rata akademik Anda?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Di bawah 2.5', 'value' => 1],
                    ['text' => '2.5 - 2.99', 'value' => 2],
                    ['text' => '3.0 - 3.49', 'value' => 3],
                    ['text' => '3.5 - 3.79', 'value' => 4],
                    ['text' => '3.8 - 4.0', 'value' => 5]
                ],
                'weight' => 4,
                'criteria_type' => 'education'
            ],
            [
                'question_text' => 'Seberapa sering Anda mengikuti pelatihan atau kursus untuk meningkatkan keterampilan Anda?',
                'question_type' => 'scale',
                'options' => [],
                'weight' => 3,
                'criteria_type' => 'education'
            ],
            [
                'question_text' => 'Bagaimana tingkat kesesuaian jurusan pendidikan Anda dengan bidang pekerjaan yang Anda minati?',
                'question_type' => 'scale',
                'options' => [],
                'weight' => 4,
                'criteria_type' => 'education'
            ],
        ];

        $this->createQuestions($questions, $questionnaireId);
    }

    private function seedExperienceQuestions($questionnaireId)
    {
        $questions = [
            [
                'question_text' => 'Berapa lama pengalaman yang Anda miliki di bidang yang diminati?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Belum pernah', 'value' => 1],
                    ['text' => 'Kurang dari 1 tahun', 'value' => 2],
                    ['text' => '1-2 tahun', 'value' => 3],
                    ['text' => '2-5 tahun', 'value' => 4],
                    ['text' => 'Lebih dari 5 tahun', 'value' => 5]
                ],
                'weight' => 5,
                'criteria_type' => 'experience'
            ],
            [
                'question_text' => 'Berapa banyak proyek yang telah Anda kerjakan yang relevan dengan bidang ini?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Belum pernah', 'value' => 1],
                    ['text' => '1-2 proyek', 'value' => 2],
                    ['text' => '3-5 proyek', 'value' => 3],
                    ['text' => '6-10 proyek', 'value' => 4],
                    ['text' => 'Lebih dari 10 proyek', 'value' => 5]
                ],
                'weight' => 4,
                'criteria_type' => 'experience'
            ],
            [
                'question_text' => 'Seberapa berpengalaman Anda dalam bekerja di lingkungan tim?',
                'question_type' => 'scale',
                'options' => [],
                'weight' => 3,
                'criteria_type' => 'experience'
            ],
            [
                'question_text' => 'Seberapa sering Anda mengambil posisi kepemimpinan dalam proyek?',
                'question_type' => 'scale',
                'options' => [],
                'weight' => 3,
                'criteria_type' => 'experience'
            ],
        ];

        $this->createQuestions($questions, $questionnaireId);
    }

    private function seedTechnicalSkillsQuestions($questionnaireId)
    {
        $questions = [
            [
                'question_text' => 'Bagaimana kemampuan Anda dalam menggunakan komputer dan aplikasi umum (MS Office, dll)?',
                'question_type' => 'scale',
                'options' => [],
                'weight' => 3,
                'criteria_type' => 'technical'
            ],
            [
                'question_text' => 'Seberapa mahir Anda dalam bahasa pemrograman (jika relevan dengan bidang Anda)?',
                'question_type' => 'scale',
                'options' => [],
                'weight' => 4,
                'criteria_type' => 'technical'
            ],
            [
                'question_text' => 'Seberapa mahir Anda dalam menggunakan alat/software khusus di bidang Anda?',
                'question_type' => 'scale',
                'options' => [],
                'weight' => 4,
                'criteria_type' => 'technical'
            ],
            [
                'question_text' => 'Bagaimana kemampuan analisis data Anda?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Sangat dasar/belum pernah', 'value' => 1],
                    ['text' => 'Dasar', 'value' => 2],
                    ['text' => 'Menengah', 'value' => 3],
                    ['text' => 'Mahir', 'value' => 4],
                    ['text' => 'Sangat mahir/expert', 'value' => 5]
                ],
                'weight' => 4,
                'criteria_type' => 'technical'
            ],
        ];

        $this->createQuestions($questions, $questionnaireId);
    }

    private function seedSoftSkillsQuestions($questionnaireId)
    {
        $questions = [
            [
                'question_text' => 'Bagaimana kemampuan komunikasi Anda?',
                'question_type' => 'scale',
                'options' => [],
                'weight' => 3,
                'criteria_type' => 'soft_skills'
            ],
            [
                'question_text' => 'Seberapa baik Anda bekerja di bawah tekanan?',
                'question_type' => 'scale',
                'options' => [],
                'weight' => 3,
                'criteria_type' => 'soft_skills'
            ],
            [
                'question_text' => 'Bagaimana kemampuan adaptasi Anda terhadap perubahan?',
                'question_type' => 'scale',
                'options' => [],
                'weight' => 3,
                'criteria_type' => 'soft_skills'
            ],
            [
                'question_text' => 'Bagaimana tingkat kemampuan kerja tim Anda?',
                'question_type' => 'multiple_choice',
                'options' => [
                    ['text' => 'Lebih suka bekerja sendiri', 'value' => 1],
                    ['text' => 'Sesekali bisa bekerja dalam tim', 'value' => 2],
                    ['text' => 'Nyaman bekerja dalam tim', 'value' => 3],
                    ['text' => 'Sangat baik dalam tim', 'value' => 4],
                    ['text' => 'Dapat memimpin tim dengan baik', 'value' => 5]
                ],
                'weight' => 4,
                'criteria_type' => 'soft_skills'
            ],
        ];

        $this->createQuestions($questions, $questionnaireId);
    }
    
    private function createQuestions($questions, $questionnaireId)
    {
        foreach ($questions as $questionData) {
            QuestionnaireQuestion::create([
                'questionnaire_id' => $questionnaireId,
                'question_text' => $questionData['question_text'],
                'question_type' => $questionData['question_type'],
                'options' => $questionData['options'],
                'weight' => $questionData['weight'],
                'criteria_type' => $questionData['criteria_type'],
            ]);
        }
    }
}
