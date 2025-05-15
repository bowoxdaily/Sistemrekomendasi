<?php

namespace App\Services;

use App\Models\QuestionnaireResponse;
use App\Models\JobRecommendation;
use Illuminate\Support\Facades\Log;

class SawRecommendationService
{
    /**
     * Hitung rekomendasi pekerjaan menggunakan metode SAW (Simple Additive Weighting)
     * 
     * @param QuestionnaireResponse $response
     * @return array
     */
    public function calculateRecommendations(QuestionnaireResponse $response)
    {
        // Langkah 1: Ambil data jawaban kuesioner dan bobot kriteria
        $answers = $response->answers()->with('question')->get();
        $jobs = JobRecommendation::all();

        // Jika tidak ada pekerjaan yang tersedia, return array kosong
        if ($jobs->isEmpty()) {
            return [];
        }

        // Mapping jawaban ke array untuk proses normalisasi
        $criteriaValues = [];
        $criteriaWeights = [];
        $criteriaTypes = [];

        foreach ($answers as $answer) {
            $question = $answer->question;

            // Hanya pertanyaan dengan tipe skala atau pilihan ganda yang digunakan dalam perhitungan
            if (in_array($question->question_type, ['scale', 'multiple_choice'])) {
                $criteriaId = $question->id;
                $criteriaValues[$criteriaId] = [];
                $criteriaWeights[$criteriaId] = $question->weight;
                $criteriaTypes[$criteriaId] = $question->criteria_type;

                // Nilai siswa untuk kriteria ini
                $studentValue = $answer->answer_value;

                // Masukkan nilai untuk setiap pekerjaan
                foreach ($jobs as $job) {
                    $jobCriteriaValues = $job->criteria_values ?? [];
                    $criteriaValues[$criteriaId][$job->id] = $jobCriteriaValues[$criteriaId] ?? 0;
                }

                // Masukkan nilai siswa (akan dibandingkan dengan nilai pekerjaan)
                $criteriaValues[$criteriaId]['student'] = $studentValue;
            }
        }

        // Langkah 2: Normalisasi nilai menggunakan metode SAW
        $normalizedValues = [];

        foreach ($criteriaValues as $criteriaId => $values) {
            $normalizedValues[$criteriaId] = [];

            // Tentukan nilai maksimum dan minimum untuk normalisasi
            $max = max($values);
            $min = min(array_filter($values)) ?: 1; // Hindari pembagian dengan 0

            foreach ($jobs as $job) {
                $value = $values[$job->id] ?? 0;

                // Normalisasi berdasarkan jenis kriteria (benefit atau cost)
                if ($criteriaTypes[$criteriaId] === 'benefit') {
                    // Untuk kriteria benefit, nilai lebih tinggi lebih baik: R = nilai / max
                    $normalizedValues[$criteriaId][$job->id] = $value / $max;
                } else {
                    // Untuk kriteria cost, nilai lebih rendah lebih baik: R = min / nilai
                    $normalizedValues[$criteriaId][$job->id] = $min / ($value ?: 1); // Hindari pembagian dengan 0
                }
            }
        }

        // Langkah 3: Hitung nilai preferensi untuk setiap pekerjaan
        $preferences = [];

        foreach ($jobs as $job) {
            $preferences[$job->id] = 0;

            foreach ($criteriaValues as $criteriaId => $values) {
                // Hitung nilai preferensi = jumlah (bobot * nilai normalisasi)
                if (isset($normalizedValues[$criteriaId][$job->id])) {
                    $preferences[$job->id] += $criteriaWeights[$criteriaId] * $normalizedValues[$criteriaId][$job->id];
                }
            }
        }

        // Langkah 4: Urutkan pekerjaan berdasarkan nilai preferensi (dari tertinggi ke terendah)
        arsort($preferences);

        // Langkah 5: Format hasil untuk disimpan di database
        $result = [];
        $rank = 1;

        foreach ($preferences as $jobId => $score) {
            $result[] = [
                'job_id' => $jobId,
                'score' => round($score, 4),
                'rank' => $rank++
            ];
        }

        // Batasi hasil hanya 5 rekomendasi teratas
        return array_slice($result, 0, 5);
    }
}
