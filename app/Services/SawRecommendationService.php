<?php

namespace App\Services;

use App\Models\QuestionnaireResponse;
use App\Models\JobRecommendation;

class SawRecommendationService
{
    public function calculateRecommendations(QuestionnaireResponse $response)
    {
        // Ambil jawaban kuesioner dan kelompokkan berdasarkan kriteria
        $answers = $response->answers()->with(['question' => function ($q) {
            $q->select('id', 'question_text', 'question_type', 'criteria_type', 'weight');
        }])->get();

        $groupedAnswers = $answers->groupBy('question.criteria_type');

        // Ambil semua data pekerjaan
        $jobs = JobRecommendation::all();

        if ($jobs->isEmpty()) {
            return [];
        }

        // Ambil semua kriteria unik dari pekerjaan
        $allCriteria = [];
        foreach ($jobs as $job) {
            foreach ($job->criteria_values as $criteria => $value) {
                if (!in_array($criteria, $allCriteria)) {
                    $allCriteria[] = $criteria;
                }
            }
        }

        // Hitung skor rata-rata untuk setiap kriteria
        $studentScores = [];
        foreach ($allCriteria as $criteriaType) {
            $criteriaAnswers = $groupedAnswers->get($criteriaType, collect());
            if ($criteriaAnswers->isEmpty()) {
                $studentScores[$criteriaType] = 0;
                continue;
            }

            $totalWeightedScore = 0;
            $totalWeight = 0;

            foreach ($criteriaAnswers as $answer) {
                $weight = $answer->question->weight;
                $totalWeightedScore += $answer->answer_value * $weight;
                $totalWeight += $weight;
            }

            $studentScores[$criteriaType] = $totalWeight > 0 ?
                ($totalWeightedScore / $totalWeight) : 0;
        }

        // Hitung skor untuk setiap pekerjaan
        $jobScores = [];
        foreach ($jobs as $job) {
            $totalScore = 0;
            $criteriaDetails = [];
            $totalWeight = array_sum($job->criteria_weights ?? array_fill_keys($allCriteria, 1));

            foreach ($job->criteria_values as $criteria => $value) {
                $studentValue = $studentScores[$criteria] ?? 0;
                $weight = ($job->criteria_weights[$criteria] ?? 1) / $totalWeight;

                // Normalisasi dan perhitungan similarity
                $normalizedStudentValue = $studentValue / 5;
                $normalizedJobValue = $value / 5;
                $similarity = 1 - abs($normalizedJobValue - $normalizedStudentValue);
                $weightedScore = $similarity * $weight;

                $criteriaDetails[$criteria] = [
                    'student_value' => $studentValue,
                    'job_value' => $value,
                    'normalized_student' => round($normalizedStudentValue, 4),
                    'normalized_job' => round($normalizedJobValue, 4),
                    'similarity' => round($similarity, 4),
                    'weighted_score' => round($weightedScore, 4),
                    'weight' => $weight
                ];

                $totalScore += $weightedScore;
            }

            // Konversi ke persentase dan bulatkan ke 1 desimal
            $matchPercentage = round($totalScore * 100, 1);

            // Pastikan tidak melebihi 100%
            $matchPercentage = min(100, max(0, $matchPercentage));

            $jobScores[] = [
                'job_id' => $job->id,
                'name' => $job->name,
                'match_percentage' => $matchPercentage,
                'salary' => $job->average_salary,
                'description' => $job->description,
                'industry_type' => $job->industry_type,
                'skills_needed' => $job->skills_needed,
                'requirements' => $job->requirements,
                'criteria_detail' => $criteriaDetails
            ];
        }

        // Urutkan berdasarkan persentase kecocokan
        usort($jobScores, function ($a, $b) {
            if ($b['match_percentage'] == $a['match_percentage']) {
                // Jika persentase sama, urutkan berdasarkan gaji
                return $b['salary'] - $a['salary'];
            }
            return $b['match_percentage'] <=> $a['match_percentage'];
        });

        // Ambil 3 rekomendasi teratas dengan minimal 50% kecocokan
        $recommendations = array_filter($jobScores, function ($job) {
            return $job['match_percentage'] >= 50;
        });

        return array_slice($recommendations, 0, 3);
    }
}
